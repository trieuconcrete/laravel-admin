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
        // If user is already logged in, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Get the last email from session if it exists
        $lastEmail = session('last_email');
        
        return view('admin.login', compact('lastEmail'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1;
        $remember = $request->has('remember');
        
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Keep the email when login fails
        return back()->withErrors([
            'login_error' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        // Store the user's email in the session before logging out
        if (Auth::guard('admin')->check()) {
            session(['last_email' => Auth::guard('admin')->user()->email]);
        }
        
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
