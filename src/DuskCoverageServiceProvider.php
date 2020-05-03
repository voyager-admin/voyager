<?php

namespace Voyager\Admin;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class DuskCoverageServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        if ($this->app->environment('production')) {
            throw new Exception('Do not use DuskCoverageServiceProvider in production!');
        }
        if (!$this->app->runningInConsole()) {
            if ($this->app->environment('testing')) {
                try {
                    $this->triggerCoverage();
                } catch (Exception $e) {
                    Log::error('Dusk coverage: ' . $e->getMessage());
                }
            }
        }
    }

    private function triggerCoverage() {
        $coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage();
        $coverage->filter()->addDirectoryToWhitelist(__DIR__);
        $name = microtime(true) . '.' . Str::random(8);
        $coverage->start($name);
        $this->app->terminating(function () use ($coverage, $name) {
            $coverage->stop();
            $writer = new \SebastianBergmann\CodeCoverage\Report\Clover;
            $writer->process($coverage, __DIR__."/../coverage-dusk.xml");
        });
    }
}