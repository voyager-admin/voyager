<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class SimpleArray extends Formfield
{
    public function type(): string
    {
        return 'simple_array';
    }

    public function name(): string
    {
        return __('voyager::formfields.simple_array.name');
    }

    public function listOptions(): array
    {
        return [
            'display'  => 3,
        ];
    }

    public function viewOptions(): array
    {
        return [
            'max'       => 0,
            'item_text' => __('voyager::generic.item'),
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
