<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Plugins as PluginManager;

class PluginsController extends Controller
{
    protected $pluginmanager;

    public function __construct(PluginManager $pluginmanager)
    {
        $this->pluginmanager = $pluginmanager;
    }

    public function enable(Request $request)
    {
        $identifier = $request->get('identifier');
        if ($request->get('enable', false)) {
            return $this->enablePlugin($identifier);
        }

        return $this->enablePlugin($identifier, false);
    }

    public function get()
    {
        return $this->pluginmanager->getAllPlugins()->sortBy('identifier')->transform(function ($plugin) {
            // This is only used to preview a theme
            if ($plugin->type == 'theme') {
                $plugin->src = $plugin->getStyleRoute();
            }

            return $plugin;
        });
    }

    public function settings($key)
    {
        $plugin = $this->pluginmanager->getAllPlugins()->get($key);
        if (!$plugin) {
            throw new \Voyager\Admin\Exceptions\PluginNotFoundException('This Plugin does not exist');
        } elseif ($plugin->has_settings && $plugin->enabled) {
            return $plugin->getSettingsView();
        }

        return redirect()->back();
    }

    public function install(Request $request)
    {
        $package = $request->get('repository', []);
        if (!is_array($package)) {
            $package = [$package];
        }
        $app = Application::getInstance();

        $composer = new \Composer\Console\Application();
        $composer->setAutoExit(false);
        $composer->setCatchExceptions(false);
        $input = new \Symfony\Component\Console\Input\ArrayInput([
            '--working-dir' => base_path('/'),
            'command'  => 'require',
            'packages' => $package,
            '--no-suggest' => true,
            '--no-progress'      => true,
        ]);
        $result = $composer->run($input, $output = new InstallationOutput());

        Application::setInstance($app);

        return response()->json($output->output(), $result === 0 ? 200 : 500);
    }

    private function enablePlugin($identifier, $enable = true)
    {
        $this->pluginmanager->getAllPlugins();

        $plugins = collect(VoyagerFacade::getJson(File::get($this->pluginmanager->getPath()), []));
        if (!$plugins->contains('identifier', $identifier)) {
            $plugins->push([
                'identifier' => $identifier,
                'enabled'    => $enable,
            ]);
        } else {
            $plugins->where('identifier', $identifier)->first()->enabled = $enable;
        }

        return File::put($this->pluginmanager->getPath(), json_encode($plugins, JSON_PRETTY_PRINT));
    }
}

class InstallationOutput extends \Symfony\Component\Console\Output\Output
{
    protected $content = [''];

    public function doWrite(string $message, bool $newline)
    {
        $this->content[count($this->content) - 1] .= $message;

        if ($newline) {
            $this->content[] = '';
        }
    }

    public function output()
    {
        return $this->content;
    }
}
