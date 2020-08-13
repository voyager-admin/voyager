<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Illuminate\Support\Collection;

interface LayoutFilter
{
    public function filterLayouts($bread, $action, $layouts): Collection;
}