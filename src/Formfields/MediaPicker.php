<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class MediaPicker extends Formfield
{
    public $browseArray = true;

    public function type(): string
    {
        return 'media_picker';
    }

    public function name(): string
    {
        return __('voyager::formfields.media_picker.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-media-picker';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-media-picker-builder';
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
