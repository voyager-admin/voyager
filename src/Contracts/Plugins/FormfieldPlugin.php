<?php

namespace Voyager\Admin\Contracts\Plugins;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Contracts\Plugins\Features\Provider\JS;
use Voyager\Admin\Contracts\Plugins\Features\Provider\PublicRoutes;

interface FormfieldPlugin extends GenericPlugin, JS, PublicRoutes
{
    public function getFormfield(): Formfield;
}
