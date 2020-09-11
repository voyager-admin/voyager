<?php

namespace Voyager\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Voyager\Admin\Facades\Voyager as VoyagerFacade;

class AuthController extends Controller
{
    public function login()
    {
        if (VoyagerFacade::auth()->user()) {
            return redirect(VoyagerFacade::auth()->redirectTo());
        }

        return view('voyager::login');
    }

    public function processLogin(Request $request)
    {
        return VoyagerFacade::auth()->authenticate($request);
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
