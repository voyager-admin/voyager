<?php

namespace Voyager\Admin\Contracts\Plugins;

interface IsThemePlugin extends IsGenericPlugin
{
    public function getStyleRoute(): string;
}
