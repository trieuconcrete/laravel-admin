<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@test.com'
        ], [
            'full_name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }
}
