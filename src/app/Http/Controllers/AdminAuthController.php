<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1;
        $remember = $request->has('remember');
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->intended('/admin/contacts');
        }

        return back()->withErrors([
            'login_error' => 'Email hoặc mật khẩu không đúng.',
        ]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }
}
