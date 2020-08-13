<?php

namespace Voyager\Admin\Contracts\Plugins;

use Voyager\Admin\Contracts\Plugins\Features\ProvideCSS;
use Voyager\Admin\Contracts\Plugins\Features\ProvidePublicRoutes;

interface ThemePlugin extends GenericPlugin, ProvideCSS, ProvidePublicRoutes
{
}
