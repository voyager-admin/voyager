<?php

namespace Voyager\Admin\Formfields;

use Illuminate\Support\Str;
use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Relationship extends Formfield
{
    public function type(): string
    {
        return 'relationship';
    }

    public function name(): string
    {
        return __('voyager::formfields.relationship.name');
    }

    public function listOptions(): array
    {
        return [];
    }

    public function viewOptions(): array
    {
        return [
            'add_view'          => null,
            'editable'          => true,
            'display_column'    => null,
            'search_text'       => '',
            'allow_null'        => true,
            'pivots'            => [],
            'scope'             => null,
        ];
    }

    public function add()
    {
        return [];
    }

    public function edit($value)
    {
        $column = $this->options->display_column ?? null;
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->map(function ($item) use ($column) {
                return [
                    'key'   => $item->getKey(),
                    'value' => $item->{$column}
                ];
            });
        }
        if ($value instanceof \Illuminate\Database\Eloquent\Model) {
            return [[
                'key'   => $value->getKey(),
                'value' => $value->{$column}
            ]];
        }

        return [];
    }

    public function update($model, $value, $old)
    {
        if (!$this->options->editable) {
            return $old;
        }

        $keys = collect($value)->pluck('key')->toArray();

        $column = $this->column->column;
        $relationship = $model->{$column}();
        $type = class_basename(get_class($relationship));

        if (is_array($keys)) {
            if ($type == 'BelongsToMany') {
                $model->{$column}()->sync($keys);
            } elseif ($type == 'BelongsTo') {
                if (count($keys) == 0) {
                    $relationship->dissociate();
                } else {
                    $relationship->associate($keys[0]);
                }
            } elseif ($type == 'HasMany' || $type == 'HasOne' || $type == 'HasManyThrough' || $type == 'HasOneThrough') {
                $old_keys = [];
                if ($old instanceof \Illuminate\Support\Collection) {
                    $old_keys = $old->modelKeys();
                }
                $related = $relationship->getRelated();
                $key = $relationship->getForeignKeyName();
                $removed = array_diff($old_keys, $keys);
                $added = array_diff($keys, $old_keys);

                foreach ($removed as $remove) {
                    $m = $related->find($remove);
                    $m->{$key} = null;
                    $m->save();
                }

                foreach ($added as $add) {
                    $m = $related->find($add);
                    $m->{$key} = $model->getKey();
                    $m->save();
                }
            }
        }
    }

    public function stored($model, $value)
    {
        $this->update($model, $value, collect());
    }

    public function canBeTranslated()
    {
        return false;
    }

    public function canBeUsedAsSetting()
    {
        return false;
    }

    public function canBeUsedInList()
    {
        return false;
    }

    public function browseDataAsArray()
    {
        return true;
    }

    public function allowColumns()
    {
        return false;
    }

    public function allowComputed()
    {
        return false;
    }

    public function allowRelationships()
    {
        return true;
    }

    public function allowRelationshipColumns()
    {
        return false;
    }

    public function allowRelationshipPivots()
    {
        return false;
    }
}
