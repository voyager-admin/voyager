<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\View\View;

interface IsGenericPlugin
{
    public function registerProtectedRoutes();

    public function registerPublicRoutes();

    public function getSettingsView(): ?View;

    public function getInstructionsView(): ?View;
}
