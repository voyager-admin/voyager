<?php

namespace Voyager\Admin\Manager;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Voyager\Admin\Classes\Action as ActionClass;
use Voyager\Admin\Classes\Bread as BreadClass;
use Voyager\Admin\Contracts\Formfields\Features;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Breads
{
    protected $formfields;
    protected $path;
    protected $breads;
    protected $backups = [];
    protected $actions;

    public function __construct()
    {
        $this->path = Str::finish(storage_path('voyager/breads'), '/');
    }

    /**
     * Sets the path where the BREAD-files are stored.
     *
     * @param string $path
     * @return string the current path.
     */
    public function setPath($path = null)
    {
        if ($path) {
            $old_path = $this->path;
            $this->path = Str::finish($path, '/');
            if ($old_path !== $path) {
                $this->breads = null;
            }
        }

        return $this->path;
    }

    /**
     * Get all BREADs from storage and validate.
     *
     * @return \Illuminate\Support\Collection<string, BreadClass>
     */
    public function getBreads()
    {
        if (!$this->breads) {
            VoyagerFacade::ensureDirectoryExists($this->path);
            $this->breads = collect(File::files($this->path))->transform(function ($bread) {
                $content = File::get($bread->getPathName());
                $json = VoyagerFacade::getJson($content);
                if ($json === false) {
                    VoyagerFacade::flashMessage('BREAD-file "'.basename($bread->getPathName()).'" does contain invalid JSON: '.json_last_error_msg(), 'yellow');

                    return;
                }

                $b = new BreadClass($json);

                // Push Exclude backups
                if (Str::contains($bread->getPathName(), '.backup.')) {
                    $date = Str::before(Str::after($bread->getFilename(), '.backup.'), '.json');
                    $this->backups[] = [
                        'table' => $b->table,
                        'path'  => $bread->getFilename(),
                        'date'  => $date,
                    ];

                    return null;
                }

                return $b;
            })->filter(function ($bread) {
                return $bread !== null;
            })->values()->mapWithKeys(static function ($value, $key) {
                return [$value->name_singular => $value];
            });
        }

        return $this->breads;
    }

    /**
     * Get backed-up BREADs.
     *
     * @return array
     */
    public function getBackups()
    {
        $this->breads = null;
        $this->backups = [];
        $this->getBreads();

        return $this->backups;
    }

    /**
     * Rollback BREAD to a given file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function rollbackBread($table, $path)
    {
        $path = Str::finish($this->path, '/');
        if ($this->backupBread($table) !== false) {
            return File::delete($path.$table.'.json') && File::copy($path.$path, $path.$table.'.json');
        }

        return false;
    }

    /**
     * Determine if a BREAD exists by the table name.
     *
     * @param string $table
     *
     * @return bool
     */
    public function hasBread($table)
    {
        return $this->getBread($table) !== null;
    }

    /**
     * Get a BREAD by the table name.
     *
     * @param string $table
     *
     * @return \Voyager\Admin\Classes\Bread
     */
    public function getBread($table)
    {
        return $this->getBreads()->where('table', $table)->first();
    }

    /**
     * Get a BREAD by the table name.
     *
     * @param string $breadName
     *
     * @return \Voyager\Admin\Classes\Bread
     */
    public function getBreadByName($breadName)
    {
        return $this->getBreads()->get($breadName);
    }

    /**
     * Store a BREAD-file.
     *
     * @param string $bread
     *
     * @return int|bool success
     */
    public function storeBread($bread)
    {
        $this->clearBreads();

        return File::put(Str::finish($this->path, '/').$bread->table.'.json', json_encode($bread, JSON_PRETTY_PRINT));
    }

    /**
     * Create a BREAD-object.
     *
     * @param string $table
     *
     * @return int|bool success
     */
    public function createBread($table)
    {
        // Guess the model name
        $name = Str::singular(Str::studly($table));

        $namespace = Str::start(Str::finish(Container::getInstance()->getNamespace() ?? 'App\\', '\\'), '\\');
        $model = (is_dir(app_path('Models')) ? $namespace.'Models\\' : $namespace).$name;

        if (!class_exists($model)) {
            $model = null;
        }

        $bread = [
            'table'         => $table,
            'slug'          => Str::slug($table),
            'name_singular' => str_replace('_', ' ', Str::singular(Str::title($table))),
            'name_plural'   => str_replace('_', ' ', Str::plural(Str::title($table))),
            'model'         => $model,
            'layouts'       => [],
        ];

        return new BreadClass($bread);
    }

    /**
     * Clears all BREAD-objects.
     */
    public function clearBreads()
    {
        $this->breads = null;
    }

    /**
     * Delete a BREAD from the filesystem.
     *
     * @param string $table The table of the BREAD
     */
    public function deleteBread($table)
    {
        $ret = File::delete(Str::finish($this->path, '/').$table.'.json');
        $this->clearBreads();

        return $ret;
    }

    /**
     * Backup a BREAD (copy table.json to table.backup.json).
     *
     * @param  string $table The table of the BREAD
     * @return string The name of the backup file
     */
    public function backupBread($table)
    {
        $old = $this->path.$table.'.json';
        $name = $table.'.backup.'.Carbon::now()->isoFormat('Y-MM-DD@HH-mm-ss').'.json';
        $new = $this->path.$name;

        if (File::exists($old)) {
            if (!File::copy($old, $new)) {
                return false;
            }
        }

        return $name;
    }

    /**
     * Get the search placeholder (Search for Users, Posts, etc...).
     *
     * @return string $placeholder The placeholder
     */
    public function getBreadSearchPlaceholder()
    {
        $breads = $this->getBreads()
        ->reject(function ($bread) {
            return empty($bread->global_search_field);
        })->shuffle();

        if ($breads->count() > 1) {
            return __('voyager::generic.search_for_breads', [
                'bread'  => $breads[0]->name_plural,
                'bread2' => $breads[1]->name_plural,
            ]);
        } elseif ($breads->count() == 1) {
            return __('voyager::generic.search_for_bread', [
                'bread' => $breads[0]->name_plural,
            ]);
        }

        return __('voyager::generic.search');
    }

    /**
     * Add a formfield.
     *
     * @param string $class The class of the formfield
     */
    public function addFormfield($class)
    {
        if (!$this->formfields) {
            $this->formfields = collect();
        }
        $class = new $class();

        if (!method_exists($class, 'name')) {
            throw new \Exception('Formfields need to implement the "name" method.');
        } elseif (!method_exists($class, 'type')) {
            throw new \Exception('Formfields need to implement the "type" method.');
        } elseif (!method_exists($class, 'getComponentName')) {
            throw new \Exception('Formfields need to implement the "getComponentName" method.');
        } elseif (!method_exists($class, 'getBuilderComponentName')) {
            throw new \Exception('Formfields need to implement the "getBuilderComponentName" method.');
        }

        $this->formfields->push($class);
    }

    /**
     * Get formfields.
     *
     * @return Illuminate\Support\Collection The formfields
     */
    public function getFormfields()
    {
        return $this->formfields->map(function ($formfield) {
            return [
                'name'                      => $formfield->name(),
                'type'                      => $formfield->type(),
                'can_be_translated'         => !property_exists($formfield, 'notTranslatable'),
                'in_settings'               => !property_exists($formfield, 'notAsSetting'),
                'in_lists'                  => !property_exists($formfield, 'notInLists'),
                'in_views'                  => !property_exists($formfield, 'notInViews'),
                'browse_array'              => property_exists($formfield, 'browseArray'),
                'allow_columns'             => !property_exists($formfield, 'noColumns'),
                'allow_computed_props'      => !property_exists($formfield, 'noComputedProps'),
                'allow_relationships'       => !property_exists($formfield, 'noRelationships'),
                'allow_relationship_props'  => !property_exists($formfield, 'noRelationshipProps'),
                'allow_relationship_pivots' => !property_exists($formfield, 'noRelationshipPivots'),
                'component'                 => $formfield->getComponentName(),
                'builder_component'         => $formfield->getBuilderComponentName(),
            ];
        });
    }

    /**
     * Get a formfield by type.
     *
     * @param string $type The type of the formfield
     *
     * @return object The formfield
     */
    public function getFormfield(string $type)
    {
        if (!$this->formfields) {
            $this->formfields = collect();
        }

        return $this->formfields->filter(function ($formfield) use ($type) {
            return $formfield->type() == $type;
        })->first();
    }

    /**
     * Get the reflection class for a model.
     *
     * @param string $model The fully qualified model name
     *
     * @return ReflectionClass The reflection object
     */
    public function getModelReflectionClass(string $model): \ReflectionClass
    {
        return new \ReflectionClass($model);
    }

    public function getModelScopes(\ReflectionClass $reflection): Collection
    {
        return collect($reflection->getMethods())->filter(function ($method) {
            return Str::startsWith($method->name, 'scope');
        })->whereNotIn('name', ['scopeWithTranslations', 'scopeWithTranslation', 'scopeWhereTranslation'])->transform(function ($method) {
            return lcfirst(Str::replaceFirst('scope', '', $method->name));
        });
    }

    public function getModelComputedProperties(\ReflectionClass $reflection): Collection
    {
        return collect($reflection->getMethods())->filter(function ($method) {
            return Str::startsWith($method->name, 'get') && Str::endsWith($method->name, 'Attribute');
        })->transform(function ($method) {
            $name = Str::replaceFirst('get', '', $method->name);
            $name = Str::replaceLast('Attribute', '', $name);

            return lcfirst($name);
        })->filter();
    }

    public function getModelRelationships(\ReflectionClass $reflection, Model $model, bool $resolve = false): Collection
    {
        $single = [
            BelongsTo::class,
            HasOne::class,
            HasOneThrough::class,
        ];

        $multi = [
            BelongsToMany::class,
            HasMany::class,
            HasManyThrough::class,
        ];

        return collect($reflection->getMethods())->transform(function ($method) use ($single, $multi, $model, $resolve) {
            $type = $method->getReturnType();
            if ($type && in_array(strval($type->getName()), array_merge($single, $multi))) {
                $columns = [];
                $scopes = [];
                $pivot = [];
                $computed = [];
                if ($resolve) {
                    $relationship = $model->{$method->getName()}();
                    $related = $relationship->getRelated();
                    $table = $related->getTable();
                    if ($type->getName() == BelongsToMany::class) {
                        $pivot = array_values(array_diff(VoyagerFacade::getColumns($relationship->getTable()), [
                            $relationship->getForeignPivotKeyName(),
                            $relationship->getRelatedPivotKeyName(),
                        ]));
                    }
                    $columns = VoyagerFacade::getColumns($table);
                    $relationship_reflection = $this->getModelReflectionClass(get_class($related));
                    $computed = $this->getModelComputedProperties($relationship_reflection)->values();
                    $scopes = $this->getModelScopes($relationship_reflection)->values();
                }

                return [
                    'method'    => $method->getName(),
                    'type'      => class_basename($type->getName()),
                    'table'     => $table,
                    'columns'   => $columns,
                    'scopes'    => $scopes,
                    'pivot'     => $pivot,
                    'computed'  => $computed,
                    'key_name'  => $relationship->getRelated()->getKeyName(),
                    'multiple'  => in_array(strval($type->getName()), $multi),
                ];
            }

            return null;
        })->filter();
    }

    /**
     * Add an action to BREADs.
     *
     * @param Action $action One or more action instances.
     */
    public function addAction()
    {
        if (is_null($this->actions)) {
            $this->actions = collect();
        }

        foreach (func_get_args() as $action) {
            $this->actions->push($action);
        }
    }

    /**
     * Manipulate all actions.
     *
     * @param callable $callback A callback which gets all actions and returns a manipulated version of them.
     */
    public function manipulateActions(callable $callback)
    {
        $this->actions = $callback($this->actions);
    }

    /**
     * Gets all actions for a BREAD.
     *
     * @param BreadClass $bread The BREAD.
     * @return Collection The collection of Actions
     */
    public function getActionsForBread(BreadClass $bread)
    {
        return $this->actions->filter(function ($action) use ($bread) {
            $display = true;
            if (is_callable($action->callback)) {
                $display = $action->callback->call($this, $bread) ?? true;
            }

            if (!is_null($action->permission)) {
                if (!VoyagerFacade::authorize(VoyagerFacade::auth()->user(), $action->permission, $bread)) {
                    $display = false;
                }
            }

            return $display;
        })->transform(function ($action) use ($bread) {
            // Resolve route
            if (is_callable($action->route_callback)) {
                $action->route_name = $action->route_callback->call($this, $bread) ?? '#';
            } else {
                $action->route_name = $action->route_callback;
            }

            return $action;
        });
    }
}
