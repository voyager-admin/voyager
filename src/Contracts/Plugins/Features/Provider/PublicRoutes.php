<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Provider;

use Voyager\Admin\Manager\Menu;

interface PublicRoutes
{
    public function providePublicRoutes(): void;
}