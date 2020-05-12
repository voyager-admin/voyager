<?php

namespace Voyager\Admin\Manager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Voyager\Admin\Contracts\Plugins\GenericPlugin;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Plugins
{
    protected $plugins;
    protected $enabled_plugins;
    protected $path;

    public function __construct()
    {
        $this->plugins = collect();
        $this->path = Str::finish(storage_path('voyager'), '/').'plugins.json';
    }

    public function setPath($path)
    {
        $this->path = $path;
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
            $plugin->enabled = in_array($plugin->identifier, $this->enabled_plugins);
            if ($plugin->getInstructionsView()) {
                $plugin->instructions = $plugin->getInstructionsView()->render();
            }
            $plugin->has_settings = !is_null($plugin->getSettingsView());
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
            if ($plugin->enabled || $plugin->type == 'theme') {
                $plugin->registerPublicRoutes();
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
            if (!($fallback instanceof IsGenericPlugin)) {
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
        return VoyagerFacade::getJson(File::get(realpath(__DIR__.'/../../plugins.json')), []);
    }

    protected function getPluginType($class)
    {
        return collect(class_implements($class))->filter(function ($interface) {
            return Str::startsWith($interface, 'Voyager\\Admin\\Contracts\\Plugins\\');
        })->transform(function ($interface) {
            return strtolower(str_replace(['Voyager\\Admin\\Contracts\\Plugins\\', 'Plugin'], '', $interface));
        })->first();
    }
}