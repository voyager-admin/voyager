<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\View\View;

interface WidgetPlugin extends GenericPlugin
{
    public function getWidgetView(): View;

    public function getWidth(): int;

    public function getTitle(): ?string;

    public function getIcon(): ?string;
}
