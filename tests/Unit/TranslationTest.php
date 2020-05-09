<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Voyager\Admin\Facades\Voyager;

class TranslationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_translate_json_string()
    {
        $string = json_encode([
            'en' => 'English',
            'de' => 'Deutsch'
        ]);

        $english = Voyager::translate($string, 'en', 'de');
        $german = Voyager::translate($string, 'de', 'en');

        $this->assertTrue($english == 'English');
        $this->assertTrue($german == 'Deutsch');
    }

    public function test_can_translate_array()
    {
        $array = [
            'en' => 'English',
            'de' => 'Deutsch'
        ];

        $english = Voyager::translate($array, 'en', 'de');
        $german = Voyager::translate($array, 'de', 'en');
        $fallback = Voyager::translate($array, 'xy', 'en');

        $this->assertTrue($english == 'English');
        $this->assertTrue($german == 'Deutsch');
        $this->assertTrue($fallback == $english);
    }

    public function test_can_not_translate_invalid_json_string()
    {
        $string = 'Not-Json';
        $result = Voyager::translate($string, 'en', 'de');
        $this->assertTrue($result == $string);
    }
}
