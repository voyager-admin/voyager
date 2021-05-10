<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\Assert;
use Voyager\Admin\Tests\Unit\TestCase;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class DevServerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_enable_dev_server()
    {
        $this->artisan('voyager:dev --enable http://test.com')
             ->expectsOutput('Enabled development server with URL "http://test.com"')
             ->assertExitCode(0);

        // TODO: Visit dashboard and check if http://test.com/js/voyager.js was loaded
    }

    public function test_can_not_enable_dev_server_with_wrong_url()
    {
        $this->artisan('voyager:dev --enable WRONG')
             ->expectsOutput('Please provide a valid URL starting with http:// or https://')
             ->assertExitCode(0);
    }

    public function test_can_not_enable_dev_server_without_args()
    {
        $this->artisan('voyager:dev')
             ->expectsOutput('Please use --disable to disable or --enable [URL] to enable the development server')
             ->assertExitCode(0);
    }

    public function test_can_not_set_dev_server_without_setting()
    {
        // Remove 1 setting
        $path = __DIR__.'/../../vendor/orchestra/testbench-core/laravel/storage/voyager/settings.json';
        $content = collect(json_decode(file_get_contents($path)))->filter(function ($setting) {
            return $setting->group !== 'admin' && $setting->key !== 'dev-server-url';
        });
        file_put_contents($path, json_encode($content));

        // Force load new settings
        resolve(\Voyager\Admin\Manager\Settings::class)->load(true);

        $this->artisan('voyager:dev')
             ->expectsOutput('Setting "admin.dev-server-url" does not exist. Please seed the default settings!')
             ->assertExitCode(0);
    }

    public function test_can_disable_dev_server()
    {
        $this->artisan('voyager:dev --enable http://test.com')
             ->expectsOutput('Enabled development server with URL "http://test.com"')
             ->assertExitCode(0);

        $this->artisan('voyager:dev --disable')
             ->expectsOutput('Disabled development server')
             ->assertExitCode(0);
    }
}
