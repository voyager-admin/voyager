<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\Support\Collection;

interface RegistersLayoutFilter
{
    public function filterLayouts($bread, $action, $layouts): Collection;
}