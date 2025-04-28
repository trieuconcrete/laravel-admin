<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    public function collection()
    {
        return User::select('id', 'name', 'email', 'role', 'status', 'created_at')->get();
    }
}
