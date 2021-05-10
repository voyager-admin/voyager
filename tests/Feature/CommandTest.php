<?php

namespace Voyager\Admin\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Tests\Unit\TestCase;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class CommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_enable_dev_server()
    {
        $this->artisan('voyager:dev --enable http://test.com')
             ->expectsOutput('Enabled development server with URL "http://test.com"')
             ->assertExitCode(0);
    }

    public function test_can_not_enable_dev_server_with_wrong_url()
    {
        $this->artisan('voyager:dev --enable WRONG')
             ->expectsOutput('Please provide a valid URL starting with http:// or https://')
             ->assertExitCode(0);
    }

    public function test_can_not_enable_dev_server_without_args()
    {
        $this->artisan('voyager:dev')
             ->expectsOutput('Please use --disable to disable or --enable [URL] to enable the development server')
             ->assertExitCode(0);
    }

    public function test_can_disable_dev_server()
    {
        $this->artisan('voyager:dev --enable http://test.com')
             ->expectsOutput('Enabled development server with URL "http://test.com"')
             ->assertExitCode(0);

        $this->artisan('voyager:dev --disable')
             ->expectsOutput('Disabled development server')
             ->assertExitCode(0);
    }
}
