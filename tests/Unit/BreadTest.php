<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Voyager\Admin\Manager\Breads as BreadManager;

class BreadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_query_global_search()
    {
        resolve(BreadManager::class)->storeBread($this->getUsersBreadJson());
        $res = $this->postJson(route('voyager.globalsearch'), [
            'query' => '',
        ])->assertStatus(200);

        $this->assertTrue($res->original['users']['count'] > 0);
    }

    public function test_can_store_user()
    {
        $user = [
            'name'      => 'Voyager',
            'email'     => 'mail@something.com'
        ];
        resolve(BreadManager::class)->storeBread($this->getUsersBreadJson());
        $res = $this->postJson(route('voyager.users.store'), [
            'data' => $user
        ])
        ->assertStatus(200);
        $this->assertDatabaseHas('users', $user);
    }

    public function test_can_update_user()
    {
        $this->test_can_store_user();
        $user = \Voyager\Admin\Tests\Stubs\User::where('name', 'Voyager')->where('email', 'mail@something.com')->first();

        $res = $this->putJson(route('voyager.users.update', $user->getKey()), [
            'data' => [
                'name'      => 'Voyager 2',
                'email'     => 'mail@something.com'
            ]
        ])
        ->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name'      => 'Voyager 2',
            'email'     => 'mail@something.com'
        ]);
    }

    public function test_can_delete_user()
    {
        $this->test_can_store_user();
        $user = \Voyager\Admin\Tests\Stubs\User::where('name', 'Voyager')->where('email', 'mail@something.com')->first();

        $res = $this->deleteJson(route('voyager.users.delete'), [
            'primary' => $user->getKey(),
        ])
        ->assertStatus(200);
        $this->assertDeleted('users', [
            'id'      => $user->getKey(),
        ]);
    }

    private function getUsersBreadJson()
    {
        return json_decode(file_get_contents(__DIR__."/../Stubs/users.json"));
    }
}
