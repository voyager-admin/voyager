<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Tests\Unit\TestCase;

class BreadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_browse_users()
    {
        resolve(BreadManager::class)->storeBread($this->getUsersBreadJson());
        $this->get(route('voyager.users.browse'))
             ->assertStatus(200)
             ->assertSeeText('Browse Users');
    }

    public function test_can_add_users()
    {
        resolve(BreadManager::class)->storeBread($this->getUsersBreadJson());
        $this->get(route('voyager.users.add'))
             ->assertStatus(200)
             ->assertSeeText('Add User');
    }

    public function test_can_read_users()
    {
        resolve(BreadManager::class)->storeBread($this->getUsersBreadJson());
        $this->get(route('voyager.users.read', 1))
             ->assertStatus(200)
             ->assertSeeText('Show User');
    }

    public function test_can_edit_user()
    {
        resolve(BreadManager::class)->storeBread($this->getUsersBreadJson());
        $this->get(route('voyager.users.edit', 1))
             ->assertStatus(200)
             ->assertSeeText('Edit User');
    }

    public function test_can_get_user_data()
    {
        resolve(BreadManager::class)->storeBread($this->getUsersBreadJson());
        $res = $this->postJson(route('voyager.users.data'), [
            'page'        => 1,
            'perpage'     => 10,
            'global'      => '',
            'filters'     => [],
            'order'       => 'name',
            'direction'   => 'asc',
            'softdeleted' => 'show',
            'locale'      => 'en',
            'filter'      => null,
        ])->assertStatus(200);
    }

    private function getUsersBreadJson()
    {
        return json_decode(file_get_contents(__DIR__."/../Stubs/users.json"));
    }
}
