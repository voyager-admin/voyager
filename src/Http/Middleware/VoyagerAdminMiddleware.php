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
}
