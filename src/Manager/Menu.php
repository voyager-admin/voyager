<?php

namespace Voyager\Admin\Manager;

use Illuminate\Support\Str;
use Voyager\Admin\Classes\MenuItem;
use Voyager\Admin\Contracts\Plugins\Features\Filter\MenuItems as MenuItemFilter;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class Menu
{
    protected $items;

    public function __construct()
    {
        $this->items = collect();
    }

    public function addItems()
    {
        foreach (func_get_args() as $item) {
            if ($item instanceof MenuItem) {
                $this->items->push($item);
            }
        }
    }

    public function getItems(Plugins $pluginmanager)
    {
        $items = $this->items->sortBy(function ($item) {
            return $item->main ? 0 : 99999999;
        })->values();
        
        $pluginmanager->getAllPlugins()->each(function ($plugin) use (&$items) {
            if ($plugin instanceof MenuItemFilter) {
                $items = $plugin->filterMenuItems($items);
            }
        });

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