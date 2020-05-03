<?php

namespace Voyager\Admin\Tests\Unit;

use Voyager\Admin\Facades\Voyager;
use Voyager\Admin\VoyagerServiceProvider;
use Voyager\Admin\Facades\Bread as BreadFacade;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $route_dir = realpath($this->getBasePath());
        if (!is_dir($route_dir.'/routes')) {	
            @mkdir($route_dir.'/routes');	
        }	
        if (!file_exists($route_dir.'/routes/web.php')) {	
            file_put_contents($route_dir.'/routes/web.php', "<?php\n");	
        }

        $bread = json_decode(file_get_contents(__DIR__.'/../Stubs/users.json'));
        BreadFacade::storeBread($bread);

        // Create a dummy user
        $user = new \Illuminate\Foundation\Auth\User();
        $user->name = 'Admin';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('secret');
        $user->save();

        $this->setupVoyager();
    }

    /**
     * Setup Voyager.
     */
    protected function setupVoyager(): void
    {
        $this->artisan('voyager:install');
    }

    protected function getPackageProviders($app)
    {
        return [
            VoyagerServiceProvider::class,
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
        $app['router']->prefix('admin')->group(function (\Illuminate\Routing\Router $router) {
            Voyager::routes($router);
        });

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup Authentication configuration
        $app['config']->set('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class);
    }

    protected function getBasePath()
    {
        // Adjust this path depending on where your override is located.
        return __DIR__.'/../../vendor/orchestra/testbench-dusk/laravel';
    }
}
