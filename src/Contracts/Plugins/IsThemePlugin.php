<?php

namespace Voyager\Admin\Contracts\Plugins;

abstract class IsThemePlugin extends IsGenericPlugin
{
    abstract public function getStyleRoute(): string;
}
