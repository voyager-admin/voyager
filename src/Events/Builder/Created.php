<?php

namespace Voyager\Admin\Events\Builder;

use Voyager\Admin\Classes\Bread;

class Created
{
    public $bread;

    /**
     * Create a new event instance.
     *
     * @param  Voyager\Admin\Classes\Bread  $bread
     * @return void
     */
    public function __construct(Bread $bread)
    {
        $this->bread = $bread;
    }
}
