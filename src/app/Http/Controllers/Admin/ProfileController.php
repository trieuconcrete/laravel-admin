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
        $user = Auth::guard('admin')->user();

        $validator = validator($request->all(), [
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
        
        if ($validator->fails()) {
            return redirect()->route('admin.profile.show')
                ->withErrors($validator)
                ->withInput()
                ->with('activeTab', 'personalDetails');
        }

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
            $user->email = $request->input('email');
            $user->save();

            return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.profile.show')
                ->with('error', 'Update failed: ' . $e->getMessage())
                ->with('activeTab', 'personalDetails');
        }
    }

    public function changePassword(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $validator = validator($request->all(), [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.profile.show')
                ->withErrors($validator)
                ->withInput()
                ->with('activeTab', 'changePassword');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.profile.show')->with('success', 'Password changed successfully!');
    }
}
