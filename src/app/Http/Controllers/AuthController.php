<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Session::has('jwt_token')) {
            return redirect('/scores');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $api = new ApiClient();
        $result = $api->login($request->email, $request->password);

        if ($result['success']) {
            return redirect('/scores');
        }

        return back()->withErrors(['login' => $result['message']])->withInput();
    }

    public function logout()
    {
        Session::forget(['jwt_token', 'user']);
        return redirect('/login');
    }
}
