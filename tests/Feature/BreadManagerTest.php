<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Tests\Unit\TestCase;

class BreadManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_browse_bread_manager()
    {
        $this->get(route('voyager.bread.index'))
             ->assertStatus(200);
    }

    public function test_display_edit_user_bread()
    {
        $this->get(route('voyager.bread.edit', 'users'))
             ->assertStatus(200);
    }
}
