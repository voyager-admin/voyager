<?php

namespace Voyager\Admin\Tests\Unit;

use Illuminate\Support\Facades\Auth;
use Voyager\Admin\Classes\Action;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;
use Voyager\Admin\Manager\Breads as BreadManager;

class ActionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function test_can_not_add_action_with_wrong_method()
    {
        $message = null;

        try {
            resolve(BreadManager::class)->addAction(
                (new Action('', 'book-open', 'accent'))->method('wrong-method')
            );
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        $this->assertEquals($message, 'Method "wrong-method" can not be used in an action!');
    }
}
