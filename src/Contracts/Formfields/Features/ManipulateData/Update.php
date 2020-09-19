<?php

namespace Voyager\Admin\Contracts\Formfields\Features\ManipulateData;

interface Update
{
    public function update($model, $value, $old);
}