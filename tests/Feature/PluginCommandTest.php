<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\Assert;
use Voyager\Admin\Tests\Unit\TestCase;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class PluginCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_disable_disable_enable_plugin()
    {
        // Disable plugin
        $this->artisan('voyager:plugins voyager-admin/voyager-testbench-plugin@ThemePlugin --disable')
             ->expectsOutput('Plugin "voyager-admin/voyager-testbench-plugin@ThemePlugin" was disabled!')
             ->assertExitCode(0);
    }
}
