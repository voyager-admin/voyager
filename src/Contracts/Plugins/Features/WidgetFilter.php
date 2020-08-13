<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Illuminate\Support\Collection;

interface WidgetFilter
{
    public function filterWidgets($widgets): Collection;
}