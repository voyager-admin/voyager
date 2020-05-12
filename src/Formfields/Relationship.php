<?php

namespace Voyager\Admin\Formfields;

use Illuminate\Support\Str;
use Voyager\Admin\Classes\Formfield;

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
            'column'        => null,
            'browse_list'   => null,
            'add_view'      => null,
        ];
    }

    public function add()
    {
        return [];
    }

    public function edit($value)
    {
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->map(function ($item) {
                return $item->getKey();
            });
        }
        if ($value) {
            return [$value->getKey()];
        }

        return [];
    }

    public function update($model, $value, $old)
    {
        $column = $this->column->column;
        $relationship = $model->{$column}();
        $type = class_basename(get_class($relationship));
        if (is_array($value)) {
            if ($type == 'BelongsToMany') {
                $model->{$column}()->sync($value);
            } elseif ($type == 'BelongsTo') {
                $relationship->associate($value[0]);
            } elseif ($type == 'BelongsTo') {
                if (count($value) == 0) {
                    $relationship->dissociate();
                } else {
                    $relationship->associate($value[0]);
                }
            }
        }
    }

    public function stored($model, $value)
    {
        $this->update($model, $value, []);
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
