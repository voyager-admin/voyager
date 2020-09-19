<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Contracts\Formfields\Formfield;

class Slug implements Formfield
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
