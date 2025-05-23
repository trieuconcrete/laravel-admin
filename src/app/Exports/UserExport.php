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
        return ['ID', 'Họ và tên', 'Email', 'Số điện thoại', 'Chức vụ', 'Trạng thái', 'Ngày tạo'];
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
            $user->role ? User::getRoles()[$user->role] : '',
            UserStatus::from($user->status)->label(),
            $user->created_at,
        ];
    }
}
