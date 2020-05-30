<?php

namespace Voyager\Admin\Contracts\Plugins;

use Voyager\Admin\Classes\Formfield;

interface FormfieldPlugin extends GenericPlugin
{
    public function getFormfield(): Formfield;
}
