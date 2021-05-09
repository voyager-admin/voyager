<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class Slug extends Formfield
{
    public function type(): string
    {
        return 'slug';
    }

    public function name(): string
    {
        return __('voyager::formfields.slug.name');
    }

    public function getComponentName(): string
    {
        return 'formfield-slug';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-slug-builder';
    }
}
