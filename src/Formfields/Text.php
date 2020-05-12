<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class Text extends Formfield
{
    public function type(): string
    {
        return 'text';
    }

    public function name(): string
    {
        return __('voyager::formfields.text.name');
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
            'placeholder'   => '',
            'default_value' => '',
            'rows'          => 1,
        ];
    }
}
