<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Tests\Unit\TestCase;

class DashboardTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_dashboard()
    {
        $this->get(route('voyager.dashboard'))
             ->assertStatus(200)
             ->assertSeeText('Welcome to Voyager 2.0');
    }
}
