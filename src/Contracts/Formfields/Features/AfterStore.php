<?php

namespace Voyager\Admin\Contracts\Formfields\Features;

interface AfterStore
{
    public function stored($model, $value);
}