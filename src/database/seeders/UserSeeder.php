<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 30 user random
        $roles = ['user', 'manager'];

        for ($i = 1; $i <= 30; $i++) {
            User::create([
                'full_name' => 'User ' . $i,
                'email' => 'user'.$i.'@test.com',
                'password' => Hash::make('password123'),
                'role' => $roles[array_rand($roles)], // random role user hoặc manager
                'status' => rand(0, 1), // 1: active, 0: inactive
            ]);
        }
    }
}
