<?php

namespace Voyager\Admin\Manager;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Voyager\Admin\Contracts\Plugins\GenericPlugin;
use Voyager\Admin\Contracts\Plugins\Features\ProvideFrontendRoutes;
use Voyager\Admin\Contracts\Plugins\Features\ProvideInstructionsView;
use Voyager\Admin\Contracts\Plugins\Features\ProvideMenuItems;
use Voyager\Admin\Contracts\Plugins\Features\ProvideProtectedRoutes;
use Voyager\Admin\Contracts\Plugins\Features\ProvidePublicRoutes;
use Voyager\Admin\Contracts\Plugins\Features\ProvideSettings;
use Voyager\Admin\Contracts\Plugins\Features\ProvideSettingsView;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Voyager;

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

    public function addPlugin($plugin)
    {
        if (!$this->enabled_plugins) {
            $this->loadEnabledPlugins();
        }
        if (is_string($plugin)) {
            $plugin = new $plugin();
        }

        if (!($plugin instanceof GenericPlugin)) {
            throw new \Exception('Plugin added to Voyager has to extend GenericPlugin');
        }

        $plugin->type = $this->getPluginType($plugin);

        $plugin->identifier = $plugin->repository.'@'.class_basename($plugin);
        $plugin->enabled = in_array($plugin->identifier, $this->enabled_plugins, true);
        if ($plugin instanceof ProvideInstructionsView) {
            $plugin->instructions = $plugin->getInstructionsView()->render();
        }

        if ($plugin->enabled || $plugin->type == 'theme') {
            if ($plugin->enabled) {
                // Register menu items
                if ($plugin instanceof ProvideMenuItems) {
                    $plugin->registerMenuItems($this->menumanager);
                }
                // Merge settings
                if ($plugin instanceof ProvideSettings) {
                    $this->settingsmanager->mergeSettings($plugin->registerSettings());
                }
                // Provide frontend routes
                if ($plugin instanceof ProvideFrontendRoutes) {
                    $plugin->provideFrontendRoutes();
                }
    
                // Provide protected routes under voyager namespace
                if ($plugin instanceof ProvideProtectedRoutes) {
                    Route::group(['as' => 'voyager.', 'prefix' => Voyager::$routePath, 'middleware' => 'voyager.admin'], function () use ($plugin) {
                        $plugin->provideProtectedRoutes();
                    });
                }
            }
            // Theme plugins need to register their routes so it can be previewed
            if ($plugin instanceof ProvidePublicRoutes) {
                Route::group(['as' => 'voyager.', 'prefix' => Voyager::$routePath], function () use ($plugin) {
                    $plugin->providePublicRoutes();
                });
            }
        }

        $plugin->has_settings = ($plugin instanceof ProvideSettingsView);
        
        $plugin->num = $this->plugins->count();
        $this->plugins->push($plugin);
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
            return Str::startsWith($interface, 'Voyager\\Admin\\Contracts\\Plugins\\') && Str::endsWith($interface, 'Plugin');
        })->transform(static function ($interface) {
            return strtolower(str_replace(['Voyager\\Admin\\Contracts\\Plugins\\', 'Plugin'], '', $interface));
        })->first();
    }
}