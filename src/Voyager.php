<?php

namespace Voyager\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Manager\Settings as SettingManager;
use Voyager\Admin\Plugins\AuthenticationPlugin;

class Voyager
{
    protected $messages = [];
    protected $tables = [];
    protected $breadmanager;
    protected $pluginmanager;
    protected $settingmanager;

    public function __construct(BreadManager $breadmanager, PluginManager $pluginmanager, SettingManager $settingmanager)
    {
        $this->breadmanager = $breadmanager;
        $this->pluginmanager = $pluginmanager;
        $this->settingmanager = $settingmanager;
    }

    /**
     * Get Voyagers routes.
     *
     * @return array an array of routes
     */
    public function routes()
    {
        $this->pluginmanager->launchPlugins();
        require __DIR__.'/../routes/voyager.php';
    }

    /**
     * Generate an absolute URL for an asset-file.
     *
     * @param string $path the relative path, e.g. js/voyager.js
     *
     * @return string
     */
    public function assetUrl($path)
    {
        return route('voyager.voyager_assets').'?path='.urlencode($path);
    }

    /**
     * Flash a message to the UI.
     *
     * @param string $message The message
     * @param string $color   The tailwind color of the exception: blue, yellow, green, red...
     * @param bool   $next    If the message should be flashed after the next request
     */
    public function flashMessage($message, $color, $timeout = 5000, $next = false)
    {
        $this->messages[] = [
            'message' => $message,
            'color'   => $color,
            'timeout' => $timeout,
        ];
        if ($next) {
            session()->push('voyager-messages', [
                'message' => $message,
                'color'   => $color,
                'timeout' => $timeout,
            ]);
        }
    }

    /**
     * Get all messages.
     *
     * @return array The messages
     */
    public function getMessages()
    {
        $messages = array_merge($this->messages, session()->get('voyager-messages', []));
        session()->forget('voyager-messages');

        return collect($messages)->unique();
    }

    /**
     * Get all Voyager translation strings.
     *
     * @return array The language strings
     */
    public function getLocalization()
    {
        return collect(['auth', 'bread', 'builder', 'formfields', 'generic', 'media', 'plugins', 'settings', 'validation', 'wysiwyg'])->flatMap(function ($file) {
            return ['voyager::'.$file => trans('voyager::'.$file)];
        })->toJson();
    }

    /**
     * Get all Routes.
     *
     * @return array The routes
     */
    public function getRoutes()
    {
        return collect(\Route::getRoutes())->mapWithKeys(function ($route) {
            return [$route->getName() => url($route->uri())];
        })->filter(function ($value, $key) {
            return $key != '';
        });
    }

