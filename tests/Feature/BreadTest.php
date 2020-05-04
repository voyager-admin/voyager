<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Tests\Unit\TestCase;

class BreadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_prefetch_routing()
    {
        $this->get(route('voyager.bread.index'));
        $this->assertTrue(true);
    }

    public function test_can_browse_users()
    {
        $this->get(route('voyager.users.browse'))
             ->assertStatus(200);
    }
}
