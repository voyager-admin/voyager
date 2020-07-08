<?php

namespace Voyager\Admin\Classes;

use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Media
{
    public function __construct($input)
    {
        foreach ($input as $key => $value) {
            if ($key == 'meta') {
                foreach ($value as $meta_key => $meta_value) {
                    $this->{$meta_key} = VoyagerFacade::translate($meta_value);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }
}