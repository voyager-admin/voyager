<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Voyager\Admin\Facades\Plugins as PluginsFacade;

class PluginsController extends Controller
{
    public function enable(Request $request)
    {
        $identifier = $request->get('identifier');
        if ($request->get('enable', false)) {
            return PluginsFacade::enablePlugin($identifier);
        }

        return PluginsFacade::disablePlugin($identifier);
    }

    public function get()
    {
        return PluginsFacade::getAllPlugins()->sortBy('identifier')->transform(function ($plugin) {
            // This is only used to preview a theme
            if ($plugin->type == 'theme') {
                $plugin->src = $plugin->getStyleRoute();
            }

            return $plugin;
        });
    }

    public function settings($key)
    {
        $plugin = PluginsFacade::getAllPlugins()->get($key);
        if (!$plugin) {
            throw new \Voyager\Admin\Exceptions\PluginNotFoundException('This Plugin does not exist');
        } elseif ($plugin->has_settings && $plugin->enabled) {
            return $plugin->getSettingsView();
        }

        return redirect()->back();
    }
}
