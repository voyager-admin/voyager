<?php

namespace Voyager\Admin\Contracts\Plugins;

interface AuthorizationPlugin extends GenericPlugin
{
    public function authorize($ability, $arguments = []);
}
