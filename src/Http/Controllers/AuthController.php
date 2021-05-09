<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $errors = [];
        $auth = VoyagerFacade::auth();
        if ($auth->user()) {
            return redirect($auth->redirectTo());
        }

        if ($request->method() == 'POST') {
            $result = $auth->authenticate($request);
            if ($result == null) {
                return redirect()->intended($auth->redirectTo());
            } else {
                $errors = $result;
            }
        }

        return $this->inertiaRender('Login', [
            'title'             => __('voyager::auth.login'),
            'welcome'           => VoyagerFacade::setting('admin.welcome', __('voyager::generic.welcome_to_voyager')),
            'has_password_view' => true,
            'errors'            => $errors,
        ]);
    }

    public function logout()
    {
        return VoyagerFacade::auth()->logout();
    }

    public function forgotPassword(Request $request)
    {
        return VoyagerFacade::auth()->forgotPassword($request);
    }
}
