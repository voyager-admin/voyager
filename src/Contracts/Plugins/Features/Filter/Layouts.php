<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Filter;

use Illuminate\Support\Collection;
use Voyager\Admin\Classes\Bread;

/**
 * An interface for plugins that want to filter layouts for BREADs.
 */
interface Layouts
{    
    /**
    * Filter layouts for a BREAD.
    *
    * @param Bread $bread        The BREAD class
    * @param string $action      The action (browse, read, edit or add)
    * @param Collection $layouts A Collection containing all possible candidates.
    *
    * @return Collection The filtered layouts
    */
    public function filterLayouts(Bread $bread, string $action, Collection $layouts): Collection;
}