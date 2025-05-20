<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('admin.profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'phone' => [
                'required',
                'string',
                'regex:/^(0|\+84|84)(3[2-9]|5[6|8|9]|7[0|6-9]|8[1-5]|9[0-9])[0-9]{7}$/',
                'unique:users,phone,' . $user->id,
            ],
            'birthday' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        try {
            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->full_name = $request->input('full_name');
            $user->username = $request->input('username');
            $user->phone = $request->input('phone');
            $user->birthday = $request->input('birthday');
            $user->save();

            return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.profile.show')->with('success', 'Password changed successfully!');
    }
}