    /**
     * Get all tables in the database.
     *
     * @return array
     */
    public function getTables()
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }

    public function getColumns($table)
    {
        if (!array_key_exists($table, $this->tables)) {
            $builder = DB::getSchemaBuilder();
            $this->tables[$table] = $builder->getColumnListing($table);
        }

        return $this->tables[$table];
    }

    /**
     * Get all locales supported by the app.
     *
     * @return array The locales
     */
    public function getLocales()
    {
        return config('app.locales', [$this->getLocale()]);
    }

    /**
     * Get the current app-locale.
     *
     * @return string The current locale
     */
    public function getLocale()
    {
        return app()->getLocale();
    }

    /**
     * Get the app fallback-locale.
     *
     * @return string The fallback locale
     */
    public function getFallbackLocale()
    {
        return config('app.fallback_locale', [$this->getLocale()]);
    }

    /**
     * Get if the app is translatable or not.
     *
     * @return bool
     */
    public function isTranslatable()
    {
        return count($this->getLocales()) > 1;
    }

    /**
     * Gets all widgets from installed and enabled plugins.
     *
     * @return Collection The widgets
     */
    public function getWidgets()
    {
        return collect($this->pluginmanager->getPluginsByType('widget')->where('enabled')->transform(function ($plugin) {
            $width = $plugin->getWidth();
            if ($width >= 1 && $width <= 11) {
                $width = 'w-'.$width.'/12';
            } else {
                $width = 'w-full';
            }

            return (object) [
                'width' => $width,
                'title' => $plugin->getTitle(),
                'icon'  => $plugin->getIcon(),
                'view'  => $plugin->getWidgetView(),
            ];
        }));
    }

    /**
     * Translate a given string/object/array.
     *
     * @return string The translated value
     */
    public function translate($value, $locale = null, $fallback = null)
    {
        if ($locale == null) {
            $locale = app()->getLocale();
        }
        if ($fallback == null) {
            $fallback = config('app.fallback_locale');
        }

        if (is_string($value)) {
            $json = $this->getJson($value);
            if (($json = $this->getJson($value)) === false) {
                return $value;
            } else {
                $value = $json;
            }
        }

        if (is_array($value)) {
            return $value[$locale] ?? $value[$fallback] ?? null;
        } elseif (is_object($value)) {
            return $value->{$locale} ?? $value->{$fallback} ?? null;
        }

        return $value;
    }

    public function setting($key = null, $default = null, $translate = true)
    {
        return $this->settingmanager->setting($key, $default, $translate);
    }

    public function getJson($input, $default = false)
    {
        $json = @json_decode($input);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $json;
        }

        return $default;
    }

    public function setBreadPath($path)
    {
        $this->breadmanager->setPath($path);
    }

    public function setPluginsPath($path)
    {
        $this->pluginmanager->setPath($path);
    }

    public function setSettingsPath($path)
    {
        $this->settingmanager->setPath($path);
    }

    public function auth()
    {
        return $this->pluginmanager->getPluginByType('authentication', AuthenticationPlugin::class);
    }

    public function ensureDirectoryExists($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    public function ensureFileExists($path, $content = '')
    {
        $this->ensureDirectoryExists(dirname($path));
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
    }

    public function authorize($user, $ability, $arguments = [])
    {
        $auth_plugins = $this->pluginmanager->getPluginsByType('authorization')->where('enabled');
        $authorized = true;
        $auth_plugins->each(function ($plugin) use ($user, $ability, $arguments, &$authorized) {
            if (!$plugin->authorize($user, $ability, $arguments)) {
                $authorized = false;
            }
        });
        return $authorized;
    }

    public function getThumbnailDefinitions()
    {
        $thumbs = collect($this->settingmanager->setting('thumbnails'));

        return $thumbs->map(function ($thumb, $name) {
            $name = Str::after($name, 'thumbnails.');

            if (is_array($thumb) && count($thumb) == 5) {
                if ($thumb[0] == 'fit') {
                    return [
                        'name'      => $name,
                        'method'    => 'fit',
                        'width'     => $thumb[1],
                        'height'    => empty($thumb[2]) ? null : $thumb[2],
                        'position'  => empty($thumb[3]) ? 'center' : $thumb[3],
                        'upsize'    => empty($thumb[4]) ? false : $thumb[4],
                    ];
                } elseif ($thumb[0] == 'crop') {
                    return [
                        'name'      => $name,
                        'method'    => 'crop',
                        'width'     => $thumb[1],
                        'height'    => $thumb[2],
                        'x'         => empty($thumb[3]) ? null : $thumb[3],
                        'y'         => empty($thumb[4]) ? null : $thumb[4],
                    ];
                } elseif ($thumb[0] == 'resize') {
                    return [
                        'name'      => $name,
                        'method'    => 'resize',
                        'width'     => empty($thumb[1]) ? null : $thumb[1],
                        'height'    => empty($thumb[2]) ? null : $thumb[2],
                        'aspect'    => empty($thumb[3]) ? true : $thumb[3],
                        'upsize'    => empty($thumb[4]) ? false : $thumb[4],
                    ];
                }
            }

            return null;
        })->filter(function ($thumb) {
            return $thumb !== null;
        });
    }
}
