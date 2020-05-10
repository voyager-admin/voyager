<?php

namespace Voyager\Admin;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Voyager\Admin\Commands\InstallCommand;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Http\Middleware\VoyagerAdminMiddleware;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Manager\Settings as SettingManager;
use Voyager\Admin\Plugins\AuthenticationPlugin;
use Voyager\Admin\Policies\BasePolicy;

class VoyagerServiceProvider extends ServiceProvider
{
    protected $policies = [];
    protected $pluginmanager;
    protected $breadmanager;

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
        $this->breadmanager->getBreads()->each(function ($bread) {
            $policy = BasePolicy::class;

            if (!empty($bread->policy) && class_exists($bread->policy)) {
                $policy = $bread->policy;
            }

            $this->policies[$bread->model.'::class'] = $policy;
        });
        $this->registerPolicies();

        $router->aliasMiddleware('voyager.admin', VoyagerAdminMiddleware::class);

        View::share('authentication', $this->pluginmanager->getPluginByType('authentication', AuthenticationPlugin::class));
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('Voyager', VoyagerFacade::class);

        $this->pluginmanager = new PluginManager();
        $this->pluginmanager->pluginsPath(Str::finish(storage_path('voyager'), '/').'plugins.json');
        $this->app->singleton(PluginManager::class, function () {
            return $this->pluginmanager;
        });

        $this->breadmanager = new BreadManager();
        $this->breadmanager->breadPath(storage_path('voyager/breads'));
        $this->app->singleton(BreadManager::class, function () {
            return $this->breadmanager;
        });

        $this->settingmanager = new SettingManager();
        $this->settingmanager->settingsPath(Str::finish(storage_path('voyager'), '/').'settings.json');
        $this->app->singleton(SettingManager::class, function () {
            return $this->settingmanager;
        });

        $this->app->singleton('voyager', function () {
            return new Voyager($this->pluginmanager, $this->settingmanager);
        });

        $this->settingmanager->loadSettings();

        $this->commands(InstallCommand::class);

        $this->registerFormfields();
    }

    public function registerFormfields()
    {
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\DynamicSelect::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Number::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Relationship::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Select::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Tags::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Text::class);
    }
}
