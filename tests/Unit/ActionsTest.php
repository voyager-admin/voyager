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

    public function test_can_hide_action_on_bread()
    {
        resolve(BreadManager::class)->addAction(
            (new Action('HiddenAction', 'book-open', 'accent'))->method('get')->displayOnBread(function ($bread) {
                return $bread->name_plural !== 'Users';
            })
        );

        $this->assertTrue($this->post(route('voyager.users.data'))->original['actions']->filter(function ($action) {
            return $action->title == 'HiddenAction';
        })->count() == 0);
    }

    public function test_can_add_download_action()
    {
        resolve(BreadManager::class)->addAction(
            (new Action('Download', 'book-open', 'accent'))->method('get')->download('list.txt')
        );

        $this->assertTrue($this->post(route('voyager.users.data'))->original['actions']->filter(function ($action) {
            return $action->title == 'Download';
        })->count() >= 1);
    }
}
