<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class DynamicSelect extends Formfield
{
    public function type(): string
    {
        return 'dynamic_select';
    }

    public function name(): string
    {
        return __('voyager::formfields.dynamic_select.name');
    }

    public function listOptions(): array
    {
        return [];
    }

    public function viewOptions(): array
    {
        return [
            'route_name'  => '',
        ];
    }

    public function canBeTranslated()
    {
        return false;
    }

    public function canBeUsedInList()
    {
        return true;
    }

    public function allowColumns()
    {
        return true;
    }

    public function allowComputed()
    {
        return false;
    }

    public function allowRelationships()
    {
        return false;
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
