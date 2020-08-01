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
     * @param \Voyager\Admin\Manager\Settings $settingsmanager
     *
     * @return void
     */
    public function handle(SettingManager $settingsmanager)
    {
        $this->setSettings($settingsmanager);

        $this->info('Successfully installed Voyager! Enjoy');
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
