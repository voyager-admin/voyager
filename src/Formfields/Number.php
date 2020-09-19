<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Contracts\Formfields\Formfield;
use Voyager\Admin\Contracts\Formfields\Features\ManipulateData\Add;

class Number implements Formfield, Add
{
    public function type(): string
    {
        return 'number';
    }

    public function name(): string
    {
        return __('voyager::formfields.number.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-number';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-number-builder';
    }

    public function add()
    {
        return $this->options->default_value ?? 0;
    }
}
