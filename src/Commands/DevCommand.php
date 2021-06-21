<?php

namespace Voyager\Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Voyager\Admin\Manager\Settings as SettingManager;

class DevCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'voyager:dev {--enable} {--disable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable using the dev server during development';

    /**
     * Execute the console command.
     *
     * @param \Voyager\Admin\Manager\Settings $settingmanager
     *
     * @return void
     */
    public function handle(SettingManager $settingmanager)
    {
        
        if ($settingmanager->getSettingsByKey('admin.dev-server')->count() != 1) {
            return $this->error('Setting "admin.dev-server" does not exist. Please seed the default settings!');
        }

        $setting = $settingmanager->setting('admin.dev-server');
        if ($this->option('enable')) {
            $settingmanager->set('admin.dev-server', true);
        } elseif ($this->option('disable')) {
            $settingmanager->set('admin.dev-server', false);
        } else {
            $settingmanager->set('admin.dev-server', !$setting);
        }

        if ($settingmanager->setting('admin.dev-server')) {
            $this->info('Enabled development server!');
        } else {
            $this->info('Disabled development server!');
        }
    }
}
