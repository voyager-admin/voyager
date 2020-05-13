<?php

namespace Voyager\Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Voyager\Admin\Manager\Settings as SettingManager;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Voyager Admin package';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \Voyager\Admin\Manager\Settings $settingsmanager
     *
     * @return void
     */
    public function handle(Filesystem $filesystem, SettingManager $settingsmanager)
    {
        $this->setRoutes($filesystem);
        $this->setSettings($settingsmanager);

        $this->info('Successfully installed Voyager! Enjoy');
    }

    private function setRoutes(Filesystem $filesystem)
    {
        $routes_contents = $filesystem->get(base_path('routes/web.php'));
        if (strpos($routes_contents, 'Voyager::routes()') === false) {
            $filesystem->append(
                base_path('routes/web.php'),
                "\n\nRoute::group(['prefix' => 'admin'], function () {\n    Voyager::routes();\n});\n"
            );

            $this->info('Added Voyager routes to routes/web.php');
        } else {
            $this->warn('Voyager routes already exist in routes/web.php');
        }
    }

    private function setSettings(SettingManager $settingsmanager)
    {
        if (is_null($settingsmanager->setting())) {
            $content = file_get_contents(realpath(__DIR__.'/../../resources/presets/settings.json'));
            $settingsmanager->saveSettings($content);
            $this->info('Default settings written');
        } else {
            $this->warn('Settings JSON file already exists. Skipped');
        }
    }
}
