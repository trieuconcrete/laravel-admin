<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Exception;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $status = Password::broker('users')->sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? back()->with('success', 'Reset link đã gửi đến email của bạn.')
                : back()->withErrors(['email' => 'Không thể gửi reset link.']);
        } catch (Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi gửi reset link: ' . $e->getMessage());
        }
    }
}
