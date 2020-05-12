<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Voyager\Admin\Manager\Breads as BreadManager;

class BreadManagerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_update_user_bread()
    {
        $users_bread = $this->getUsersBreadJson();
        $this->putJson(route('voyager.bread.update', 'users'), ['bread' => $users_bread])
            ->assertStatus(200);
    }

    public function test_cant_update_user_bread_with_invalid_json()
    {
        $this->putJson(route('voyager.bread.update', 'users'), ['bread' => @json_decode('[}')])
            ->assertStatus(422)
            ->assertJson([
                __('voyager::bread.json_data_not_valid')
            ]);
    }

    public function test_cant_update_user_bread_not_existing_model()
    {
        $users_bread = $this->getUsersBreadJson();
        $users_bread->model = 'App\\NotExistingModel';
        $this->putJson(route('voyager.bread.update', 'users'), ['bread' => $users_bread])
            ->assertStatus(422)
            ->assertJson([
                'model' => [
                    __('voyager::validation.class_does_not_exist', ['value' => 'App\\NotExistingModel', 'attribute' => 'model'])
                ],
            ]);
    }

    public function test_can_delete_user_bread()
    {
        $this->delete(route('voyager.bread.delete', 'users'))
             ->assertStatus(200);
        $this->assertFalse(file_exists(resolve(BreadManager::class)->setPath() . 'users.json'));
    }

    public function test_can_not_delete_not_existing_bread()
    {
        $this->delete(route('voyager.bread.delete', 'not_existing'))
             ->assertStatus(500);
    }

    public function test_can_backup_restore_users_bread()
    {
        // Backup
        $this->postJson(route('voyager.bread.backup-bread'), ['table' => 'users'])
            ->assertStatus(200);

        $breads = $this->post(route('voyager.bread.get-breads'))->assertStatus(200);
        $backups = collect($breads->original['backups']);
        $this->assertTrue($backups->filter(function ($backup) {
            return $backup['table'] == 'users' && Str::startsWith($backup['path'], 'users.backup');
        })->count() > 0);

        // Restore

    }

    public function test_can_get_model_properties()
    {
        $res = $this->postJson(route('voyager.bread.get-properties'), ['model' => 'Illuminate\\Foundation\\Auth\\User'])
             ->assertStatus(200);
        $this->assertTrue(in_array('email', $res->original['columns']));
    }

    public function test_can_not_get_model_properties_not_exisiting_model()
    {
        $res = $this->postJson(route('voyager.bread.get-properties'), ['model' => '\\Not\\Existing\\Model'])
             ->assertStatus(400)
             ->assertSeeText('Model "\\Not\\Existing\\Model" does not exist!', false);
    }

    public function test_can_not_get_model_properties_empty_model()
    {
        $res = $this->postJson(route('voyager.bread.get-properties'), ['model' => ''])
             ->assertStatus(400)
             ->assertSeeText('Please enter a model class');
    }

    private function getUsersBreadJson()
    {
        return json_decode(file_get_contents(__DIR__."/../Stubs/users.json"));
    }
}
