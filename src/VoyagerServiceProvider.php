<?php

namespace Voyager\Admin;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Voyager\Admin\Classes\Bread;
use Voyager\Admin\Classes\MenuItem;
use Voyager\Admin\Commands\InstallCommand;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Http\Middleware\VoyagerAdminMiddleware;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Manager\Menu as MenuManager;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Manager\Settings as SettingManager;
use Voyager\Admin\Policies\BasePolicy;

class VoyagerServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $policies = [];

    /**
     * @var PluginManager
     */
    protected $pluginmanager;

    /**
     * @var BreadManager
     */
    protected $breadmanager;

    /**
     * @var MenuManager
     */
    protected $menumanager;

    /**
     * @var SettingManager
     */
    protected $settingmanager;

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(Router $router)
    {
        $this->registerResources();

        $this->loadPluginFormfields();

        $breads = $this->breadmanager->getBreads();

        // Register menu-items
        $this->registerMenuItems();
        $this->registerBreadBuilderMenuItem($breads);
        $this->registerBreadMenuItems($breads);

        // Register BREAD policies
        $this->registerBreadPolicies($breads);
        $this->registerPolicies();

        // Register permissions
        app(Gate::class)->before(static function ($user, $ability, $arguments = []) {
            return VoyagerFacade::authorize($user, $ability, $arguments);
        });

        $router->aliasMiddleware('voyager.admin', VoyagerAdminMiddleware::class);

        $this->registerRoutes($breads);
    }


    /**
     * Register the Voyager resources.
     *
     * @return void
     */
    protected function registerResources(): void
    {
        $this->loadViewsFrom(realpath(__DIR__.'/../resources/views'), 'voyager');
        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), 'voyager');
    }

    /**
     * Register the Voyager routes.
     *
     * @param Collection $breads A collection of the Voyager apps current bread types.
     *
     * @return void
     */
    protected function registerRoutes(Collection $breads)
    {
        Route::group([
            'as'         => 'voyager.',
            'prefix'     => '/admin',
            'middleware' => 'web',
        ], function () use ($breads) {
            Route::group([
                'namespace' => 'Voyager\Admin\Http\Controllers',
            ], function () use ($breads) {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
                $this->registerBreadRoutes($breads);
            });
            VoyagerFacade::pluginRoutes();
        });
        VoyagerFacade::pluginFrontendRoutes();
    }

    /**
     * Register all the dynamic BREAD type routes.
     *
     * @param Collection $breads A collection of the Voyager apps current bread types.
     */
    private function registerBreadRoutes(Collection $breads): void
    {
        $breads->each(static function (Bread $bread) {
            $controller = 'BreadController';
            if (!empty($bread->controller)) {
                $controller = \Illuminate\Support\Str::start($bread->controller, '\\');
            }
            Route::group([
                'as'         => $bread->slug.'.',
                'prefix'     => $bread->slug,
            ], static function () use ($bread, $controller) {
                // Browse
                Route::view('/', 'voyager::bread.browse', compact('bread'))->name('browse');
                Route::post('/data', ['uses'=> $controller.'@data', 'as' => 'data', 'bread' => $bread]);

                // Edit
                Route::get('/edit/{id}', ['uses' => $controller.'@edit', 'as' => 'edit', 'bread' => $bread]);
                Route::put('/{id}', ['uses' => $controller.'@update', 'as' => 'update', 'bread' => $bread]);

                // Add
                Route::get('/add', ['uses' => $controller.'@add', 'as' => 'add', 'bread' => $bread]);
                Route::post('/', ['uses' => $controller.'@store', 'as' => 'store', 'bread' => $bread]);

                // Delete
                Route::delete('/', ['uses' => $controller.'@delete', 'as' => 'delete', 'bread' => $bread]);
                Route::patch('/', ['uses' => $controller.'@restore', 'as' => 'restore', 'bread' => $bread]);

                // Read
                Route::get('/{id}', ['uses' => $controller.'@read', 'as' => 'read', 'bread' => $bread]);
            });
        });
    }

    public function loadPluginFormfields()
    {
        $this->pluginmanager->getPluginsByType('formfield')->where('enabled')->each(function ($formfield) {
            $this->breadmanager->addFormfield($formfield->getFormfield());
        });
    }

    /**
     * Register all policies from the the BREAD types.
     *
     * @param Collection $breads A collection of the Voyager apps current bread types.
     */
    public function registerBreadPolicies(Collection $breads): void
    {
        $breads->each(function ($bread) {
            $policy = BasePolicy::class;

            if (!empty($bread->policy) && class_exists($bread->policy)) {
                $policy = $bread->policy;
            }

            $this->policies[$bread->model.'::class'] = $policy;
        });
    }

    /**
     * Register the menu items for each BREAD type's builder.
     *
     * @param Collection $breads A collection of the Voyager apps current bread types.
     */
    public function registerBreadBuilderMenuItem(Collection $breads): void
    {
        $bread_builder_item = (new MenuItem(__('voyager::generic.bread'), 'bread', true))
                                ->permission('browse', ['breads'])
                                ->route('voyager.bread.index');

        $this->menumanager->addItems(
            $bread_builder_item
        );

        $breads->each(function ($bread) use ($bread_builder_item) {
            $bread_builder_item->addChildren(
                (new MenuItem($bread->name_plural, $bread->icon, true))->permission('edit', [$bread->table])
                    ->route('voyager.bread.edit', ['table' => $bread->table])
            );
        });
    }

    /**
     * @param Collection $breads A collection of the Voyager apps current bread types.
     */
    public function registerBreadMenuItems(Collection $breads)
    {
        if ($breads->count() > 0) {
            $this->menumanager->addItems(
                (new MenuItem('', '', true))->divider()
            );

            $breads->each(function ($bread) {
                $this->menumanager->addItems(
                    (new MenuItem($bread->name_plural, $bread->icon, true))->permission('browse', [$bread])
                        ->route('voyager.'.$bread->slug.'.browse')
                );
            });
        }
    }

    private function registerMenuItems()
    {
        $this->menumanager->addItems(
            (new MenuItem(__('voyager::generic.dashboard'), 'home', true))->permission('browse', ['admin'])->route('voyager.dashboard')->exact()
        );
        $this->menumanager->addItems(
            (new MenuItem(__('voyager::generic.media'), 'photograph', true))->permission('browse', ['media'])->route('voyager.media'),
        );

        if ($this->settingmanager->setting('admin.ui-components', true)) {
            $this->menumanager->addItems(
                (new MenuItem(__('voyager::generic.ui_components'), 'template', true))->permission('browse', ['ui'])->route('voyager.ui'),
            );
        }

        $this->menumanager->addItems(
            (new MenuItem(__('voyager::generic.settings'), 'cog', true))->permission('browse', ['settings'])->route('voyager.settings.index'),
            (new MenuItem(__('voyager::plugins.plugins'), 'puzzle', true))->permission('browse', ['plugins'])->route('voyager.plugins.index')
        );
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Voyager', VoyagerFacade::class);

        $this->menumanager = new MenuManager();
        $this->app->singleton(MenuManager::class, function () {
            return $this->menumanager;
        });

        $this->settingmanager = new SettingManager();
        $this->app->singleton(SettingManager::class, function () {
            return $this->settingmanager;
        });

        $this->pluginmanager = new PluginManager($this->menumanager, $this->settingmanager);
        $this->app->singleton(PluginManager::class, function () {
            return $this->pluginmanager;
        });

        $this->breadmanager = new BreadManager();
        $this->app->singleton(BreadManager::class, function () {
            return $this->breadmanager;
        });

        $this->app->singleton('voyager', function () {
            return new Voyager($this->breadmanager, $this->pluginmanager, $this->settingmanager);
        });

        $this->settingmanager->loadSettings();
        $this->pluginmanager->launchPlugins();

        $this->commands(InstallCommand::class);

        $this->registerFormfields();
    }

    private function registerFormfields()
    {
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Checkboxes::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\DynamicSelect::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\MediaPicker::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Number::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Password::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Radios::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Relationship::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Select::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\SimpleArray::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Slug::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Tags::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Text::class);
    }
}
