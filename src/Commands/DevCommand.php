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
    protected $signature = 'voyager:dev {--enable} {--disable} {url?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Voyager plugins';

    /**
     * Execute the console command.
     *
     * @param \Voyager\Admin\Manager\Settings $settingmanager
     *
     * @return void
     */
    public function handle(SettingManager $settingmanager)
    {
        $url = $this->argument('url');

        if ($settingmanager->getSettingsByKey('admin.dev-server-url')->count() != 1) {
            return $this->error('Setting "admin.dev-server-url" does not exist. Please seed the default settings!');
        }

        if ($this->option('enable') && $url) {
            if (Str::startsWith($url, 'http://') || Str::startsWith($url, 'https://')) {
                $settingmanager->set('admin.dev-server-url', $url);

                return $this->info('Enabled development server with URL "'.$url.'"');
            } else {
                return $this->error('Please provide a valid URL starting with http:// or https://');
            }
        } elseif ($this->option('disable')) {
            $settingmanager->set('admin.dev-server-url', null);

            return $this->info('Disabled development server');
        }

        return $this->error('Please use --disable to disable or --enable [URL] to enable the development server');
    }
}
