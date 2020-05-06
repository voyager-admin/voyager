<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\View\View;

abstract class IsWidgetPlugin extends IsGenericPlugin
{
    public abstract function getWidgetView(): View;

    public abstract function getWidth(): int;
}
