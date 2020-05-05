<?php

namespace Voyager\Admin;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Voyager\Admin\Commands\InstallCommand;
use Voyager\Admin\Facades\Bread as BreadFacade;
use Voyager\Admin\Facades\Plugins as PluginsFacade;
use Voyager\Admin\Facades\Settings as SettingsFacade;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Http\Middleware\VoyagerAdminMiddleware;
use Voyager\Admin\Plugins\AuthenticationPlugin;
use Voyager\Admin\Policies\BasePolicy;

class VoyagerServiceProvider extends ServiceProvider
{
    protected $policies = [];

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager');
        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), 'voyager');

        // Register Policies
        BreadFacade::getBreads()->each(function ($bread) {
            $policy = BasePolicy::class;

            if (!empty($bread->policy) && class_exists($bread->policy)) {
                $policy = $bread->policy;
            }

            $this->policies[$bread->model.'::class'] = $policy;
        });
        $this->registerPolicies();

        $router->aliasMiddleware('voyager.admin', VoyagerAdminMiddleware::class);

        View::share('authentication', PluginsFacade::getPluginByType('authentication', AuthenticationPlugin::class));
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('Bread', BreadFacade::class);
        $loader->alias('VoyagerPlugins', PluginsFacade::class);
        $loader->alias('VoyagerSettings', SettingsFacade::class);
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->app->singleton('bread', function () {
            return new Bread();
        });
        $this->app->singleton('plugins', function () {
            return new Plugins();
        });
        $this->app->singleton('settings', function () {
            return new Settings();
        });
        $this->app->singleton('voyager', function () {
            return new Voyager();
        });

        $this->loadBreadsFrom(storage_path('voyager/breads'));
        $this->loadSettingsFrom(Str::finish(storage_path('voyager'), '/').'settings.json');
        $this->loadPluginsFrom(Str::finish(storage_path('voyager'), '/').'plugins.json');

        $this->commands(InstallCommand::class);

        $this->registerFormfields();
    }

    public function loadBreadsFrom($path)
    {
        BreadFacade::breadPath($path);
    }

    public function loadSettingsFrom($path)
    {
        SettingsFacade::settingsPath($path);
        SettingsFacade::loadSettings();
    }

    public function loadPluginsFrom($path)
    {
        PluginsFacade::pluginsPath($path);
    }

    public function registerFormfields()
    {
        BreadFacade::addFormfield(\Voyager\Admin\Formfields\DynamicSelect::class);
        BreadFacade::addFormfield(\Voyager\Admin\Formfields\Number::class);
        BreadFacade::addFormfield(\Voyager\Admin\Formfields\Relationship::class);
        BreadFacade::addFormfield(\Voyager\Admin\Formfields\Select::class);
        BreadFacade::addFormfield(\Voyager\Admin\Formfields\Tags::class);
        BreadFacade::addFormfield(\Voyager\Admin\Formfields\Text::class);
    }
}
