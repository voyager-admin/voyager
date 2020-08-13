<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Filter;

use Illuminate\Support\Collection;

/**
 * An interface for plugins that want to filter menu items.
 */
interface MenuItems
{
    /**
    * Filter menu items.
    *
    * @param Collection $items The menu items
    *
    * @return Collection The filtered menu items
    */
    public function filterMenuItems(Collection $items): Collection;
}