<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Illuminate\Support\Collection;

interface MenuItemFilter
{
    public function filterMenuItems(Collection $items): Collection;
}