<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class Select extends Formfield
{
    public function type(): string
    {
        return 'select';
    }

    public function name(): string
    {
        return __('voyager::formfields.select.name');
    }

    public function listOptions(): array
    {
        return [
            'multiple'  => false,
            'options'   => [],
        ];
    }

    public function viewOptions(): array
    {
        return [
            'multiple'    => false,
            'options'     => [],
        ];
    }

    public function browse($input)
    {
        if ($this->options->multiple === true) {
            return json_decode($input) ?? [];
        }

        return $input;
    }

    public function read($input)
    {
        if ($this->options->multiple === true) {
            return json_decode($input) ?? [];
        }

        return $input;
    }

    public function edit($input)
    {
        if ($this->options->multiple === true) {
            return json_decode($input) ?? [];
        }

        return $input;
    }

    public function update($model, $input, $old)
    {
        if ($this->options->multiple === true) {
            return json_encode($input) ?? '';
        }

        return $input;
    }

    public function store($input)
    {
        if ($this->options->multiple === true) {
            return json_encode($input) ?? '';
        }

        return $input;
    }

    public function browseDataAsArray()
    {
        return true;
    }
}
