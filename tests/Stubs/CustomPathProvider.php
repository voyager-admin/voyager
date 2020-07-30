<?php

namespace Voyager\Admin\Tests\Stubs;

use Illuminate\Support\ServiceProvider;
use Voyager\Admin\Facades\Voyager;

class CustomPathProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Voyager::path('/voyager');
    }
}
