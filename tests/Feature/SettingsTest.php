<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Facades\Voyager;
use Voyager\Admin\Tests\Unit\TestCase;

class SettingsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_browse_settings()
    {
        $this->get(route('voyager.settings.index'))
             ->assertStatus(200);
    }

    public function test_can_update_settings()
    {
        $this->postJson(route('voyager.settings.store'), ['settings' => $this->getSettingsJson()])
             ->assertStatus(200);
    }

    private function getSettingsJson()
    {
        return json_decode(file_get_contents(__DIR__."/../Stubs/settings.json"));
    }
}
