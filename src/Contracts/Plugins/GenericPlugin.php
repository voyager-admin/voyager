<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\View\View;

interface GenericPlugin
{
    public function registerProtectedRoutes();

    public function registerPublicRoutes();

    public function getSettingsView(): ?View;

    public function getInstructionsView(): ?View;

    public function getCssRoutes(): array;

    public function getJsRoutes(): array;
}
