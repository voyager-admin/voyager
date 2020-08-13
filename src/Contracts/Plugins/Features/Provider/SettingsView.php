<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Provider;

use Illuminate\View\View;

interface SettingsView
{
    public function getSettingsView(): View;
}