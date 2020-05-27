<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class Checkboxes extends Formfield
{
    public function type(): string
    {
        return 'checkboxes';
    }

    public function name(): string
    {
        return __('voyager::formfields.checkboxes.name');
    }

    public function listOptions(): array
    {
        return [
            'options'   => [],
        ];
    }

    public function viewOptions(): array
    {
        return [
            'options'     => [],
        ];
    }

    public function browse($input)
    {
        return json_decode($input) ?? [];
    }

    public function read($input)
    {
        return json_decode($input) ?? [];
    }

    public function edit($input)
    {
        return json_decode($input) ?? [];
    }

    public function update($model, $input, $old)
    {
        return json_encode($input) ?? '';
    }

    public function store($input)
    {
        return json_encode($input) ?? '';
    }

    public function browseDataAsArray()
    {
        return true;
    }
}
