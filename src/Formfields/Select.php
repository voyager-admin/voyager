<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Select extends Formfield
{
    public $browseArray = true;

    public function type(): string
    {
        return 'select';
    }

    public function name(): string
    {
        return __('voyager::formfields.select.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-select';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-select-builder';
    }

    public function add()
    {
        if ($this->options->multiple ?? false) {
            return [];
        }

        return null;
    }

    public function browse($value)
    {
        if ($this->options->multiple ?? false && !is_array($value)) {
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
        if ($this->options->multiple ?? false) {
            return json_encode($value);
        }

        return $value;
    }

    public function update($model, $value, $old)
    {
        return $this->store($value);
    }
}
