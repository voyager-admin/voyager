<?php

namespace Voyager\Admin\Tests\Unit;

use Voyager\Admin\Tests\Unit\TestCase;

class VoyagerDefaultPathTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_find_voyager_at_admin()
    {
        $res = $this->get('/admin')->assertRedirect(route('voyager.login'));
    }

    public function test_can_not_find_voyager_at_voyager()
    {
        $res = $this->get('/voyager')->assertNotFound();
    }
}
