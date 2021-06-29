<?php

namespace Voyager\Admin\Manager;

use Composer\InstalledVersions;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Voyager\Admin\Contracts\Plugins\GenericPlugin;
use Voyager\Admin\Contracts\Plugins\Features\Provider\CSS as CSSProvider;
use Voyager\Admin\Contracts\Plugins\Features\Provider\FrontendRoutes;
use Voyager\Admin\Contracts\Plugins\Features\Provider\JS as JSProvider;
use Voyager\Admin\Contracts\Plugins\Features\Provider\MenuItems;
use Voyager\Admin\Contracts\Plugins\Features\Provider\ProtectedRoutes;
use Voyager\Admin\Contracts\Plugins\Features\Provider\PublicRoutes;
use Voyager\Admin\Contracts\Plugins\Features\Provider\Settings as SettingsProvider;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Voyager;

class Plugins
{
    protected $menumanager;
    protected $settingsmanager;
    protected $plugins;
    protected $enabled_plugins;
    protected $path;
    protected $preferences_changed = false;

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
            throw new \Exception('Plugin added to Voyager has to inherit GenericPlugin');
        }

        $plugin->identifier = $plugin->repository.'@'.class_basename($plugin);
        $plugin->enabled = array_key_exists($plugin->identifier, $this->enabled_plugins);
        $plugin->version = InstalledVersions::getPrettyVersion($plugin->repository);
        $plugin->version_normalized = InstalledVersions::getVersion($plugin->repository);

        $plugin->preferences = new class ($plugin, $this) {
            private $plugin, $pluginmanager;

            public function __construct(GenericPlugin $plugin, Plugins $pluginmanager) {
                $this->plugin = $plugin;
                $this->pluginmanager = $pluginmanager;
            }

            public function set($key, $value, $locale = null) {
                return $this->pluginmanager->setPreference($this->plugin->identifier, $key, $value, $locale);
            }

            public function get($key, $default = null, $translate = true) {
                return $this->pluginmanager->getPreference($this->plugin->identifier, $key, $default, $translate);
            }

            public function remove($key) {
                return $this->pluginmanager->removePreference($this->plugin->identifier, $key);
            }

            public function removeAll() {
                return $this->pluginmanager->removeAllPreferences($this->plugin->identifier);
            }
        };
        
        $plugin->num = $this->plugins->count();
        $this->plugins->push($plugin);
    }

    public function launchPlugins($protected = false, $public = false)
    {
        $this->getAllPlugins(false)->each(function ($plugin) use ($protected, $public) {
            if ($plugin->enabled) {
                if ($protected) {
                    if ($plugin instanceof ProtectedRoutes) {
                        $plugin->provideProtectedRoutes();
                    }
                } elseif ($public) {
                    if ($plugin instanceof FrontendRoutes) {
                        $plugin->provideFrontendRoutes();
                    }
                } else {
                    if ($plugin->enabled) {
                        // Register menu items
                        if ($plugin instanceof MenuItems) {
                            $plugin->provideMenuItems($this->menumanager);
                        }
                        // Merge settings
                        if ($plugin instanceof SettingsProvider) {
                            $this->settingsmanager->merge($plugin->registerSettings());
                        }
                    }
                }
            }
        });
    }

    public function loadEnabledPlugins()
    {
        $this->enabled_plugins = [];

        VoyagerFacade::ensureFileExists($this->path, '[]');

        collect(VoyagerFacade::getJson(File::get($this->path), []))->where('enabled')->each(function ($plugin) {
            $this->enabled_plugins[$plugin->identifier] = [
                'preferences'   => (array) ($plugin->preferences ?? []),
            ];
        });
    }

    public function getAllPlugins($enabled = true): Collection
    {
        if ($enabled) {
            return $this->plugins->where('enabled');
        }

        return $this->plugins;
    }

    public function enablePlugin($identifier, $enable = true)
    {
        $found = false;
        $this->getAllPlugins(false)->each(function ($plugin) use (&$found, $identifier) {
            if ($plugin->identifier == $identifier) {
                $found = true;
            }
        });

        if (!$found) {
            throw new \Exception('Plugin with identifier "'.$identifier.'" is not registered and can not be enabled/disabled!');
        }

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

    public function getAssets()
    {
        $assets = collect();
        $this->getAllPlugins(false)->each(function ($plugin) use ($assets) {
            if ($plugin instanceof CSSProvider) {
                $assets->push([
                    'name'      => Str::slug($plugin->name).'.css',
                    'content'   => $plugin->provideCSS()
                ]);
            }
            if ($plugin instanceof JSProvider && $plugin->enabled) {
                $assets->push([
                    'name'      => Str::slug($plugin->name).'.js',
                    'content'   => $plugin->provideJS()
                ]);
            }
        });

        return $assets;
    }

    public function setPreference($identifier, $key, $value, $locale = null)
    {
        if (!is_null($locale)) {
            $value = VoyagerFacade::setTranslation(
                $this->enabled_plugins[$identifier]['preferences'][$key],
                $value,
                $locale
            );
        }
        
        $this->enabled_plugins[$identifier]['preferences'][$key] = $value;
        $this->preferences_changed = true;
    }

    public function getPreference($identifier, $key, $default = null, $translate = true)
    {
        $value = $this->enabled_plugins[$identifier]['preferences'][$key] ?? $default;
        if ($translate !== false) {
            return VoyagerFacade::translate($value, ($translate === true ? null : $translate));
        }

        return $value;
    }

    public function getPreferences($identifier)
    {
        return $this->enabled_plugins[$identifier]['preferences'] ?? [];
    }

    public function removePreference($identifier, $key): bool
    {
        // TODO: Make sure everything exists
        unset($this->enabled_plugins[$identifier]['preferences'][$key]);
        $this->preferences_changed = true;

        return true;
    }

    public function removeAllPreferences($identifier): bool
    {
        // TODO: Make sure everything exists
        unset($this->enabled_plugins[$identifier]['preferences']);
        $this->preferences_changed = true;

        return true;
    }

    public function __destruct()
    {
        if ($this->preferences_changed) {
            $plugins = collect(VoyagerFacade::getJson(File::get($this->getPath()), []))->transform(function ($plugin) {
                $plugin->preferences = $this->enabled_plugins[$plugin->identifier]['preferences'] ?? [];

                return $plugin;
            });

            File::put($this->getPath(), json_encode($plugins, JSON_PRETTY_PRINT));
        }
    }
}