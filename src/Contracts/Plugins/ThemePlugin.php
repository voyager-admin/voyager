<?php

namespace Voyager\Admin\Contracts\Plugins;

interface ThemePlugin extends GenericPlugin
{
    public function getStyleRoute(): string;
}
