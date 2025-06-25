<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalaryDetail;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalaryDetailPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('salary.view_any');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalaryDetail  $salaryDetail
     * @return mixed
     */
    public function view(User $user, SalaryDetail $salaryDetail)
    {
        return $user->hasPermission('salary.view') || 
               $user->id === $salaryDetail->employee->managed_by;
    }

    /**
     * Determine whether the user can process payment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalaryDetail  $salaryDetail
     * @return mixed
     */
    public function process(User $user, SalaryDetail $salaryDetail)
    {
        // Only allow processing if:
        // 1. User has permission to process any salary, OR
        // 2. User is the manager of the employee and has permission to process their team's salaries
        return $user->hasPermission('salary.process_payment') || 
               ($user->hasPermission('salary.process_team_payment') && 
                $user->id === $salaryDetail->employee->managed_by);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('salary.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalaryDetail  $salaryDetail
     * @return mixed
     */
    public function update(User $user, SalaryDetail $salaryDetail)
    {
        // Only allow update if salary is not yet paid
        if ($salaryDetail->status === 'paid') {
            return false;
        }
        
        return $user->hasPermission('salary.update') || 
               ($user->hasPermission('salary.update_team') && 
                $user->id === $salaryDetail->employee->managed_by);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalaryDetail  $salaryDetail
     * @return mixed
     */
    public function delete(User $user, SalaryDetail $salaryDetail)
    {
        // Only allow delete if salary is not yet paid
        if ($salaryDetail->status === 'paid') {
            return false;
        }
        
        return $user->hasPermission('salary.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalaryDetail  $salaryDetail
     * @return mixed
     */
    public function restore(User $user, SalaryDetail $salaryDetail)
    {
        return $user->hasPermission('salary.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalaryDetail  $salaryDetail
     * @return mixed
     */
    public function forceDelete(User $user, SalaryDetail $salaryDetail)
    {
        return $user->hasPermission('salary.force_delete');
    }
}
