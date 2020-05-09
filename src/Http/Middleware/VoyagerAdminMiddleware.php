<?php

namespace Voyager\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Voyager\Admin\Manager\Plugins as PluginManager;
use Voyager\Admin\Plugins\AuthenticationPlugin;


class VoyagerAdminMiddleware
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
        $plugin = $this->pluginmanager->getPluginByType('authentication', AuthenticationPlugin::class);

        return $plugin->handleRequest($request, $next);
    }
}
