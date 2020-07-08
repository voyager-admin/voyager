<?php

namespace Voyager\Admin\Traits;

use Illuminate\Database\Eloquent\Model;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

trait Translatable
{
    private $translate = true;
    public $current_locale;
    public $fallback_locale;

    public function setLocales()
    {
        if (!$this->current_locale) {
            $this->current_locale = app()->getLocale();
            $this->fallback_locale = config('app.fallback_locale');
        }
    }

    public function getTranslated($key, $locale, $fallback, $default)
    {
        $this->setLocales();

        $old_locale = $this->current_locale;
        $old_fallback = $this->fallback_locale;

        // Set locales to desired
        $this->current_locale = $locale;
        $this->fallback_locale = $fallback;

        $value = $this->__get($key);

        // Set locales back to original
        $this->current_locale = $old_locale;
        $this->fallback_locale = $old_fallback;

        return $value ?? $default;
    }

    public function setTranslated($key, $value, $locale)
    {
        $old_locale = $this->current_locale;

        // Set locale to desired
        $this->current_locale = $locale;

        $this->__set($key, $value);

        // Set locales back to original
        $this->current_locale = $old_locale;
    }

    public function translate()
    {
        $this->translate = true;
    }

    public function dontTranslate()
    {
        $this->translate = false;
    }

    public function __get($key)
    {
        $this->setLocales();
        $value = null;

        if ($this instanceof Model) {
            $value = $this->getAttribute($key);
        } else {
            $value = $this->{$key};
        }
        if (!$this->translate) {
            return $value;
        }

        if ($this->shouldColumnBeTranslated($key)) {
            return VoyagerFacade::translate($value, $this->current_locale, $this->fallback_locale);
        }

        return $value;
    }

    public function __set($key, $value)
    {
        $this->setLocales();
        if ($this->shouldColumnBeTranslated($key)) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            } else {
                $value = json_encode((object) [
                    $this->current_locale => $value,
                ]);
            }
        }
        if ((bool) class_parents($this)) {
            parent::__set($key, $value);
        } else {
            $this->{$key} = $value;
        }
    }

    public function shouldColumnBeTranslated($column)
    {
        return (
            $this->translate &&
            property_exists($this, 'translatable') &&
            is_array($this->translatable) &&
            in_array($column, $this->translatable)
        );
    }
}
