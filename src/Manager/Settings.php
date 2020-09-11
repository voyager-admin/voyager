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
            $this->loadSettings();
        }

        return $this->path;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function mergeSettings($settings)
    {
        $this->settings = $this->settings->merge($settings);
    }

    public function setting($key = null, $default = null, $translate = true)
    {
        $this->loadSettings();
        $settings = $this->settings;

        if (Str::contains($key, '.')) {
            // We are looking for a setting in a group
            list($group, $key) = explode('.', $key);
            $settings = $settings->where('group', $group)->where('key', $key);
        } elseif (!empty($key)) {
            // We are looking for a setting without a group OR all group-settings
            $group = $settings->where('group', null)->where('key', $key);

            if ($group->count() == 0) {
                $settings = $settings->where('group', $key);
            } else {
                $settings = $group;
            }
        }

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
        } elseif ($settings->count() == 1) {
            $settings = $settings->first();
        }

        return $settings;
    }

    public function exists($group, $key)
    {
        return $this->settings->where('group', $group)->where('key', $key)->count() > 0;
    }

    public function saveSettings($content = null)
    {
        if (is_null($content)) {
            $content = $this->settings;
        }
        $this->loadSettings();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }

        File::put($this->path, $content);
    }

    public function loadSettings()
    {
        VoyagerFacade::ensureFileExists($this->path, '[]');
        $this->settings = collect(VoyagerFacade::getJson(File::get($this->path), []));
    }
}