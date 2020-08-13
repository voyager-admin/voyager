<?php

namespace Voyager\Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Voyager\Admin\Manager\Plugins as PluginManager;

class PluginsCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'voyager:plugins {plugin?} {--enable} {--disable} {--show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Voyager plugins';

    /**
     * Execute the console command.
     *
     * @param \Voyager\Admin\Manager\Plugins $pluginsmanager
     *
     * @return void
     */
    public function handle(PluginManager $pluginmanager)
    {
        $name = $this->argument('plugin');
        $plugin = null;
        if (!is_null($name)) {
            $plugin = $pluginmanager->getAllPlugins(false)->filter(function ($plugin) use ($name) {
                return $plugin->repository == $name || $plugin->identifier == $name;
            });
            if ($plugin->count() > 1) {
                // TODO: Test this
                $selected = $this->choice('Package "'.$name.'" contains multiple plugins. Please select the plugin you want to use:', $plugin->pluck('name')->toArray(), 0);
                $plugin = $plugin->where('name', $selected)->first();
            } else {
                $plugin = $plugin->first();
            }
        }

        if ($this->option('enable')) {
            // Enable a plugin
            if (!is_null($plugin)) {
                if ($plugin->enabled) {
                    $this->warn('Plugin is already enabled!');
                } else {
                    $pluginmanager->enablePlugin($plugin->identifier);
                    $this->info('Plugin "'.$name.'" was enabled!');
                }

                return;
            }
        } elseif ($this->option('disable')) {
            // Disable a plugin
            if (!is_null($plugin)) {
                if (!$plugin->enabled) {
                    $this->warn('Plugin is already disabled!');
                } else {
                    $pluginmanager->disablePlugin($plugin->identifier);
                    $this->info('Plugin "'.$name.'" was disabled!');
                }

                return;
            }
        } elseif ($this->option('show') || !is_null($name)) {
            // Show information about a plugin
            if (!is_null($plugin)) {
                $this->info('Name: '.$plugin->name);
                $this->info('Description: '.$plugin->description);
                $this->info('Website: '.$plugin->website);
                $this->info('Version: '.$plugin->version);

                return;
            }
        } else {
            $plugins = $pluginmanager->getAllPlugins(false);
            $selected = $this->choice('The following plugins are registered. Select one to get details', $pluginmanager->getAllPlugins(false)->pluck('name')->toArray());
            $this->call('voyager:plugins', [
                'plugin' => $pluginmanager->getAllPlugins(false)->where('name', $selected)->first()->identifier,
            ]);

            return;
        }

        $this->error('Plugin "'.$name.'" is not registered');
    }
}
