<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Tests\Unit\TestCase;

class AuthTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_logout()
    {
        Auth::loginUsingId(1);
        $this->get(route('voyager.logout'))
             ->assertRedirect(route('voyager.login'));
    }

    public function test_can_login()
    {
        $this->get(route('voyager.login'))
             ->assertSeeText('Sign in to your account');
    }
}
