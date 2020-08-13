<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Provider;

use Voyager\Admin\Manager\Menu;

interface MenuItems
{
    public function provideMenuItems(Menu $menuManager): void;
}