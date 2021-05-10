<?php

namespace Voyager\Admin\Tests\Feature;

use Voyager\Admin\Classes\MenuItem;
use Voyager\Admin\Manager\Menu as MenuManager;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Tests\Unit\TestCase;

class MenuTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_add_url_item()
    {
        $this->getMenuManager()->addItems(
            (new MenuItem('', '', true))->url('http://voyager.test')
        );

        $this->assertTrue(collect(json_decode(json_encode($this->getMenuItems())))->filter(function ($item) {
            return $item->href == 'http://voyager.test';
        })->count() == 1);
    }

    public function test_can_add_route_item()
    {
        $this->getMenuManager()->addItems(
            (new MenuItem('', '', true))->route('voyager.dashboard')
        );

        $this->assertTrue(collect(json_decode(json_encode($this->getMenuItems())))->filter(function ($item) {
            return $item->href == route('voyager.dashboard');
        })->count() >= 1);
    }

    private function getMenuManager()
    {
        return resolve(MenuManager::class);
    }

    private function getMenuItems()
    {
        return $this->getMenuManager()->getItems(resolve(PluginManager::class));
    }
}
