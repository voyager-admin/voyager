<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Facades\Voyager;
use Voyager\Admin\Tests\Unit\TestCase;

class AssetsTest extends TestCase
{
    public function test_assets_css_loaded()
    {
        $this->get(Voyager::assetUrl('css/voyager.css'))
             ->assertStatus(200);
    }

    public function test_assets_js_loaded()
    {
        $this->get(Voyager::assetUrl('js/voyager.js'))
             ->assertStatus(200);
    }

    public function test_assets_not_existing_not_loaded()
    {
        $this->get(Voyager::assetUrl('some/wrong.file'))
             ->assertStatus(404);
    }

    public function test_assets_outside_file_not_loaded()
    {
        $this->get(Voyager::assetUrl('../../../../../../.env'))
             ->assertStatus(404);
    }
}
