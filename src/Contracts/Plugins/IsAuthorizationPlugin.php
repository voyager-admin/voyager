<?php

namespace Voyager\Admin\Contracts\Plugins;

abstract class IsAuthorizationPlugin extends IsGenericPlugin
{
    abstract public function authorize($ability, $arguments = []);
}
