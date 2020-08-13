<?php

namespace Voyager\Admin\Contracts\Plugins;

use Voyager\Admin\Contracts\Plugins\Features\Provider\CSS;
use Voyager\Admin\Contracts\Plugins\Features\Provider\PublicRoutes;

interface ThemePlugin extends GenericPlugin, CSS, PublicRoutes
{
}
