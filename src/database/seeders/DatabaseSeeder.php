<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order
        $this->call([
            AdminSeeder::class, // Create admin user
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
        ]);
        
        $this->command->info('Database seeding completed successfully!');
        $this->command->info('Admin login: admin@test.com / password');
    }
}