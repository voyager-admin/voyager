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

    public function listOptions(): array
    {
        return [
            'display_length'    => 150,
        ];
    }

    public function viewOptions(): array
    {
        return [
            'field'         => '',
            'placeholder'   => '',
            'default_value' => '',
            'replacement'   => '-',
            'lower'         => false,
            'strict'        => false,
        ];
    }
}
