<?php

namespace Voyager\Admin\Formfields;

use Voyager\Admin\Classes\Formfield;

class Password extends Formfield
{
    public function type(): string
    {
        return 'password';
    }

    public function name(): string
    {
        return __('voyager::formfields.password.name');
    }

    public function listOptions(): array
    {
        return [];
    }

    public function viewOptions(): array
    {
        return [];
    }

    public function update($model, $input, $old)
    {
        if (!isset($input) || empty($input)) {
            return $old;
        }

        return $this->store($input);
    }

    public function store($input)
    {
        return bcrypt($input);
    }
}
