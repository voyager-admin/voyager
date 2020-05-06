<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\View\View;

abstract class IsWidgetPlugin extends IsGenericPlugin
{
    abstract public function getWidgetView(): View;

    abstract public function getWidth(): int;
}
