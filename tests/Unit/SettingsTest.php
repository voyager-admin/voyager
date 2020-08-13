<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class SettingsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_get_settings_group()
    {
        $settings = VoyagerFacade::setting('admin');
        $this->assertTrue(
            is_array($settings) && count($settings) > 0
        );
    }
}
