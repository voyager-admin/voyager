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
            $value = VoyagerFacade::getJson($value, []);
        }

        return $value;
    }

    public function read($value)
    {
        return $this->edit($value);
    }

    public function edit($value)
    {
        if (!is_array($value)) {
            $value = VoyagerFacade::getJson($value, []);
        }

        if ($this->asArray() && is_array($value)) {
            foreach ($value as $key => $row) {
                $value[$key] = ['null' => $row];
            }
        }

        return $value;
    }

    public function store($value)
    {
        if ($this->asArray()) {
            foreach ($value as $key => $row) {
                $value[$key] = $row['null'];
            }
        }

        return json_encode($value);
    }

    public function update($model, $value, $old)
    {
        return $this->store($value);
    }

    private function asArray()
    {
        $withoutKey = false;
        collect($this->options->formfields)->each(function ($formfield) use (&$withoutKey) {
            if ($formfield->column->column == null || $formfield->column->column == '') {
                $withoutKey = true;
            }
        });

        return ($withoutKey && count($this->options->formfields) == 1);
    }
}
