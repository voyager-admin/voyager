<?php

namespace Voyager\Admin\Manager;

use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Illuminate\Support\Str;

class Menu
{
    protected $pluginmanager;
    protected $items;
    protected $callbacks = [];

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

    public function addCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    public function getItems()
    {
        $items = $this->items->sortBy(function ($item) {
            return $item->main ? 0 : 99999999;
        })->values();
        foreach ($this->callbacks as $callback) {
            $items = $callback($items);
        }

        return $this->validatePermissions($items);
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