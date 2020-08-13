<?php

namespace Voyager\Admin\Tests\Browser;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Auth;
use Laravel\Dusk\Browser as DuskBrowser;
use Orchestra\Testbench\Dusk\Options as DuskOptions;
use Orchestra\Testbench\Dusk\TestCase as DuskTestCase;
use Voyager\Admin\Facades\Voyager;
use Voyager\Admin\DuskCoverageServiceProvider;
use Voyager\Admin\VoyagerServiceProvider;

class TestCase extends DuskTestCase
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

        $this->setupVoyager();
    }

    /**
     * Setup Voyager.
     */
    protected function setupVoyager(): void
    {
        $this->artisan('voyager:install');
        $this->artisan('voyager:plugins voyager-admin/voyager-testbench-plugin@ThemePlugin --enable');
        $this->artisan('voyager:plugins voyager-admin/voyager-testbench-plugin@GenericPlugin --enable');
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
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup Authentication configuration
        $app['config']->set('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class);
    }

    protected function driver(): RemoteWebDriver
    {
        // Hide console informations like "Download the Vue devtools..."
        $options = DuskOptions::getChromeOptions();
        $options->addArguments([
            '--log-level=3',
            '--silent',
            '--disable-gpu',
            '--headless',
            '--no-sandbox',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }
}
