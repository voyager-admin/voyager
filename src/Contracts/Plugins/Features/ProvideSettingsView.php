<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Illuminate\View\View;

interface ProvideSettingsView
{
    public function getSettingsView(): View;
}