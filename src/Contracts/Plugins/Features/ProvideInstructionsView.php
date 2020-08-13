<?php

namespace Voyager\Admin\Contracts\Plugins\Features;

use Illuminate\View\View;

interface ProvideInstructionsView
{
    public function getInstructionsView(): View;
}