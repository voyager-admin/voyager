<?php

namespace Voyager\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Voyager\Admin\Facades\Plugins as PluginsFacade;
use Voyager\Admin\Plugins\AuthenticationPlugin;

class VoyagerAdminMiddleware
{
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
        $plugin = PluginsFacade::getPluginByType('authentication', AuthenticationPlugin::class);

        return $plugin->handleRequest($request, $next);
    }
}
