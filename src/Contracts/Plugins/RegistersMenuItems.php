<?php

namespace Voyager\Admin\Contracts\Plugins;

use Voyager\Admin\Manager\Menu;

interface RegistersMenuItems
{
    public function registerMenuItems(Menu $menuManager): array;
}