<?php

namespace Voyager\Admin\Facades;

use Illuminate\Support\Facades\Facade;

class Bread extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bread';
    }
}
