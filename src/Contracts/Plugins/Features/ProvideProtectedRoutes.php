<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Voyager\Admin\Manager\Menu;

interface ProvideProtectedRoutes
{
    public function provideProtectedRoutes(): void;
}