<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Config\Repository;
use Voyager\Admin\Facades\Voyager;
use Voyager\Admin\Tests\Stubs\CustomPathProvider;
use Voyager\Admin\Tests\Unit\TestCase;
use Voyager\Admin\VoyagerServiceProvider;

class VoyagerCustomPathTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function useVoyagerCustomPath($app)
    {
        Voyager::path('/voyager');
    }

    /**
     * @environment-setup useVoyagerCustomPath
     */
    public function test_can_find_voyager_at_voyager()
    {
        $res = $this->get('/voyager')->assertRedirect(route('voyager.login'));
    }

    public function test_can_not_find_voyager_at_default()
    {
        $res = $this->get('/admin')->assertNotFound();
    }

    /**
     * Note this is a bit of a hack to make sure consecutive tests are at the default.
     *
     * @param $app
     */
    protected function useVoyagerDefaultPath($app)
    {
        Voyager::path();
    }

    /**
     * Note this is a bit of a hack to make sure consecutive tests are at the default.
     *
     * @environment-setup useVoyagerDefaultPath
     */
    public function test_can_find_voyager_at_default()
    {
        $res = $this->get('/admin')->assertRedirect(route('voyager.login'));
    }
}
