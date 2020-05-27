<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class Radios extends Formfield
{
    public function type(): string
    {
        return 'radios';
    }

    public function name(): string
    {
        return __('voyager::formfields.radios.name');
    }

    public function listOptions(): array
    {
        return [
            'options'   => [],
        ];
    }

    public function viewOptions(): array
    {
        return [
            'options'     => [],
        ];
    }
}
