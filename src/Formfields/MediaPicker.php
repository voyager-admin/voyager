<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class MediaPicker extends Formfield
{
    public function type(): string
    {
        return 'media_picker';
    }

    public function name(): string
    {
        return __('voyager::formfields.media_picker.name');
    }

    public function listOptions(): array
    {
        return [
            'display'   => 3,
            'icons'     => true,
        ];
    }

    public function viewOptions(): array
    {
        return [
            'min'         => 0,
            'max'         => 0,
            'list_url'    => null,
            'upload_url'  => null,
            'meta'        => [],
        ];
    }

    public function browse($input)
    {
        return json_decode($input);
    }

    public function read($input)
    {
        return json_decode($input);
    }

    public function edit($input)
    {
        return json_decode($input);
    }

    public function update($model, $input, $old)
    {
        return json_encode($input);
    }

    public function store($input)
    {
        return json_encode($input);
    }

    public function browseDataAsArray()
    {
        return true;
    }
}
