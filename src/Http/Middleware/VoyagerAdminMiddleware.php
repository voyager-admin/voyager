<?php

namespace Voyager\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware as InertiaMiddleware;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Contracts\Plugins\AuthenticationPlugin;
use Voyager\Admin\Plugins\AuthenticationPlugin as DefaultAuthPlugin;


class VoyagerAdminMiddleware extends InertiaMiddleware
{
    protected $pluginmanager;

    public function __construct(PluginManager $pluginmanager)
    {
        $this->pluginmanager = $pluginmanager;
    }
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $plugin = $this->pluginmanager->getAllPlugins()->filter(function ($plugin) {
            return $plugin instanceof AuthenticationPlugin;
        })->first() ?? new DefaultAuthPlugin();

        return $plugin->handleRequest($request, $next);
    }

    // Inertia specific functionality:

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'voyager::app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            //
        ]);
    }
}
