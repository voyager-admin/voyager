<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Voyager\Admin\Manager\Menu;

interface ProvideMenuItems
{
    public function provideMenuItems(Menu $menuManager): array;
}