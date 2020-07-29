<?php

namespace Voyager\Admin\Contracts\Plugins;

use Illuminate\View\View;

/**
 * @property-read string $name          The name of this voyager plugin.
 * @property-read string $description   A short description of this plugins function.
 * @property-read string $repository    The packagist package name for this plugin.
 * @property-read string $website       The public URL to this plugins repository.
 * @property-read string $version       The plugins current package version.
 */
interface GenericPlugin
{
    public function registerProtectedRoutes();

    public function registerPublicRoutes();

    public function getSettingsView(): ?View;

    public function getInstructionsView(): ?View;

    public function getCssRoutes(): array;

    public function getJsRoutes(): array;
}
