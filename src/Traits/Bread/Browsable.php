<?php

namespace Voyager\Admin\Traits\Bread;

use DB;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

trait Browsable
{
    public function loadSoftDeletesQuery($bread, $layout, $softdeleted, $query)
    {
        $this->uses_soft_deletes = $bread->usesSoftDeletes();
        if (!isset($layout->options->soft_deletes) || !$layout->options->soft_deletes || !$this->uses_soft_deletes) {
            $this->uses_soft_deletes = false;

            return $query;
        }
        if ($softdeleted == 'show') {
            $query = $query->withTrashed();
        } elseif ($softdeleted == 'only') {
            $query = $query->onlyTrashed();
        }

        return $query;
    }

    public function globalSearchQuery($global, $layout, $locale, $query)
    {
        if (!empty($global)) {
            $query = $query->where(function ($query) use ($global, $layout, $locale) {
                $layout->searchableFormfields()->each(function ($formfield) use (&$query, $global, $locale) {
                    $query = $this->queryColumn($query, $formfield, $global, $locale, true);
                });
            });
        }

        return $query;
    }

    public function columnSearchQuery($filters, $layout, $query, $locale, &$warnings)
    {
        collect(array_filter($filters))->each(function ($filter, $column) use ($layout, &$query, $locale, &$warnings) {
            $formfield = $layout->getFormfieldByColumn($column);
            if (!$formfield) {
                $warnings[] = __('voyager::bread.column_does_not_exist_search', ['column' => $column]);

                return;
            }

            $query = $this->queryColumn($query, $formfield, $filter, $locale);
        });

        return $query;
    }

    public function applyCustomFilter($bread, $layout, $filter, $query)
    {
        if (!is_null($filter) && is_array($filter)) {
            // Validate filter exists in layout
            if (collect($layout->options->filters)->where('column', $filter['column'])->where('operator', $filter['operator'])->where('value', $filter['value'])->count() > 0) {
                if ($filter['column']) {
                    $query = $query->where($filter['column'], $filter['operator'], $filter['value']);
                }
            }
        }

        return $query;
    }

    public function applyCustomScope($bread, $layout, $filter, $query, &$warnings)
    {
        if (!is_null($filter) && is_array($filter)) {
            // Validate filter exists in layout
            if (collect($layout->options->filters)->where('column', $filter['column'])->where('operator', $filter['operator'])->where('value', $filter['value'])->count() > 0) {
                if ($filter['column'] === null) {
                    if (method_exists($query, $filter['value'])) {
                        $query = $query->{$filter['value']}();
                    } else {
                        $warnings[] = __('voyager::bread.scope_does_not_exist', ['scope' => $filter['value']]);
                    }
                }
            }
        }

        return $query;
    }

    public function orderQuery($layout, $direction, $order, $query, $locale, &$warnings)
    {
        if (!empty($direction) && !empty($order)) {
            $formfield = $layout->getFormfieldByColumn($order);
            if ($formfield && $formfield->column->type == 'column') {
                if ($layout->getFormfieldByColumn($order)->translatable ?? false) {
                    $query = $query->orderBy(DB::raw('lower('.$order.'->"$.'.$locale.'")'), $direction);
                } else {
                    $query = $query->orderBy($order, $direction);
                }
            } elseif ($formfield && $formfield->column->type == 'relationship') {
                $warnings[] = __('voyager::bread.can_not_order_by_relationship', ['column' => $formfield->column->column]);
            } else {
                $warnings[] =  __('voyager::bread.column_does_not_exist_order', ['column' => $order]);
            }
        }

        return $query;
    }

