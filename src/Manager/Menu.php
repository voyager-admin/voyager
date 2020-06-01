<?php

namespace Voyager\Admin\Manager;

use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Illuminate\Support\Str;

class Menu
{
    protected $pluginmanager;
    protected $items;

    public function __construct(PluginManager $pluginmanager)
    {
        $this->pluginmanager = $pluginmanager;
        $this->items = collect();
    }

    public function addItems()
    {
        foreach (func_get_args() as $item) {
            $this->items->push($item);
        }
    }

    public function getItems()
    {
        $resolved = $this->resolveUrls($this->items);

        return $this->validatePermissions($resolved);
    }

    private function resolveUrls($collection)
    {
        return $collection->transform(function ($item) {
            $item->children = $this->resolveUrls($item->children);
            
            if (!is_null($item->route) && \Route::has($item->route)) {
                $item->href = route($item->route, $item->route_params);
            } elseif (!is_null($item->url)) {
                $item->href = $item->url;
            }

            return $item;
        });
    }

    private function validatePermissions($collection)
    {
        return $collection->filter(function ($item) {
            $item->children = $this->validatePermissions($item->children);

            if (($item->permission['ability'] ?? null) === null) {
                return true;
            }

            return VoyagerFacade::authorize(
                VoyagerFacade::auth()->user(),
                $item->permission['ability'] ?? null,
                $item->permission['arguments'] ?? []
            );
        });
    }
}