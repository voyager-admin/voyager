<?php

namespace Voyager\Admin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    public function __call($name, $arguments)
    {
        return true;
    }
}
