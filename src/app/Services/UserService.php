<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\ImageHelper;
use App\Models\User;
use App\Repositories\Interface\UserRepositoryInterface as UserRepository;
use App\Repositories\Interface\DriverLicenseRepositoryInterface as DriverLicenseRepository;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Storage;
use App\Models\DriverLicense;
use App\Constants;
use App\Models\Position;
use App\Repositories\Interface\PositionRepositoryInterface as PositionRepository;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Summary of __construct
     * @param \App\Repositories\Interface\UserRepositoryInterface $userRepository
     * @param \App\Repositories\Interface\DriverLicenseRepositoryInterface $driverLicenseRepository
     * @param \App\Repositories\Interface\PositionRepositoryInterface $positionRepository
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected DriverLicenseRepository $driverLicenseRepository,
        protected PositionRepository $positionRepository
    ) {}

    /**
     * Store a newly created user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\User
     * @throws \Exception
     */
    public function store(Request $request)
    {
        try {
            $isDriver = (bool) $request->add_driver;

            $data = $request->all();

            // Format salary
            $data['salary_base'] = $request->salary_base ? str_replace(',', '', $request->salary_base) : 0;

            // Generate password
            $data['password'] = Hash::make($data['password'] ?? 'password');

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $data['avatar'] = ImageHelper::upload($request->file('avatar'));
            }

            // Set role and position
            $data['role'] = $isDriver ? User::ROLE_DRIVER : User::ROLE_STAFF;
            if ($isDriver) {
                $position = $this->positionRepository->findBy(['code' => Position::POSITION_TX]);
                $data['position_id'] = $position->id ?? null;
            }

            // Create user
            $user = $this->userRepository->create($data);
            if (!$user) {
                throw new \Exception('Create user failed');
            }

            // If is driver, create driver license
            if ($isDriver) {
                $this->driverLicenseRepository->create([
                    'user_id' => $user->id,
                    'license_type' => $request->license_type,
                    'expiry_date' => $request->license_expire_date,
                    'license_number' => null,
                    'issue_date' => Carbon::today(),
                    'issued_by' => null
                ]);
            }

            // Assign position
            $user->assignPosition((int) $user->position_id);

            return $user;
        } catch (\Throwable $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \App\Models\User $user
     */
    public function update(Request $request, User $user): User
    {
        $data = $request->all();

        // handle salary
        if ($request->user_action == Constants::USER_ACTION_CHANGE_INFORMATION) {
            $data['salary_base'] = $request->salary_base ? str_replace(',', '', $request->salary_base) : 0;
        }

        // Handle password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // Handle avatar upload
        if ($request->avatar) {
            $data['avatar'] = ImageHelper::replace($user->avatar, $data['avatar'], 'avatars');
        } else {
            unset($data['avatar']);
        }

        // Update user
        $user = $this->userRepository->update($user->id, $data);

        // Handle Driver License
        if ($request->user_action == Constants::USER_ACTION_CHANGE_LICENSE) {
            $this->updateDriverLicense($user, $request);
        }

        return $user;
    }

    /**
     * Summary of updateDriverLicense
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function updateDriverLicense(User $user, Request $request): void
    {
        $license = $user->license ?? new DriverLicense(['user_id' => $user->id]);
        $licenseData = $request->all();
        $licenseData['status'] = $request->license_status;

        if ($request->license_file) {
            if ($license->license_file && Storage::disk('public')->exists($license->license_file)) {
                Storage::disk('public')->delete($license->license_file);
            }
            $licenseData['license_file'] = ImageHelper::replace($license->license_file, $request->license_file, 'licenses');
        } else {
            unset($licenseData['license_file']);
        }

        $license->fill($licenseData)->save();
    }
}
