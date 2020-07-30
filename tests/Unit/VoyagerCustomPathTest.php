<?php

namespace Voyager\Admin\Tests\Unit;

use Voyager\Admin\Tests\Stubs\CustomPathProvider;
use Voyager\Admin\Tests\Unit\TestCase;
use Voyager\Admin\VoyagerServiceProvider;

class VoyagerCustomPathTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            VoyagerServiceProvider::class,
            CustomPathProvider::class,
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_find_voyager_at_voyager()
    {
        $res = $this->get('/voyager')->assertRedirect(route('voyager.login'));
    }

    public function test_can_not_find_voyager_at_default()
    {
        $res = $this->get('/admin')->assertNotFound();
    }
}
