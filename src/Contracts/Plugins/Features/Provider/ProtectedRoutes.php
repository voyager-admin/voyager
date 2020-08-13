<?php

namespace Voyager\Admin\Contracts\Plugins\Features\Provider;

interface ProtectedRoutes
{
    public function provideProtectedRoutes(): void;
}