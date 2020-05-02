<?php

namespace Voyager\Admin\Contracts\Plugins;

interface IsAuthorizationPlugin extends IsGenericPlugin
{
    public function authorize($ability, $arguments = []);
}
