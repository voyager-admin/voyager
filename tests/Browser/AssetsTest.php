<?php

namespace Voyager\Admin\Tests\Browser;

use Laravel\Dusk\Browser as DuskBrowser;
use Voyager\Admin\Facades\Voyager;

class AssetsTest extends TestCase
{
    public function test_asset_js_loaded()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visit(Voyager::assetUrl('js/voyager.js'))
                ->assertDontSee('Not Found');
        });
    }

    public function test_asset_not_loaded()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visit(Voyager::assetUrl('some/wrong.file'))
                ->assertSee('Not Found');
        });
    }

    public function test_asset_cannot_load_file_outside()
    {
        $this->browse(function (DuskBrowser $browser) {
            $browser->visit(Voyager::assetUrl('../../../../../../.env'))
                ->assertSee('Not Found');
        });
    }
}
