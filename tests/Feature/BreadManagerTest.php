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

    public function test_prefetch_routing()
    {
        $this->get(route('voyager.bread.index'));
        $this->assertTrue(true);
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

    public function test_creating_bread_for_not_existing_table_fails()
    {
        $this->get(route('voyager.bread.create', 'not_existing'))
             ->assertSee('TableNotFoundException');
    }

    public function test_editing_not_existing_bread_redirects()
    {
        $this->get(route('voyager.bread.edit', 'migrations'))
             ->assertRedirect(route('voyager.bread.create', 'migrations'));
    }

    public function test_creating_existing_bread_redirects()
    {
        $this->get(route('voyager.bread.create', 'users'))
             ->assertRedirect(route('voyager.bread.edit', 'users'));
    }

    public function test_create_migrations_bread()
    {
        $this->get(route('voyager.bread.create', 'migrations'))
             ->assertStatus(200);
    }
}
