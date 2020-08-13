<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Filter;

use Illuminate\Support\Collection;

interface Widgets
{
    public function filterWidgets($widgets): Collection;
}