<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Repeater extends Formfield
{
    public $browseArray = true;

    public function type(): string
    {
        return 'repeater';
    }

    public function name(): string
    {
        return __('voyager::formfields.repeater.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-repeater';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-repeater-builder';
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
