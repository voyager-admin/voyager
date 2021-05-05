<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Manager\Plugins as PluginManager;

class PluginsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_not_add_invalid_plugin()
    {
        $message = null;
        try {
            resolve(PluginManager::class)->addPlugin(new \stdClass());
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals($message, 'Plugin added to Voyager has to extend GenericPlugin');
    }

    public function test_can_write_enabled_plugins()
    {
        $this->assertTrue(resolve(PluginManager::class)->enablePlugin('voyager-admin/voyager-testbench-plugin@GenericPlugin') > 0);
        // TODO: Test if final file contains plugin
    }

    public function test_can_write_disabled_plugins()
    {
        $this->assertTrue(resolve(PluginManager::class)->disablePlugin('voyager-admin/voyager-testbench-plugin@GenericPlugin') > 0);
        // TODO: Test if final file contains plugin
    }

    public function test_can_not_enable_unknown_plugins()
    {
        $message = null;
        try {
            resolve(PluginManager::class)->enablePlugin('unknown-package@GenericPlugin');
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals($message, 'Plugin with identifier "unknown-package@GenericPlugin" is not registered and can not be enabled/disabled!');
    }
}
