<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Plugins as PluginManager;

class PluginsController extends Controller
{
    protected $pluginmanager;

    public function __construct(PluginManager $pluginmanager)
    {
        $this->pluginmanager = $pluginmanager;
    }

    public function index()
    {
        return view('voyager::plugins.browse');
    }

    public function enable(Request $request)
    {
        $identifier = $request->get('identifier');
        if ($request->get('enable', false)) {
            return $this->enablePlugin($identifier);
        }

        return $this->enablePlugin($identifier, false);
    }

    public function get()
    {
        return $this->pluginmanager->getAllPlugins()->sortBy('identifier')->transform(function ($plugin) {
            // This is only used to preview a theme
            if ($plugin->type == 'theme') {
                $plugin->src = $plugin->getCssRoutes();
            }

            return $plugin;
        })->values();
    }

    public function settings($key)
    {
        $plugin = $this->pluginmanager->getAllPlugins()->get($key);
        if (!$plugin) {
            throw new \Voyager\Admin\Exceptions\PluginNotFoundException('This Plugin does not exist');
        } elseif ($plugin->has_settings && $plugin->enabled) {
            return $plugin->getSettingsView();
        }

        return redirect()->back();
    }

    private function enablePlugin($identifier, $enable = true)
    {
        $this->pluginmanager->getAllPlugins();

        $plugins = collect(VoyagerFacade::getJson(File::get($this->pluginmanager->getPath()), []));
        if (!$plugins->contains('identifier', $identifier)) {
            $plugins->push([
                'identifier' => $identifier,
                'enabled'    => $enable,
            ]);
        } else {
            $plugins->where('identifier', $identifier)->first()->enabled = $enable;
        }

        return File::put($this->pluginmanager->getPath(), json_encode($plugins, JSON_PRETTY_PRINT));
    }
}