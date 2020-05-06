<?php

namespace Voyager\Admin\Contracts\Plugins;

abstract class IsAuthorizationPlugin extends IsGenericPlugin
{
    public abstract function authorize($ability, $arguments = []);
}
