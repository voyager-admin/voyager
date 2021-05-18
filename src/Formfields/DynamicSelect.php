<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class DynamicSelect extends Formfield
{
    public $browseArray = true;

    public function type(): string
    {
        return 'dynamic_select';
    }

    public function name(): string
    {
        return __('voyager::formfields.dynamic_select.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-dynamic-select';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-dynamic-select-builder';
    }

    public function add()
    {
        return null;
    }

    public function browse($value)
    {
        if (!is_array($value)) {
            return VoyagerFacade::getJson($value, []);
        }

        return $value;
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
