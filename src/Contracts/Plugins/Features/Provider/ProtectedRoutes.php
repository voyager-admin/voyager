<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Provider;

use Voyager\Admin\Manager\Menu;

interface ProtectedRoutes
{
    public function provideProtectedRoutes(): void;
}