    public function eagerLoadRelationships($layout, $query, &$warnings)
    {
        $relationships = [];
        $layout->getFormfieldsByColumnType('relationship')->pluck('column.column')->each(function ($relationship) use (&$relationships) {
            list($r, $p) = explode('.', $relationship);
            $relationships[$r][] = $p;
        });

        $with = [];
        collect($relationships)->each(function ($props, $relationship) use (&$with, $query, &$warnings) {
            $instance = $query->getModel()->newInstance();
            if (method_exists($instance, $relationship)) {
                $keyName = $instance->{$relationship}()->getRelated()->getKeyName();
                $with[] = $relationship.':'.implode(',', [$keyName, ...$props]);
            } else {
                $warnings[] = __('voyager::bread.relationship_does_not_exist', ['relationship' => $relationship]);
            }
        });

        if (!empty($with)) {
            return $query->with($with);
        }

        return $query;
    }

    public function transformResults($layout, $translatable, $query, $global, $filters)
    {
        return $query->transform(function ($item) use ($translatable, $layout, $global, $filters) {
            $item->primary_key = $item->getKey();
            if ($translatable) {
                $item->dontTranslate();
            }
            // Add soft-deleted property
            $item->is_soft_deleted = $this->uses_soft_deletes ? $item->trashed() : false;

            $layout->formfields->each(function ($formfield) use (&$item, $global, $filters) {
                $column = $formfield->column->column;
                if ($formfield->column->type == 'relationship') {
                    $relationship = Str::before($column, '.');
                    $property = Str::after($column, '.');
                    if ($item->{$relationship} instanceof Collection) {
                        $q = $item->{$relationship};

                        /*if (!empty($global)) {
                            $q = $q->where(DB::raw('lower('.$property.')'), 'LIKE', '%'.strtolower($global).'%');
                        } elseif (array_key_exists($formfield->column->column, $filters)) {
                            $q = $q->where(DB::raw('lower('.$property.')'), 'LIKE', '%'.strtolower($filters[$formfield->column->column]).'%');
                        }*/

                        // X-Many relationship
                        $item->{$column} = $q->pluck($property)->transform(function ($value) use ($formfield) {
                            return $formfield->browse($value);
                        });
                    } elseif (!empty($item->{$relationship})) {
                        // Normal property/X-One relationship
                        $item->{$column} = $formfield->browse($item->{$relationship}->{$property});
                    }
                } elseif ($formfield->translatable ?? false) {
                    $value = $item->{$column};
                    if (is_string($value)) {
                        $value = json_decode($value) ?? [];
                    } elseif (empty($value)) {
                        $value = [];
                    }
                    foreach ($value as $locale => $content) {
                        $value->{$locale} = $formfield->browse($content);
                    }

                    $item->{$column} = $value;
                } else {
                    $item->{$column} = $formfield->browse($item->{$column});
                }
            });

            return $item;
        });
    }

    private function queryColumn($query, $formfield, $filter, $locale, $global = false)
    {
        $filter = '%'.strtolower($filter).'%';
        $translatable = $formfield->translatable ?? false;
        $column = $formfield->column->column;

        if (method_exists($formfield, 'query')) {
            return $formfield->query($query, $filter, ($translatable ? $locale : null), $global);
        }

        if ($formfield->column->type == 'column' && $translatable) {
            $query = $query->{$global ? 'orWhereRaw' : 'whereRaw'}('LOWER(`'.$column.'->"$.'.$locale.'`) LIKE ?', [$filter]);
        } elseif ($formfield->column->type == 'column') {
            $query = $query->{$global ? 'orWhereRaw' : 'whereRaw'}('LOWER(`'.$column.'`) LIKE ?', [$filter]);
        } elseif ($formfield->column->type == 'relationship') {
            if ($global) {
                // TODO: We could skip global-searching relationships here
                // return $query;
            }
            list($name, $column) = explode('.', $formfield->column->column);

            $query = $query->{$global ? 'orWhereHas' : 'whereHas'}($name, function ($q) use ($column, $filter, $translatable, $locale) {
                if ($translatable) {
                    $q->whereRaw('LOWER(`'.$column.'->$.'.$locale.'`) LIKE ?', [$filter]);
                } else {
                    $q->whereRaw('LOWER(`'.$column.'`) LIKE ?', [$filter]);
                }
            });
        }

        return $query;
    }
}
