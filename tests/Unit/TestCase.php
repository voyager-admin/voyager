<?php

namespace Voyager\Admin\Tests\Unit;

use Voyager\Admin\Facades\Voyager;
use Voyager\Admin\VoyagerServiceProvider;
use Voyager\TestbenchPlugin\TestbenchPluginServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        // Create user BREAD
        $bread = json_decode(file_get_contents(__DIR__.'/../Stubs/users.json'));
        resolve(\Voyager\Admin\Manager\Breads::class)->storeBread($bread);

        // Create a dummy user
        $user = new \Voyager\Admin\Tests\Stubs\User();
        $user->name = 'Admin';
        $user->email = 'admin@admin.com';
        $user->save();

        $this->setupVoyager();
    }

    /**
     * Setup Voyager.
     */
    protected function setupVoyager(): void
    {
        // Delete voyager settings
        $path = __DIR__.'/../../vendor/orchestra/testbench-core/laravel/storage/voyager/settings.json';
        unlink($path);

        $this->artisan('voyager:install');
        $this->artisan('voyager:plugins', [
            'plugin'    => 'voyager-admin/voyager-testbench-plugin@GenericPlugin',
            '--enable'  => true,
        ]);
        $this->artisan('voyager:plugins', [
            'plugin'    => 'voyager-admin/voyager-testbench-plugin@ThemePlugin',
            '--enable'  => true,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            VoyagerServiceProvider::class,
            TestbenchPluginServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup Authentication configuration
        $app['config']->set('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class);
    }

    /*protected function getBasePath()
    {
        // Adjust this path depending on where your override is located.
        return __DIR__.'/../../vendor/orchestra/testbench-dusk/laravel';
    }*/
}
