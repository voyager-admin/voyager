<?php

namespace Voyager\Admin\Contracts\Plugins;

/**
 * An interface for plugins that want to expand their functionality to frontend route space.
 *
 * These URLs would not be registered under the default Voyager routes. Instead these are created on
 * the app routes. A good example for utilizing this interface would be a "Voyager Blog" plugin that registers
 * the front-end (read as 'end user') URL routes.
 *
 * @package Voyager\Admin\Contracts\Plugins
 */
interface HasFrontendFeatures
{
    /**
     * Registers the plugin's frontend routes.
     *
     * @return void
     */
    public function registerFrontendRoutes();
}