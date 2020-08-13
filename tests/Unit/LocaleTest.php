<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class LocaleTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_default_locales()
    {
        $this->assertCount(1, VoyagerFacade::getLocales());
        $this->assertEquals(VoyagerFacade::getLocales()[0], 'en');
    }

    public function test_can_add_locale()
    {
        VoyagerFacade::addLocale('de');

        $this->assertCount(1, VoyagerFacade::getLocales());
        $this->assertEquals(VoyagerFacade::getLocales()[0], 'de');
    }

    public function test_can_override_locales()
    {
        VoyagerFacade::setLocales(['fr', 'pt']);

        $this->assertCount(2, VoyagerFacade::getLocales());
        $this->assertEquals(VoyagerFacade::getLocales()[0], 'fr');
        $this->assertEquals(VoyagerFacade::getLocales()[1], 'pt');
    }

    public function test_can_get_fallback_locale()
    {
        $this->assertEquals(VoyagerFacade::getLocale(), VoyagerFacade::getFallbackLocale());
    }
}
