<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Tests\Unit\TestCase;

class InstallTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_not_migrate_settings_when_no_new()
    {
        $this->artisan('voyager:install')
             ->expectsQuestion('Settings JSON file already exists. Do you want to migrate new settings?', 'Yes')
             ->expectsOutput('No new settings found!')
             ->assertExitCode(0);
    }

    public function test_can_migrate_settings()
    {
        // Remove 1 setting
        $path = __DIR__.'/../../vendor/orchestra/testbench-core/laravel/storage/voyager/settings.json';
        $content = json_decode(file_get_contents($path));
        array_splice($content, -1, 1);
        file_put_contents($path, json_encode($content));

        // Force load new settings
        resolve(\Voyager\Admin\Manager\Settings::class)->load(true);

        $this->artisan('voyager:install')
             ->expectsQuestion('Settings JSON file already exists. Do you want to migrate new settings?', 'Yes')
             ->expectsOutput('Wrote 1 new setting(s)!')
             ->assertExitCode(0);
    }
}
