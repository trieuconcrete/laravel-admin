<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalaryAdvanceRequest;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class SalaryAdvanceRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SalaryAdvanceRequest $salaryAdvanceRequest): bool
    {
        // User có thể xem request của chính mình hoặc nếu là admin/manager
        return $user->id === $salaryAdvanceRequest->user_id || 
               $this->isAdminOrManager($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Kiểm tra xem user có đang cố gắng tạo request với status approved hoặc paid không
        $requestData = request()->all();
        
        if (isset($requestData['status']) && in_array($requestData['status'], [
            SalaryAdvanceRequest::STATUS_APPROVED, 
            SalaryAdvanceRequest::STATUS_PAID
        ])) {
            // Chỉ admin hoặc manager mới có thể tạo request với status approved hoặc paid
            return $this->isAdminOrManager($user);
        }
        
        return true; // Tất cả user đều có thể tạo request với status khác
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SalaryAdvanceRequest $salaryAdvanceRequest): bool
    {
        // Debug log để xem Policy được gọi
        Log::info('SalaryAdvanceRequestPolicy::update called', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'request_id' => $salaryAdvanceRequest->id,
            'request_status' => $salaryAdvanceRequest->status,
            'new_status' => request()->input('status')
        ]);

        return $this->canModifyRequest($user, $salaryAdvanceRequest);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SalaryAdvanceRequest $salaryAdvanceRequest): bool
    {
        return $this->isAdminOrManager($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SalaryAdvanceRequest $salaryAdvanceRequest): bool
    {
        return $this->isAdminOrManager($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SalaryAdvanceRequest $salaryAdvanceRequest): bool
    {
        return $this->isAdminOrManager($user);
    }

    /**
     * Kiểm tra xem user có phải là admin hoặc manager không
     */
    private function isAdminOrManager(User $user): bool
    {
        // Đơn giản hóa việc kiểm tra role
        $isAdminOrManager = $user->role === 'admin' || $user->role === 'manager';
        
        Log::info('Checking if user is admin or manager', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'is_admin_or_manager' => $isAdminOrManager
        ]);
        
        return $isAdminOrManager;
    }

    /**
     * Kiểm tra xem user có thể sửa đổi request hay không
     */
    private function canModifyRequest(User $user, SalaryAdvanceRequest $salaryAdvanceRequest): bool
    {
        $newStatus = request()->input('status');
        
        Log::info('Checking modify request permission', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'request_id' => $salaryAdvanceRequest->id,
            'current_status' => $salaryAdvanceRequest->status,
            'new_status' => $newStatus,
            'is_admin_or_manager' => $this->isAdminOrManager($user),
            'is_own_request' => $user->id === $salaryAdvanceRequest->user_id
        ]);
        
        // Nếu đang thay đổi status thành approved hoặc paid
        if ($newStatus && in_array($newStatus, [
            SalaryAdvanceRequest::STATUS_APPROVED, 
            SalaryAdvanceRequest::STATUS_PAID
        ])) {
            $canChange = $this->isAdminOrManager($user);
            Log::info('Status change to approved/paid', ['can_change' => $canChange]);
            return $canChange;
        }
        
        // Nếu status hiện tại là approved hoặc paid
        if (in_array($salaryAdvanceRequest->status, [
            SalaryAdvanceRequest::STATUS_APPROVED, 
            SalaryAdvanceRequest::STATUS_PAID
        ])) {
            $canModify = $this->isAdminOrManager($user);
            Log::info('Modifying approved/paid request', ['can_modify' => $canModify]);
            return $canModify;
        }

        // Với các status khác, user có thể sửa đổi request của chính mình hoặc admin/manager có thể sửa đổi bất kỳ request nào
        $canModifyOther = $user->id === $salaryAdvanceRequest->user_id || $this->isAdminOrManager($user);
        Log::info('Modifying other status request', ['can_modify_other' => $canModifyOther]);
        
        return $canModifyOther;
    }
}
