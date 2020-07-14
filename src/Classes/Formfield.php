<?php

namespace Voyager\Admin\Classes;

use Voyager\Admin\Contracts\Bread\Formfield as FormfieldContract;

abstract class Formfield implements FormfieldContract, \JsonSerializable
{
    public $translatable = false;
    public $column;

    public function browse($input)
    {
        return $input;
    }

    public function read($input)
    {
        return $input;
    }

    public function edit($input)
    {
        return $input;
    }

    public function update($model, $input, $old)
    {
        return $input;
    }

    public function add()
    {
        return '';
    }

    public function store($input)
    {
        return $input;
    }

    public function stored($model, $data)
    {
        return;
    }

    public function canBeTranslated()
    {
        return true;
    }

    public function canBeUsedAsSetting()
    {
        return true;
    }

    public function canBeUsedInList()
    {
        return true;
    }

    public function canBeUsedInView()
    {
        return true;
    }

    public function browseDataAsArray()
    {
        return false;
    }

    public function allowColumns()
    {
        return true;
    }

    public function allowComputed()
    {
        return true;
    }

    public function allowRelationships()
    {
        return false;
    }

    public function allowRelationshipColumns()
    {
        return true;
    }

    public function allowRelationshipPivots()
    {
        return true;
    }

    public function jsonSerialize()
    {
        // Formfield will be used in BREAD builder. We need list/view options and some other things
        if (!$this->column) {
            $viewoptions = array_merge($this->viewOptions(), ['title' => '', 'description' => '']);
            return [
                'name'                     => $this->name(),
                'type'                     => $this->type(),
                'canBeTranslated'          => $this->canBeTranslated(),
                'listOptions'              => (object) $this->listOptions(),
                'viewOptions'              => (object) $viewoptions,
                'asSetting'                => $this->canBeUsedAsSetting(),
                'inList'                   => $this->canBeUsedInList(),
                'inView'                   => $this->canBeUsedInView(),
                'browseArray'              => $this->browseDataAsArray(),
                'allowColumns'             => $this->allowColumns(),
                'allowComputed'            => $this->allowComputed(),
                'allowRelationships'       => $this->allowRelationships(),
                'allowRelationshipColumns' => $this->allowRelationshipColumns(),
                'allowPivot'               => $this->allowRelationshipPivots(),
            ];
        }

        // BREAD was already stored by the BREAD builder. We don't need the above things at this point
        return array_merge([
            'type'            => $this->type(),
        ], (array) $this);
    }
}
