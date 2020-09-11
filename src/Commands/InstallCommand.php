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
        $content = file_get_contents(realpath(__DIR__.'/../../resources/presets/settings.json'));
        if (is_null($settingsmanager->setting())) {
            $settingsmanager->save($content);
            $this->info('Default settings written');
        } else {
            if ($this->confirm('Settings JSON file already exists. Do you want to migrate new settings?')) {
                $preset = json_decode($content);
                $new = 0;
                foreach ($preset as $setting) {
                    if (!$settingsmanager->exists($setting->group, $setting->key)) {
                        if (empty($setting->group)) {
                            $this->line('New setting: "'.$setting->key.'": '.$setting->info);
                        } else {
                            $this->line('New setting: "'.$setting->group.'.'.$setting->key.'": '.$setting->info);
                        }
                        $settingsmanager->merge([$setting]);
                        $new++;
                    }
                }
                if ($new > 0) {
                    $settingsmanager->save();
                    $this->info('Wrote '.$new.' new setting(s)!');
                } else {
                    $this->info('No new settings found!');
                }
            }
        }
    }
}
