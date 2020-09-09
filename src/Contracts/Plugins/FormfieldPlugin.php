<?php

namespace Voyager\Admin\Contracts\Plugins;

use Voyager\Admin\Classes\Formfield;
use Voyager\Admin\Contracts\Plugins\Features\Provider\JS;

interface FormfieldPlugin extends GenericPlugin, JS
{
    public function getFormfield(): Formfield;
}
