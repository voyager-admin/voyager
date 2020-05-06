<?php

namespace Voyager\Admin\Contracts\Plugins;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

abstract class IsAuthenticationPlugin extends IsGenericPlugin
{
    public abstract function user(): ?object;

    public abstract function name(): ?string;

    public abstract function guard(): string;

    public abstract function authenticate(Request $request);

    public abstract function logout();

    public abstract function redirectTo();

    public abstract function forgotPassword(Request $request);

    public abstract function handleRequest(Request $request, Closure $next);

    public abstract function loginView(): ?View;

    public function forgotPasswordView(): ?View
    {
        return null;
    }
}
