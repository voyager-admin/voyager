<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Contracts\Formfields\Features\BrowseArray;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Checkbox extends Formfield implements BrowseArray
{
    public function type(): string
    {
        return 'checkbox';
    }

    public function name(): string
    {
        return __('voyager::formfields.checkbox.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-checkbox';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-checkbox-builder';
    }

    public function add()
    {
        return [];
    }

    public function browse($value)
    {
        if (!is_array($value)) {
            return VoyagerFacade::getJson($value, []);
        }

        return $value;
    }

    public function read($value)
    {
        return $this->browse($value);
    }

    public function edit($value)
    {
        return $this->browse($value);
    }

    public function store($value)
    {
        return json_encode($value);
    }

    public function update($model, $value, $old)
    {
        return $this->store($value);
    }
}
