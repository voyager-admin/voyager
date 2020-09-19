<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Add;
use Voyager\Admin\Contracts\Formfields\Formfield;

class Radio implements Formfield, Add
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
