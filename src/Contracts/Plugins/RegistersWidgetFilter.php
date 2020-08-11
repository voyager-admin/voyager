<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\Support\Collection;

interface RegistersWidgetFilter
{
    public function filterWidgets($widgets): Collection;
}