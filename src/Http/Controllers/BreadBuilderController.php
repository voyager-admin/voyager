<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Voyager\Admin\Events\Builder\BackedUp as BackedUpEvent;
use Voyager\Admin\Events\Builder\Created as CreatedEvent;
use Voyager\Admin\Events\Builder\Deleted as DeletedEvent;
use Voyager\Admin\Events\Builder\Restored as RestoredEvent;
use Voyager\Admin\Events\Builder\Updated as UpdatedEvent;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Rules\ClassExists as ClassExistsRule;
use Voyager\Admin\Rules\DefaultLocale as DefaultLocaleRule;

class BreadBuilderController extends Controller
{
    protected $breadmanager;

    public function __construct(BreadManager $breadmanager)
    {
        $this->breadmanager = $breadmanager;
    }

    /**
     * Display all BREADs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = VoyagerFacade::getTables();

        return view('voyager::builder.index', compact('tables'));
    }

    /**
     * Create a BREAD for a given $table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\Response
     */
    public function create($table)
    {
        if (!in_array($table, VoyagerFacade::getTables())) {
            throw new \Voyager\Admin\Exceptions\TableNotFoundException('Table "'.$table.'" does not exist');
        }

        if ($this->breadmanager->getBread($table)) {
            VoyagerFacade::flashMessage(
                __('voyager::builder.bread_already_exists', ['table' => $table]),
                'yellow',
                5000,
                true
            );

            return redirect()->route('voyager.bread.edit', $table);
        }

        $bread = $this->breadmanager->createBread($table);
        $new = true;

        return view('voyager::builder.edit-add', compact('bread', 'new'));
    }

    /**
     * Edit a BREAD for a given $table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($table)
    {
        $bread = $this->breadmanager->getBread($table);
        if (!$bread) {
            VoyagerFacade::flashMessage(__('voyager::builder.bread_does_no_exist', [
                'table' => $table
            ]), 'red', 5000, true);

            return redirect()->route('voyager.bread.create', $table);
        }

        $new = false;

        return view('voyager::builder.edit-add', compact('bread', 'new'));
    }

    /**
     * Update a BREAD.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $table
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $table)
    {
        $bread = $request->bread;

        if (!is_array($bread)) {
            return response()->json([__('voyager::bread.json_data_not_valid')], 422);
        }

        $bread = (object) $bread;

        $bread->table = $table;

        $validator = Validator::make($request->get('bread'), [
            'slug'          => ['required', new DefaultLocaleRule()],
            'name_singular' => ['required', new DefaultLocaleRule()],
            'name_plural'   => ['required', new DefaultLocaleRule()],
            'model'         => ['nullable', new ClassExistsRule()],
            'controller'    => ['nullable', new ClassExistsRule()],
            'policy'        => ['nullable', new ClassExistsRule()],
        ]);

        if ($validator->passes()) {
            $bread = $this->breadmanager->getBreadByTable($table);
            if (!$this->breadmanager->storeBread($bread)) {
                $validator->errors()->add('bread', __('voyager::builder.bread_save_failed'));

                return response()->json($validator->errors(), 422);
            }
            if ($bread) {
                event(new UpdatedEvent($this->breadmanager->getBreadByTable($table)));
            } else {
                event(new CreatedEvent($this->breadmanager->getBreadByTable($table)));
            }
        } else {
            return response()->json($validator->errors(), 422);
        }

        return response()->json($validator->messages(), 200);
    }

    /**
     * Remove a BREAD by a given table.
     *
     * @param string $table
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($table)
    {
        event(new DeletedEvent($this->breadmanager->getBreadByTable($table)));
        return response('', $this->breadmanager->deleteBread($table) ? 200 : 500);
    }

    /**
     * Get BREAD properties (accessors, scopes and relationships) for a model.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function getProperties(Request $request)
    {
        $model = $request->get('model', '');
        if ($model == '') {
            return response('Please enter a model class', 400);
        }
        $model = Str::start($model, '\\');
        if (!class_exists($model)) {
            return response('Model "'.$model.'" does not exist!', 400);
        }
        $instance = new $model();
        $reflection = $this->breadmanager->getModelReflectionClass($model);
        $resolve_relationships = $request->get('resolve_relationships', false);

        return response()->json([
            'columns'       => VoyagerFacade::getColumns($instance->getTable()),
            'computed'      => $this->breadmanager->getModelComputedProperties($reflection)->values(),
            'scopes'        => $this->breadmanager->getModelScopes($reflection)->values(),
            'relationships' => $this->breadmanager->getModelRelationships($reflection, $instance, $resolve_relationships)->values(),
            'softdeletes'   => in_array(SoftDeletes::class, class_uses($instance)),
        ], 200);
    }

    /**
     * Get all BREADs.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBreads()
    {
        return response()->json([
            'breads'  => $this->breadmanager->getBreads(),
            'backups' => $this->breadmanager->getBackups(),
        ], 200);
    }

    /**
     * Backup a BREAD.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function backupBread(Request $request)
    {
        $table = $request->get('table', '');
        $result = $this->breadmanager->backupBread($table);
        event(new BackedUpEvent($this->breadmanager->getBreadByTable($table)));

        return response($result, $result === false ? 500 : 200);
    }

    /**
     * Rollback a BREAD.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function rollbackBread(Request $request)
    {
        $table = $request->get('table', '');
        $result = $this->breadmanager->rollbackBread($table, $request->get('path', ''));
        event(new RestoredEvent($this->breadmanager->getBreadByTable($table)));

        return response($result, $result === false ? 500 : 200);
    }
}
