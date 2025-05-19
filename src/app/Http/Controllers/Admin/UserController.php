<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Position;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Models\DriverLicense;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Enum\UserStatus as EnumUserStatus;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('employee_code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->whereNull('deleted_at')->latest()->paginate(10);
        $positions = Position::pluck('name', 'id');
        $licenses = DriverLicense::getCarLicenseTypes();
        $statuses = EnumUserStatus::options();
        
        return view('admin.users.index', compact([
            'users',
            'positions', 
            'licenses',
            'statuses'
        ]));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\User\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->userService->store($request);

            DB::commit();

            return response()->json(['message' => 'User created successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $positions = Position::pluck('name', 'id');
        $licenses = DriverLicense::getCarLicenseTypes();
        $statuses = EnumUserStatus::options();
        $licenseStatuses = DriverLicense::getStatuses();

        return view('admin.users.show', compact('user', 'positions', 'licenses', 'statuses', 'licenseStatuses'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $this->authorize('update', $user);

            $this->userService->update($request, $user);

            DB::commit();
            
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('User creation failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Summary of destroy
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
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
