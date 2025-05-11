<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            PositionsSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            UpdateUsersWithPositionsSeeder::class,
            DriverLicensesSeeder::class,
            VehiclesSeeder::class,
            CustomerSeeder::class,
            ContractSeeder::class,
            ShipmentSeeder::class,

            AllowanceTypeSeeder::class,
            DeductionTypeSeeder::class,
            SalaryPeriodSeeder::class,
            SalarySeeder::class,
        ]);
    }
}
