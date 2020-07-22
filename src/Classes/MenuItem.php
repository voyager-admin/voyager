<?php

namespace Voyager\Admin\Classes;

class MenuItem implements \JsonSerializable
{
    public $title;
    public $icon;
    public $main;
    public $permission = [];
    public $url;
    public $route;
    public $route_params = [];
    public $href = '';
    public $divider = false;
    public $exact = false;
    public $children;

    public function __construct(string $title = '', string $icon = '', bool $main = false)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->main = $main;
        $this->children = collect();

        return $this;
    }

    public function permission($ability, $arguments = [])
    {
        $this->permission = [
            'ability'   => $ability,
            'arguments' => $arguments,
        ];

        return $this;
    }

    public function route($route, $params = [])
    {
        $this->route = $route;
        $this->route_params = $params;

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    public function divider()
    {
        $this->divider = true;

        return $this;
    }

    public function exact()
    {
        $this->exact = true;

        return $this;
    }

    public function addChildren()
    {
        foreach (func_get_args() as $item) {
            $this->children->push($item);
        }

        return $this;
    }

    private function resolveUrl()
    {
        if (!is_null($this->route) && \Route::has($this->route)) {
            return route($this->route, $this->route_params);
        } elseif (!is_null($this->url)) {
            return $this->url;
        }

        return '#';
    }

    public function jsonSerialize()
    {
        return array_merge((array) $this, [
            'href'  => $this->resolveUrl()
        ]);
    }
}
