<?php

namespace Voyager\Admin\Traits\Bread;

use DB;
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
            return $query->where(function ($query) use ($global, $layout, $locale) {
                $layout->searchableFormfields()->each(function ($formfield) use (&$query, $global, $locale) {
                    $query = $this->queryColumn($query, $formfield, $global, $locale, true);
                });
            });
        }

        return $query;
    }

    public function columnSearchQuery($filters, $layout, $query, $locale)
    {
        collect(array_filter($filters))->each(function ($filter, $column) use ($layout, &$query, $locale) {
            $formfield = $layout->getFormfieldByColumn($column);
            if (!$formfield) {
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

    public function applyCustomScope($bread, $layout, $filter, $query)
    {
        if (!is_null($filter) && is_array($filter)) {
            // Validate filter exists in layout
            if (collect($layout->options->filters)->where('column', $filter['column'])->where('operator', $filter['operator'])->where('value', $filter['value'])->count() > 0) {
                if ($filter['column'] === null) {
                    if (method_exists($query, $filter['value'])) {
                        $query = $query->{$filter['value']}();
                    } else {
                        // TODO: Scope does not exist. Show error?
                    }
                }
            }
        }

        return $query;
    }

    public function orderQuery($layout, $direction, $order, $query, $locale)
    {
        if (!empty($direction) && !empty($order)) {
            $formfield = $layout->getFormfieldByColumn($order);
            if ($formfield && $formfield->column->type == 'column') {
                if ($layout->getFormfieldByColumn($order)->translatable ?? false) {
                    $query = $query->orderBy(DB::raw('lower('.$order.'->"$.'.$locale.'")'), $direction);
                } else {
                    $query = $query->orderBy($order, $direction);
                }
            }
        }

        return $query;
    }

    public function transformResults($layout, $translatable, $query)
    {
        return $query->transform(function ($item) use ($translatable, $layout) {
            $item->primary_key = $item->getKey();
            if ($translatable) {
                $item->dontTranslate();
            }
            // Add soft-deleted property
            $item->is_soft_deleted = $this->uses_soft_deletes ? $item->trashed() : false;

            $layout->formfields->each(function ($formfield) use (&$item) {
                $column = $formfield->column->column;
                if ($formfield->column->type == 'relationship') {
                    $relationship = Str::before($column, '.');
                    $property = Str::after($column, '.');
                    if (Str::contains($property, 'pivot.')) {
                        // Pivot data
                        $property = Str::after($property, 'pivot.');
                        $pivot = [];
                        $item->{$relationship}->each(function ($related) use (&$pivot, $formfield, $property) {
                            if (isset($related->pivot) && isset($related->pivot->{$property})) {
                                $pivot[] = $formfield->browse($related->pivot->{$property});
                            }
                        });
                        $item->{$column} = $pivot;
                    } elseif ($property == 'relationship_amount') {
                        // Set as string so 0/1 doesn't get evaluated as bool by javascript
                        if ($item->{$relationship} instanceof Collection) {
                            $item->{$column} = ''.$item->{$relationship}->count();
                        } elseif (is_array($item->{$relationship})) {
                            $item->{$column} = ''.count($item->{$relationship});
                        } else {
                            $item->{$column} = $item->{$relationship} == null ? '0' : '1';
                        }
                    } elseif ($item->{$relationship} instanceof Collection) {
                        // X-Many relationship
                        $item->{$column} = $item->{$relationship}->pluck($property)->transform(function ($value) use ($formfield) {
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
        $translatable = $formfield->translatable ?? false;
        $column = $formfield->column->column;

        if (method_exists($formfield, 'query')) {
            return $formfield->query($query, $filter, ($translatable ? $locale : null), $global);
        }

        if ($formfield->column->type == 'column' && $translatable) {
            $query = $query->{$global ? 'orWhere' : 'where'}(DB::raw('lower('.$column.'->"$.'.$locale.'")'), 'LIKE', '%'.strtolower($filter).'%');
        } elseif ($formfield->column->type == 'column') {
            $query = $query->{$global ? 'orWhere' : 'where'}(DB::raw('lower('.$column.')'), 'LIKE', '%'.strtolower($filter).'%');
        } elseif ($formfield->column->type == 'relationship') {
            list($name, $column) = explode('.', $formfield->column->column);
            $query = $query->{$global ? 'orWhereHas' : 'whereHas'}($name, function ($q) use ($column, $filter, $translatable, $locale) {
                if ($translatable) {
                    $q->where(DB::raw('lower('.$column.'->"$.'.$locale.'")'), 'LIKE', '%'.strtolower($filter).'%');
                } else {
                    $q->where(DB::raw('lower('.$column.')'), 'LIKE', '%'.strtolower($filter).'%');
                }
            });
        }

        return $query;
    }
}
