<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSeederMaster extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            AllowanceTypeSeeder::class,
            VehicleTypesSeeder::class,
            PositionsSeeder::class,
            DeductionTypeSeeder::class,
            ShipmentDeductionTypeSeeder::class,
        ]);
    }
}
