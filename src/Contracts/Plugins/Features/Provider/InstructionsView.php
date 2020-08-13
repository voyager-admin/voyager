<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Provider;

use Illuminate\View\View;

interface InstructionsView
{
    public function getInstructionsView(): View;
}