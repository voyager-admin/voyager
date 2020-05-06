<?php

namespace Voyager\Admin\Contracts\Plugins;

abstract class IsThemePlugin extends IsGenericPlugin
{
    public abstract function getStyleRoute(): string;
}
