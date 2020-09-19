<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Contracts\Formfields\Formfield;

class Text implements Formfield
{
    public function type(): string
    {
        return 'text';
    }

    public function name(): string
    {
        return __('voyager::formfields.text.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-text';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-text-builder';
    }
}
