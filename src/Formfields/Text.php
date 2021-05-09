<?php

namespace Voyager\Admin\Formfields;

use Illuminate\Support\Str;
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

    public function getComponentName(): string
    {
        return 'formfield-text';
    }

    public function getBuilderComponentName(): string
    {
        return 'formfield-text-builder';
    }

    public function browse($input)
    {
        return Str::limit(strip_tags($input), $this->options->display_length ?? 150);
    }

    public function add()
    {
        return $this->options->default_value ?? '';
    }
}
