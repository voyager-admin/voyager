<?php

namespace Voyager\Admin\Manager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Voyager\Admin\Contracts\Plugins\GenericPlugin;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Plugins
{
    protected $menumanager;
    protected $settingsmanager;
    protected $plugins;
    protected $enabled_plugins;
    protected $path;

    public function __construct(Menu $menumanager, Settings $settingsmanager)
    {
        $this->menumanager = $menumanager;
        $this->settingsmanager = $settingsmanager;
        $this->plugins = collect();
        $this->path = Str::finish(storage_path('voyager'), '/').'plugins.json';
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function addPlugin($plugin = null)
    {
        if (!$this->enabled_plugins) {
            $this->loadEnabledPlugins();
        }
        if ($plugin) {
            if (!is_object($plugin)) {
                $plugin = new $plugin();
            }
            $plugin->type = $this->getPluginType($plugin);

            $plugin->identifier = $plugin->repository.'@'.class_basename($plugin);
            $plugin->enabled = in_array($plugin->identifier, $this->enabled_plugins, true);
            if ($plugin->getInstructionsView()) {
                $plugin->instructions = $plugin->getInstructionsView()->render();
            }
            $plugin->has_settings = !is_null($plugin->getSettingsView());
            $plugin->num = $this->plugins->count();
            $this->plugins->push($plugin);
        }
    }

    public function loadEnabledPlugins()
    {
        $this->enabled_plugins = [];

        VoyagerFacade::ensureFileExists($this->path, '[]');

        collect(VoyagerFacade::getJson(File::get($this->path), []))->where('enabled')->each(function ($plugin) {
            $this->enabled_plugins[] = $plugin->identifier;
        });
    }

    public function getAllPlugins()
    {
        return collect($this->plugins);
    }

    public function launchPlugins()
    {
        $this->getAllPlugins()->each(function ($plugin) {
            if ($plugin->enabled || $plugin->type === 'theme') {
                $plugin->registerPublicRoutes();
                if (method_exists($plugin, 'registerMenuItems')) {
                    $plugin->registerMenuItems($this->menumanager);
                }
                if (method_exists($plugin, 'registerSettings')) {
                    $settings = $plugin->registerSettings();
                    if (is_array($settings)) {
                        $this->settingsmanager->mergeSettings($settings);
                    }
                }
                Route::group(['middleware' => 'voyager.admin'], function () use ($plugin) {
                    $plugin->registerProtectedRoutes();
                });
            }
        });
    }

    public function getPluginByType($type, $fallback = null)
    {
        $plugin = $this->getPluginsByType($type)->where('enabled')->first();
        if (!$plugin && $fallback !== null) {
            $plugin = $fallback;
            if (!($fallback instanceof GenericPlugin)) {
                $plugin = new $fallback();
            }
        }

        return $plugin;
    }

    public function getPluginsByType($type)
    {
        return $this->getAllPlugins()->where('type', $type);
    }

    public function getAvailablePlugins()
    {
        return VoyagerFacade::getJson(File::get(dirname(__DIR__, 2) . '/plugins.json'), []);
    }

    protected function getPluginType($class)
    {
        return collect(class_implements($class))->filter(static function ($interface) {
            return Str::startsWith($interface, 'Voyager\\Admin\\Contracts\\Plugins\\');
        })->transform(static function ($interface) {
            return strtolower(str_replace(['Voyager\\Admin\\Contracts\\Plugins\\', 'Plugin'], '', $interface));
        })->first();
    }
}