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

class UserService
{
    /**
     * Summary of __construct
     * @param \App\Repositories\Interface\UserRepositoryInterface $userRepository
     * @param \App\Repositories\Interface\DriverLicenseRepositoryInterface $driverLicenseRepository
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected DriverLicenseRepository $driverLicenseRepository
    ) {}

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['salary_base'] = str_replace(',', '', $request->salary_base);
            // general password
            $data['password'] = $request->password ? Hash::make($request->password) : Hash::make('password');

            // case have avatar
            if ($request->hasFile('avatar')) {
                $data['avatar'] = ImageHelper::upload($request->file('avatar'));
            }

            // set role
            $data['role'] = (bool) $request->add_driver ? User::ROLE_DRIVER : User::ROLE_STAFF;

            // create user
            if (!$user = $this->userRepository->create($data)) {
                throw new \Exception('Create user have error!');
            }

            // case create driver
            if ((bool) $request->add_driver) {
                $this->driverLicenseRepository->create([
                    'user_id' => $user->id,
                    'license_type' => $request->license_type,
                    'expiry_date' => $request->license_expire_date,
                    'license_number' => '',
                    'issue_date' => Carbon::today(),
                    'issued_by' => ''
                ]);
            }

            // case have position
            if ($request->position) {
                $user->assignPosition((int) $request->position);
            }

            return $user;
        } catch (\Exception $e) {
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
        if ($request->salary_base) {
            $data['salary_base'] = str_replace(',', '', $request->salary_base);
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
