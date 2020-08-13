<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\Support\Collection;

interface AuthorizationPlugin extends GenericPlugin
{
    public function authorize($user, $ability, $arguments = []): ?bool;
}
