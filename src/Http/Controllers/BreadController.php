<?php

namespace Voyager\Admin\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Traits\Bread\Browsable;
use Voyager\Admin\Traits\Bread\Saveable;
use Voyager\Admin\Traits\Translatable;

class BreadController extends Controller
{
    use Browsable, Saveable;

    public $uses_soft_deletes = false;
    protected $breadmanager;

    public function __construct(BreadManager $breadmanager, PluginManager $pluginsmanager)
    {
        $this->breadmanager = $breadmanager;
        parent::__construct($pluginsmanager);
    }

    public function data(Request $request)
    {
        $start = microtime(true);
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'browse');

        extract($request->only(['page', 'perpage', 'global', 'filters', 'order', 'direction', 'softdeleted', 'locale', 'filter']));

        $query = $bread->getModel();
        if (!empty($layout->options->scope)) {
            $query = $query->{$layout->options->scope}();
        }

        // Soft-deletes
        $query = $this->loadSoftDeletesQuery($bread, $layout, $softdeleted, $query);

        $total = $query->count();

        // Custom filter
        $query = $this->applyCustomFilter($bread, $layout, $filter, $query);

        // Global search ($global)
        $query = $this->globalSearchQuery($global, $layout, $locale, $query);

        // Column search ($filters)
        $query = $this->columnSearchQuery($filters, $layout, $query, $locale);

        // Ordering ($order and $direction)
        $query = $this->orderQuery($layout, $direction, $order, $query, $locale);

        $filtered = $query->count();

        // Pagination ($page and $perpage)
        $query = $query->skip(($page - 1) * $perpage)->take($perpage)->get();

        // Load accessors
        $accessors = $layout->getFormfieldsByColumnType('computed')->pluck('column.column')->toArray();
        $query = $query->each(function ($item) use ($accessors) {
            $item->append($accessors);
        });

        // Transform results
        $query = $this->transformResults($layout, $bread->usesTranslatableTrait(), $query);

