<?php

namespace Voyager\Admin\Contracts\Plugins;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

abstract class IsAuthenticationPlugin extends IsGenericPlugin
{
    abstract public function user(): ?object;

    abstract public function name(): ?string;

    abstract public function guard(): string;

    abstract public function authenticate(Request $request);

    abstract public function logout();

    abstract public function redirectTo();

    abstract public function forgotPassword(Request $request);

    abstract public function handleRequest(Request $request, Closure $next);

    abstract public function loginView(): ?View;

    public function forgotPasswordView(): ?View
    {
        return null;
    }
}
