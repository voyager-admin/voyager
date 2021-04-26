<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Voyager\Admin\Contracts\Plugins\ThemePlugin;
use Voyager\Admin\Contracts\Plugins\Features\Provider\InstructionsComponent;
use Voyager\Admin\Contracts\Plugins\Features\Provider\SettingsComponent;
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
        return $this->inertiaRender('Plugins', [
            'title'             => __('voyager::plugins.plugins'),
            'available-plugins' => $this->pluginmanager->getAvailablePlugins()
        ]);
    }

    public function enable(Request $request)
    {
        $identifier = $request->get('identifier');
        if ($request->get('enable', false)) {
            return $this->pluginmanager->enablePlugin($identifier);
        }

        return $this->pluginmanager->enablePlugin($identifier, false);
    }

    public function get()
    {
        return $this->pluginmanager->getAllPlugins(false)->sortBy('identifier')->transform(function ($plugin) {
            $plugin->type = collect(class_implements($plugin))->filter(static function ($interface) {
                return Str::startsWith($interface, 'Voyager\\Admin\\Contracts\\Plugins\\') && Str::endsWith($interface, 'Plugin');
            })->transform(static function ($interface) {
                return strtolower(str_replace(['Voyager\\Admin\\Contracts\\Plugins\\', 'Plugin'], '', $interface));
            })->first();

            if ($plugin instanceof SettingsComponent) {
                $plugin->settings_component = $plugin->getSettingsComponent();
            }
            if ($plugin instanceof InstructionsComponent) {
                $plugin->instructions_component = $plugin->getInstructionsComponent();
            }

            return $plugin;
        })->values();
    }
}