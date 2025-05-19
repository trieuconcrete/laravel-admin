<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Enum\UserStatus;

class UserExport extends BaseExport
{
    /**
     * Summary of headings
     * @return string[]
     */
    public function headings(): array
    {
        return ['ID', 'Full Name', 'Email', 'Phone', 'Role', 'Status', 'Created at'];
    }

    /**
     * Summary of map
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->full_name,
            $user->email,
            $user->phone,
            $user->role,
            UserStatus::from($user->status)->label(),
            $user->created_at,
        ];
    }
}
