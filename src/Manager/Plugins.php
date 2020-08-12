<?php

namespace Voyager\Admin\Manager;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Voyager\Admin\Contracts\Plugins\GenericPlugin;
use Voyager\Admin\Contracts\Plugins\HasFrontendFeatures;
use Voyager\Admin\Contracts\Plugins\RegistersMenuItems;
use Voyager\Admin\Contracts\Plugins\RegistersSettings;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Plugins
{
    protected $menumanager;
    protected $settingsmanager;
    protected $plugins;
    protected $enabled_plugins;
    protected $path;

    /**
     * @var bool
     */
    protected $plugins_loaded = false;

    /**
     * @var bool
     */
    protected $routes_registered = false;

    /**
     * @var bool
     */
    protected $frontend_routes_registered = false;

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

    public function getAllPlugins($enabled = true): Collection
    {
        $plugins = $this->plugins;
        if ($enabled) {
            $plugins = $plugins->where('enabled');
        }

        return $plugins;
    }

    public function launchPlugins(): void
    {
        $this->getAllPlugins(false)->filter(static function ($plugin) {
            return $plugin->enabled || $plugin->type === 'theme';
        })->each(function ($plugin) {
            if ($plugin instanceof RegistersMenuItems) {
                $plugin->registerMenuItems($this->menumanager);
            }
            if ($plugin instanceof RegistersSettings) {
                $this->settingsmanager->mergeSettings($plugin->registerSettings());
            }
        });
        $this->plugins_loaded = true;
    }

    public function registerRoutes(): void
    {
        $this->getAllPlugins()->filter(static function ($plugin) {
            return $plugin->enabled || $plugin->type === 'theme';
        })->each(static function ($plugin) {
            $plugin->registerPublicRoutes();
            Route::group(['middleware' => 'voyager.admin'], static function () use ($plugin) {
                $plugin->registerProtectedRoutes();
            });
        });
        $this->routes_registered = true;
    }

    public function registerFrontendRoutes(): void
    {
        $this->getAllPlugins()->filter(static function ($plugin) {
            return $plugin->enabled && $plugin instanceof HasFrontendFeatures;
        })->each(static function ($plugin) {
            Route::group(['middleware' => 'web'], static function () use ($plugin) {
                $plugin->registerFrontendRoutes();
            });
        });
        $this->frontend_routes_registered = true;
    }

    public function getPluginByType($type, $fallback = null, $enabled = true)
    {
        $plugin = $this->getPluginsByType($type, $enabled)->first();

        if (!$plugin && $fallback !== null) {
            $plugin = $fallback;
            if (!($fallback instanceof GenericPlugin)) {
                $plugin = new $fallback();
            }
        }

        return $plugin;
    }

    public function getPluginsByType($type, $enabled = true)
    {
        return $this->getAllPlugins($enabled)->where('type', $type);
    }

    public function getAvailablePlugins()
    {
        return VoyagerFacade::getJson(File::get(dirname(__DIR__, 2) . '/plugins.json'), []);
    }

    public function enablePlugin($identifier, $enable = true)
    {
        $this->getAllPlugins(false);

        $plugins = collect(VoyagerFacade::getJson(File::get($this->getPath()), []));
        if (!$plugins->contains('identifier', $identifier)) {
            $plugins->push([
                'identifier' => $identifier,
                'enabled'    => $enable,
            ]);
        } else {
            $plugins->where('identifier', $identifier)->first()->enabled = $enable;
        }

        return File::put($this->getPath(), json_encode($plugins, JSON_PRETTY_PRINT));
    }

    public function disablePlugin($identifier)
    {
        return $this->enablePlugin($identifier, false);
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