<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Contracts\Bread\Formfield;

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
            'label'         => '',
            'description'   => '',
            'column'        => null,
            'browse_list'   => null,
            'add_view'      => null,
        ];
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
