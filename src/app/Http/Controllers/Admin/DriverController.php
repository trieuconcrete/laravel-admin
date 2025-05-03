<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;

class DriverController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(10);
        return view('admin.drivers.index', compact('users'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'status' => $request->has('status') ? 1 : 0,
            ]);
            $request->validate([
                'full_name' => 'required',
                'email' => 'required|email|unique:users',
                'birthday' => 'nullable|date',
                'phone' => 'nullable|string',
                'status' => 'required|boolean',
            ]);

            $data = $request->only('full_name', 'email', 'status', 'birthday');

            User::create($data);

            return redirect()->route('admin.drivers.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('admin.drivers.view', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('admin.drivers.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $this->authorize('update', $user);
            $request->merge([
                'status' => $request->has('status') ? 1 : 0,
            ]);
            $request->validate([
                'full_name' => 'required|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'username' => 'nullable|max:100|unique:users,username,' . ($user->id ?? 'NULL') . ',id',
                'birthday' => 'nullable|date',
                'phone' => 'nullable|string',
                'role' => 'required',
                'password' => [
                    'nullable',
                    'confirmed',
                    'min:6'
                ],
                'status' => 'required|boolean',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
        
            $data = $request->only('full_name', 'email', 'role', 'status', 'birthday', 'username');
        
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
        
            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }
        
            $user->update($data);
        
            return redirect()->route('admin.drivers.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
}
