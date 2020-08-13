<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Voyager\Admin\Manager\Menu;

interface ProvidePublicRoutes
{
    public function providePublicRoutes(): void;
}