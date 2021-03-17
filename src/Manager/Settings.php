<?php

namespace Voyager\Admin\Manager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Voyager\Admin\Classes\Setting;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Settings
{
    protected $path;
    protected $settings = null;

    public function __construct()
    {
        $this->path = Str::finish(storage_path('voyager'), '/').'settings.json';
    }

    /**
     * Sets the path where the settings-file is stored.
     *
     * @param string $path
     *
     * @return string the current path
     */
    public function setPath($path = null)
    {
        if (!is_null($path)) {
            $this->path = $path;
            $this->load();
        }

        return $this->path;
    }

    public function get()
    {
        return $this->settings;
    }

    // Set a settings-value based on the key. When locale is not provided and the setting is translatable, it expects an array of locale-values
    public function set(string $key, $value, $locale = null)
    {
        $setting = $this->getSettingsByKey($key);

        if ($key->count() == 1) {
            // ...
        } else {
            throw new \Exception('Setting with key `'.$key.'` does not exist');
        }
    }

    public function merge(array $settings)
    {
        $this->settings = $this->settings->merge($settings);
    }

    public function setting($key = null, $default = null, $translate = true)
    {
        $this->load();
        $settings = $this->getSettingsByKey($key);

        // Modify collection and only include key/value pairs
        $settings = $settings->mapWithKeys(function ($setting) use ($translate, $default) {
            $key = $setting->key;
            if ($setting->group !== null && $setting->group !== '') {
                $key = implode('.', [$setting->group, $setting->key]);
            }
            if ($translate && $setting->translatable || false) {
                return [$key => VoyagerFacade::translate($setting->value, app()->getLocale(), config('app.fallback_locale')) ?? $default];
            }

            return [$key => $setting->value ?? $default];
        });

        if ($settings->count() == 0) {
            return $default;
        } elseif ($settings->count() == 1) { // TODO: Don't use first() when all in a group
            $settings = $settings->first();
        }

        return $settings;
    }

    public function exists($group, $key)
    {
        return $this->settings->where('group', $group)->where('key', $key)->count() > 0;
    }

    public function save($content = null)
    {
        if (is_null($content)) {
            $content = $this->settings;
        }
        $this->load();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }

        File::put($this->path, $content);
    }

    public function load()
    {
        VoyagerFacade::ensureFileExists($this->path, '[]');
        $this->settings = collect(VoyagerFacade::getJson(File::get($this->path), []));
    }

    public function getSettingsByKey($key)
    {
        if (Str::contains($key, '.')) {
            // We are looking for a setting in a group
            list($group, $key) = explode('.', $key);
            return $this->settings->where('group', $group)->where('key', $key);
        } elseif (!empty($key)) {
            // We are looking for a setting without a group OR all group-settings
            $group = $this->settings->where('group', null)->where('key', $key);

            if ($group->count() == 0) {
                // All settings in a group
                return $this->settings->where('group', $key);
            } else {
                // Setting without a group but matching key
                return $group;
            }
        }

        return $this->settings;
    }
}