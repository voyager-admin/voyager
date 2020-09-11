<?php

namespace Voyager\Admin\Manager;

use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Voyager\Admin\Contracts\Plugins\GenericPlugin;
use Voyager\Admin\Contracts\Plugins\Features\Provider\CSS as CSSProvider;
use Voyager\Admin\Contracts\Plugins\Features\Provider\FrontendRoutes;
use Voyager\Admin\Contracts\Plugins\Features\Provider\InstructionsView;
use Voyager\Admin\Contracts\Plugins\Features\Provider\JS as JSProvider;
use Voyager\Admin\Contracts\Plugins\Features\Provider\MenuItems;
use Voyager\Admin\Contracts\Plugins\Features\Provider\ProtectedRoutes;
use Voyager\Admin\Contracts\Plugins\Features\Provider\PublicRoutes;
use Voyager\Admin\Contracts\Plugins\Features\Provider\Settings as SettingsProvider;
use Voyager\Admin\Contracts\Plugins\Features\Provider\SettingsView;
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
            throw new \Exception('Plugin added to Voyager has to extend GenericPlugin');
        }

        $plugin->type = $this->getPluginType($plugin);

        $plugin->identifier = $plugin->repository.'@'.class_basename($plugin);
        $plugin->enabled = array_key_exists($plugin->identifier, $this->enabled_plugins);
        if ($plugin instanceof InstructionsView) {
            $plugin->instructions = $plugin->getInstructionsView()->render();
        }

        $plugin->has_settings = ($plugin instanceof SettingsView);
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
        };
        
        $plugin->num = $this->plugins->count();
        $this->plugins->push($plugin);
    }

    public function launchPlugins($protected = false, $public = false)
    {
        $this->getAllPlugins(false)->each(function ($plugin) use ($protected, $public) {
            if ($plugin->enabled || $plugin->type == 'theme') {
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
                    // Theme plugins need to register their routes so it can be previewed
                    if ($plugin instanceof PublicRoutes) {
                        $plugin->providePublicRoutes();
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

    protected function getPluginType($class)
    {
        return collect(class_implements($class))->filter(static function ($interface) {
            return Str::startsWith($interface, 'Voyager\\Admin\\Contracts\\Plugins\\') && Str::endsWith($interface, 'Plugin');
        })->transform(static function ($interface) {
            return strtolower(str_replace(['Voyager\\Admin\\Contracts\\Plugins\\', 'Plugin'], '', $interface));
        })->first();
    }

    public function getAssets()
    {
        return $this->getAllPlugins(false)->filter(function ($plugin) {
            return $plugin instanceof CSSProvider || ($plugin instanceof JSProvider && $plugin->enabled);
        })->transform(function ($plugin) {
            return [
                'name'      => Str::slug($plugin->name).($plugin instanceof CSSProvider ? '.css' : '.js'),
                'content'   => ($plugin instanceof CSSProvider ? $plugin->provideCSS() : $plugin->provideJS())
            ];
        });
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