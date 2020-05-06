<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\View\View;

abstract class IsGenericPlugin
{
    public function registerProtectedRoutes()
    {
        return;
    }

    public function registerPublicRoutes()
    {
        return;
    }

    public function getSettingsView(): ?View
    {
        return null;
    }

    public function getInstructionsView(): ?View
    {
        return null;
    }
}
