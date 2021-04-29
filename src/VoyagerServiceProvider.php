<?php

namespace Voyager\Admin;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Voyager\Admin\Classes\Action;
use Voyager\Admin\Classes\Bread;
use Voyager\Admin\Classes\MenuItem;
use Voyager\Admin\Commands\DevCommand;
use Voyager\Admin\Commands\InstallCommand;
use Voyager\Admin\Commands\PluginsCommand;
use Voyager\Admin\Exceptions\Handler as ExceptionHandler;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Http\Middleware\VoyagerAdminMiddleware;
use Voyager\Admin\Manager\Breads as BreadManager;
use Voyager\Admin\Manager\Menu as MenuManager;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Manager\Settings as SettingManager;
use Voyager\Admin\Contracts\Plugins\FormfieldPlugin;
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
        $router->aliasMiddleware('voyager.admin', VoyagerAdminMiddleware::class);

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

        // Register actions
        $this->registerActions();
        $this->registerBulkActions();

        // Register permissions
        app(Gate::class)->before(static function ($user, $ability, $arguments = []) {
            return VoyagerFacade::authorize($user, $ability, $arguments);
        });

        $dev_server = $this->settingmanager->setting('admin.dev-server-url', null);

        if (!empty($dev_server) && filter_var($dev_server, FILTER_VALIDATE_URL) !== false) {
            view()->share('voyagerDevServer', Str::finish($dev_server, '/'));
        }

        Inertia::setRootView('voyager::app');

        // Share data with Inertia
        Inertia::share([
            'locales'               => VoyagerFacade::getLocales(),
            'locale'                => VoyagerFacade::getLocale(),
            'initial_locale'        => VoyagerFacade::getLocale(),

            'admin_title'           => VoyagerFacade::setting('admin.title', 'Voyager II'),
            'notification_position' => VoyagerFacade::setting('admin.notification-position', 'top-right'),

            'current_url'           => Str::finish(url()->current(), '/'),
            'rtl'                   => (__('voyager::generic.is_rtl') == 'true'),
        ]);

        // Only share sensitive data when user is logged in
        Event::listen('voyager.auth.registered', function ($auth) {
            if ($auth->user()) {
                Inertia::share([
                    'debug'                 => config('app.debug') ?? false,
                    'json_output'           => VoyagerFacade::setting('admin.json-output', true),
                    'search_placeholder'    => $this->breadmanager->getBreadSearchPlaceholder(),
                    'sidebar'               => [
                        'items'     => $this->menumanager->getItems($this->pluginmanager),
                        'title'     => VoyagerFacade::setting('admin.sidebar-title', 'Voyager II'),
                        'icon_size' => VoyagerFacade::setting('admin.icon-size', 6)
                    ],
                    'user'                  => [
                        'name'      => $auth->name(),
                        'avatar'    => VoyagerFacade::assetUrl('images/default-avatar.png'),
                    ]
                ]);
            }
        });

        // A Voyager page was requested. Dispatched in Controller::__construct()
        Event::listen('voyager.page', function () {
            // Override ExceptionHandler only when on a Voyager page
            app()->singleton(
                \Illuminate\Contracts\Debug\ExceptionHandler::class,
                ExceptionHandler::class
            );
        });
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
     * @param Collection $breads A collection of the Voyager apps current BREADs.
     *
     * @return void
     */
    protected function registerRoutes(Collection $breads)
    {
        Route::group(['as' => 'voyager.', 'prefix' => Voyager::$routePath, 'namespace' => 'Voyager\Admin\Http\Controllers'], function () use ($breads) {
            Route::group(['middleware' => config('auth.defaults.guard', 'web')], function () use ($breads) {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
                $this->pluginmanager->launchPlugins();
                // Protected routes
                Route::group(['middleware' => 'voyager.admin'], function () use ($breads) {
                    $this->registerBreadRoutes($breads);
                    $this->pluginmanager->launchPlugins(true);
                });
            });
        });
        
        $this->pluginmanager->launchPlugins(false, true);
    }

    /**
     * Register all the dynamic BREAD type routes.
     *
     * @param Collection $breads A collection of the Voyager apps current BREADs.
     */
    private function registerBreadRoutes(Collection $breads): void
    {
        $breads->each(static function (Bread $bread) {
            $controller = 'BreadController';
            if (!empty($bread->controller)) {
                $controller = Str::start($bread->controller, '\\');
            }
            Route::group([
                'as'         => $bread->slug.'.',
                'prefix'     => $bread->slug,
            ], static function () use ($bread, $controller) {
                // Browse
                Route::get('/', ['uses'=> $controller.'@browse', 'as' => 'browse', 'bread' => $bread]);
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

                // Order
                Route::post('/order', ['uses' => $controller.'@order', 'as' => 'order', 'bread' => $bread]);

                // Relationship
                Route::post('/relationship', ['uses' => $controller.'@relationship', 'as' => 'relationship', 'bread' => $bread]);
            });
        });
    }

    /**
     * Register the default BREAD actions (single entry).
     *
     * @return void
     */
    public function registerActions()
    {
        $read_action = (new Action('voyager::generic.read', 'book-open'))
        ->route(function ($bread) {
            return 'voyager.'.$bread->slug.'.read';
        })->permission('read');
        $edit_action = (new Action('voyager::generic.edit', 'pencil'))
        ->route(function ($bread) {
            return 'voyager.'.$bread->slug.'.edit';
        })->permission('edit');
        $delete_action = (new Action('voyager::generic.delete', 'trash', 'red'))
        ->route(function ($bread) {
            return 'voyager.'.$bread->slug.'.delete';
        })
        ->method('delete')
        ->confirm('voyager::bread.delete_type_confirm', null, 'red')
        ->success('voyager::bread.delete_type_success', null, 'green')
        ->displayDeletable()
        ->reloadAfter()
        ->permission('delete');

        $restore_action = (new Action('voyager::generic.restore', 'trash'))
        ->route(function ($bread) {
            return 'voyager.'.$bread->slug.'.restore';
        })
        ->method('patch')
        ->confirm('voyager::bread.restore_type_confirm', null, 'yellow')
        ->success('voyager::bread.restore_type_success', null, 'green')
        ->displayRestorable()
        ->reloadAfter()
        ->permission('restore');

        $this->breadmanager->addAction(
            $read_action,
            $edit_action,
            $delete_action,
            $restore_action
        );
    }

    /**
     * Register the default BREAD actions (multiple entries).
     *
     * @return void
     */
    public function registerBulkActions()
    {
        $add_action = (new Action('voyager::generic.add_type', 'plus', 'green'))
        ->route(function ($bread) {
            return 'voyager.'.$bread->slug.'.add';
        })
        ->bulk();

        $delete_action = (new Action('voyager::bread.delete_type', 'trash', 'red'))
        ->route(function ($bread) {
            return 'voyager.'.$bread->slug.'.delete';
        })
        ->method('delete')
        ->confirm('voyager::bread.delete_type_confirm', null, 'red')
        ->success('voyager::bread.delete_type_success', null, 'green')
        ->bulk()
        ->displayDeletable()
        ->reloadAfter();

        $restore_action = (new Action('voyager::bread.restore_type', 'trash', 'yellow'))
        ->route(function ($bread) {
            return 'voyager.'.$bread->slug.'.restore';
        })
        ->method('patch')
        ->confirm('voyager::bread.restore_type_confirm', null, 'yellow')
        ->success('voyager::bread.restore_type_success', null, 'green')
        ->bulk()
        ->displayRestorable()
        ->reloadAfter();

        $this->breadmanager->addAction(
            $add_action,
            $delete_action,
            $restore_action
        );
    }

    /**
     * Fetch enabled formfield plugins and register them with the BREAD manager.
     */
    public function loadPluginFormfields(): void
    {
        $this->pluginmanager->getAllPlugins()->filter(function ($plugin) {
            return $plugin instanceof FormfieldPlugin;
        })->each(function ($formfield) {
            $this->breadmanager->addFormfield($formfield->getFormfield());
        });
    }

    /**
     * Register all policies from the BREADs.
     *
     * @param Collection $breads A collection of the Voyager apps current BREADs.
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
     * Register the menu items for each BREAD builder.
     *
     * @param Collection $breads A collection of the Voyager apps current BREADs.
     */
    public function registerBreadBuilderMenuItem(Collection $breads): void
    {
        $bread_builder_item = (new MenuItem(__('voyager::generic.bread'), 'bread', true))
                                ->permission('browse', ['breads'])
                                ->route('voyager.bread.index');

        $this->menumanager->addItems($bread_builder_item);

        $breads->each(static function ($bread) use ($bread_builder_item) {
            $bread_builder_item->addChildren(
                (new MenuItem($bread->name_plural, $bread->icon, true))->permission('edit', [$bread->table])
                    ->route('voyager.bread.edit', ['table' => $bread->table])
            );
        });
    }

    /**
     * Register BREAD-browse menu items for all BREADs.
     * 
     * @param Collection $breads A collection of the Voyager apps current BREADs.
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

    /**
     * Register generic menu items.
     */
    private function registerMenuItems()
    {
        $this->menumanager->addItems(
            (new MenuItem(__('voyager::generic.dashboard'), 'home', true))->permission('browse', ['admin'])->route('voyager.dashboard')->exact()
        );
        $this->menumanager->addItems(
            (new MenuItem(__('voyager::generic.media'), 'photograph', true))->permission('browse', ['media'])->route('voyager.media')
        );

        if ($this->settingmanager->setting('admin.ui-components', true)) {
            $this->menumanager->addItems(
                (new MenuItem(__('voyager::generic.ui_components'), 'template', true))->permission('browse', ['ui'])->route('voyager.ui')
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
        app()->singleton(MenuManager::class, function () {
            return $this->menumanager;
        });

        $this->settingmanager = new SettingManager();
        app()->singleton(SettingManager::class, function () {
            return $this->settingmanager;
        });

        $this->pluginmanager = new PluginManager($this->menumanager, $this->settingmanager);
        app()->singleton(PluginManager::class, function () {
            return $this->pluginmanager;
        });

        $this->breadmanager = new BreadManager();
        app()->singleton(BreadManager::class, function () {
            return $this->breadmanager;
        });

        app()->singleton('voyager', function () {
            return new Voyager($this->breadmanager, $this->pluginmanager, $this->settingmanager);
        });

        $this->settingmanager->load();

        $this->commands(DevCommand::class);
        $this->commands(InstallCommand::class);
        $this->commands(PluginsCommand::class);

        $this->registerFormfields();

        app()->booted(function () {
            $this->registerRoutes($this->breadmanager->getBreads());
        });
    }

    /**
     * Register all core formfields.
     */
    private function registerFormfields()
    {
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Checkbox::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\DynamicSelect::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\MediaPicker::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Number::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Password::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Radio::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Select::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\SimpleArray::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Slug::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Tags::class);
        $this->breadmanager->addFormfield(\Voyager\Admin\Formfields\Text::class);
    }
}
