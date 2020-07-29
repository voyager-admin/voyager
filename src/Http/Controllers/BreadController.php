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

        extract($request->only(['page', 'perpage', 'global', 'filters', 'order', 'direction', 'softdeleted', 'locale']));

        $query = $bread->getModel()->select('*');
        if (!empty($layout->scope)) {
            $query = $bread->getModel()->{$layout->scope}()->select('*');
        }

        // Soft-deletes
        $query = $this->loadSoftDeletesQuery($bread, $layout, $softdeleted, $query);

        $total = $query->count();

        // Global search ($global)
        $query = $this->globalSearchQuery($global, $layout, $locale, $query);

        // Column search ($filters)
        $query = $this->columnSearchQuery($filters, $layout, $query, $locale);

        // Ordering ($order and $direction)
        $query = $this->orderQuery($layout, $direction, $order, $query, $locale);

        $query = $query->get();
        $filtered = $query->count();

        // Pagination ($page and $perpage)
        $query = $query->slice(($page - 1) * $perpage)->take($perpage);

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
            'primary'           => $query->get(0) ? $query->get(0)->getKeyName() : 'id',
            'translatable'      => $layout->hasTranslatableFormfields(),
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
        $validation_errors = $this->validateData($layout->formfields, $data);
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

        $layout->formfields->each(function ($formfield) use (&$data) {
            $value = $data->{$formfield->column->column};
            $data->{$formfield->column->column} = $formfield->read($value);
        });

        return view('voyager::bread.read', compact('bread', 'data', 'layout'));
    }

    public function edit(Request $request, $id)
    {
        $bread = $this->getBread($request);
        $layout = $this->getLayoutForAction($bread, 'add');
        $new = false;

        $data = $bread->getModel()->findOrFail($id);

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
        $layout = $this->getLayoutForAction($bread, 'add');

        $model = $bread->getModel()->findOrFail($id);
        $data = $request->get('data', []);

        if ($bread->usesTranslatableTrait()) {
            $model->dontTranslate();
        }

        // Validate Data
        $validation_errors = $this->validateData($layout->formfields, $data);
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
        if ($bread->usesSoftDeletes()) {
            $model = $model->withTrashed();
        }

        $deleted = 0;

        $force = $request->get('force', 'false');
        if ($request->has('ids')) {
            $ids = $request->get('ids');
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            $model->find($ids)->each(function ($entry) use ($bread, $force, &$deleted) {
                if ($force == 'true' && $bread->usesSoftDeletes()) {
                    // TODO: Check if layout allows usage of soft-deletes
                    if ($entry->trashed()) {
                        $this->authorize('force-delete', $entry);
                        $entry->forceDelete();
                        $deleted++;
                    }
                } else {
                    $this->authorize('delete', $entry);
                    $entry->delete();
                    $deleted++;
                }
            });
        }

        return $deleted;
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

        if ($request->has('ids')) {
            $ids = $request->get('ids');
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

        return $restored;
    }

    public function relationship(Request $request)
    {
        // TODO: Validate that the method exists in edit/add layout
        $bread = $this->getBread($request);
        $data = [];
        $perpage = 3;

        $query = $request->get('query', null);
        $page = $request->get('page', 1);
        $method = $request->get('method');
        $column = $request->get('column');
        $locale = VoyagerFacade::getLocale();
        $translatable = false;
        $model = $bread->getModel();
        $data = $model->{$method}()->getRelated();

        if (in_array(Translatable::class, class_uses($data)) && in_array($column, $data->translatable)) {
            $translatable = true;
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
