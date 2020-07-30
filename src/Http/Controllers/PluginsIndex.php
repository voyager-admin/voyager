<?php

namespace Voyager\Admin\Http\Controllers;

class PluginsIndex
{
    public function __invoke()
    {
        return view('voyager::plugins.browse');
    }
}