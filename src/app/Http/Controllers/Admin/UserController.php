<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
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
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
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
                'username' => 'nullable|max:100|unique:users,username',
                'birthday' => 'nullable|date',
                'password' => [
                    'required',
                    'confirmed',
                    'min:6'
                ],
                'role' => 'required',
                'status' => 'required|boolean',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $data = $request->only('full_name', 'email', 'role', 'status', 'birthday', 'username');
            $data['password'] = Hash::make($request->password);

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            User::create($data);

            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', compact('user'));
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
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }
        
            $user->update($data);
        
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
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

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}