        return [
            'results'           => $query->values(),
            'filtered'          => $filtered,
            'total'             => $total,
            'layout'            => $layout,
            'execution'         => number_format(((microtime(true) - $start) * 1000), 0, '.', ''),
            'uses_soft_deletes' => $this->uses_soft_deletes,
            'actions'           => $this->breadmanager->getActionsForBread($bread),
        ];
    }

    public function add(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'add');
        $new = true;
        $data = collect();

        $instance = new $bread->model();
        $reflection = $this->breadmanager->getModelReflectionClass($bread->model);
        $relationships = $this->breadmanager->getModelRelationships($reflection, $instance, true)->values();

        $layout->formfields->each(function ($formfield) use (&$data) {
            $data->put($formfield->column->column, $formfield->add());
        });

        if ($request->has('from_relationship')) {
            return compact('bread', 'layout', 'new', 'data', 'relationships');
        }

        return view('voyager::bread.edit-add', compact('bread', 'layout', 'new', 'data', 'relationships'));
    }

    public function store(Request $request)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'add');

        $model = new $bread->model();
        $data = $request->get('data', []);

        if ($bread->usesTranslatableTrait()) {
            $model->dontTranslate();
        }

        // Validate Data
        $validate_all_locales = $layout->options->validate_locales == 'all' ?? false;
        $validation_errors = $this->validateData($layout->formfields, $data, $validate_all_locales);
        if (count($validation_errors) > 0) {
            return response()->json($validation_errors, 422);
        }

        $model = $this->updateStoreData($layout->formfields, $data, $model, false);

        if ($model->save()) {
            $layout->formfields->each(function ($formfield) use ($data, $model) {
                $formfield->stored($model, $data[$formfield->column->column]);
            });

            return response($model->getKey(), 200);
        } else {
            return response($model->getKey(), 500);
        }
    }

    public function read(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'read');
        $data = $bread->getModel()->findOrFail($id);
        if (!empty($layout->options->scope)) {
            $data = $bread->getModel()->{$layout->options->scope}()->findOrFail($id);
        }

        $layout->formfields->each(function ($formfield) use (&$data) {
            $value = $data->{$formfield->column->column};
            $data->{$formfield->column->column} = $formfield->read($value);
        });

        return view('voyager::bread.read', compact('bread', 'data', 'layout'));
    }

    public function edit(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'edit');
        $new = false;

        $data = $bread->getModel()->findOrFail($id);

        if (!empty($layout->options->scope)) {
            $data = $bread->getModel()->{$layout->options->scope}()->findOrFail($id);
        }

        if ($bread->usesTranslatableTrait()) {
            $data->dontTranslate();
        }

        $reflection = $this->breadmanager->getModelReflectionClass($bread->model);
        $relationships = $this->breadmanager->getModelRelationships($reflection, $data, true)->values();

        $breadData = (object) json_decode($data->toJson());

        $layout->formfields->each(function ($formfield) use (&$data, &$breadData) {
            $value = $data->{$formfield->column->column};

            if ($formfield->translatable ?? false) {
                $translations = [];
                $value = json_decode($value) ?? [];
                foreach ($value as $locale => $translated) {
                    $translations[$locale] = $formfield->edit($translated);
                }
                $breadData->{$formfield->column->column} = json_encode($translations);
            } else {
                $breadData->{$formfield->column->column} = $formfield->edit($value);
            }
        });

        $data = $breadData;

        return view('voyager::bread.edit-add', compact('bread', 'layout', 'new', 'data', 'relationships'));
    }

    public function update(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'edit');

        $model = $bread->getModel()->findOrFail($id);
        if (!empty($layout->options->scope)) {
            $model = $bread->getModel()->{$layout->options->scope}()->findOrFail($id);
        }
        $data = $request->get('data', []);

        if ($bread->usesTranslatableTrait()) {
            $model->dontTranslate();
        }

        // Validate Data
        $validate_all_locales = $layout->options->validate_locales == 'all' ?? false;
        $validation_errors = $this->validateData($layout->formfields, $data, $validate_all_locales);
        if (count($validation_errors) > 0) {
            return response()->json($validation_errors, 422);
        }
        $model = $this->updateStoreData($layout->formfields, $data, $model);

        if ($model->save()) {
            return response($model->getKey(), 200);
        } else {
            return response($model->getKey(), 500);
        }
    }

    public function delete(Request $request)
    {
        $bread = $this->getBread($request);
        $model = $bread->getModel();

        $deleted = 0;

        if ($request->has('primary')) {
            $ids = $request->get('primary');
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            $model->find($ids)->each(function ($entry) use ($bread, &$deleted) {
                $this->authorize('delete', $entry);
                $entry->delete();
                $deleted++;
            });
        }

        return [
            'amount' => $deleted,
        ];
    }

    public function restore(Request $request)
    {
        // TODO: Check if layout allows usage of soft-deletes
        $bread = $this->getBread($request);
        if (!$bread->usesSoftDeletes()) {
            return;
        }

        $restored = 0;

        $model = $bread->getModel()->withTrashed();

        if ($request->has('primary')) {
            $ids = $request->get('primary');
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $model->find($ids)->each(function ($entry) use ($bread, &$restored) {
                if ($entry->trashed()) {
                    $this->authorize('restore', $entry);
                    $entry->restore();
                    $restored++;
                }
            });
        }

        return [
            'amount' => $restored,
        ];
    }

    public function relationship(Request $request)
    {
        // TODO: Validate that the method exists in edit/add layout
        $bread = $this->getBread($request);
        $data = [];
        $perpage = 5;

        $query = $request->get('query', null);
        $page = $request->get('page', 1);
        $method = $request->get('method');
        $column = $request->get('column');
        $scope = $request->get('scope', null);
        $locale = VoyagerFacade::getLocale();
        $translatable = false;
        $model = $bread->getModel();
        $data = $model->{$method}()->getRelated();

        if (in_array(Translatable::class, class_uses($data)) && in_array($column, $data->translatable)) {
            $translatable = true;
        }

        if (!empty($scope)) {
            // TODO: Get scope from layout instead of request
            $data = $data->{$scope}();
        }

        if (!empty($query)) {
            if ($translatable) {
                $data = $data->where(DB::raw('lower('.$column.'->"$.'.$locale.'")'), 'LIKE', '%'.strtolower($query).'%');
            } else {
                $data = $data->where(DB::raw('lower('.$column.')'), 'LIKE', '%'.strtolower($query).'%');
            }
        }
        $data = $data->get();
        $count = $data->count();

        $data = $data->slice(($page - 1) * $perpage)->take($perpage);

        $data->transform(function ($item) use ($column) {
            return [
                'key'   => $item->getKey(),
                'value' => $item->{$column},
            ];
        });

        return [
            'pages' => ceil($count / $perpage),
            'data'  => $data->values(),
        ];
    }
}
