<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class Radio extends Formfield
{
    public function type(): string
    {
        return 'radio';
    }

    public function name(): string
    {
        return __('voyager::formfields.radio.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-radio';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-radio-builder';
    }

    public function add()
    {
        return null;
    }
}
