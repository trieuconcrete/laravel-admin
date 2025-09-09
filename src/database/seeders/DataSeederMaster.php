<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataSeederMaster extends Seeder
{
    private bool $truncationCancelled = false;

    /**
     * Run the database seeds.
     * 
     * SAFETY FEATURES:
     * - Blocks execution in production environments
     * - Warns when running in staging
     * - Requires user confirmation before truncating data
     * - Supports --force flag for automated environments
     */
    public function run(): void
    {
        // Prevent running in production environment
        if (app()->environment(['production', 'prod', 'live'])) {
            $this->command->error('🚫 DataSeederMaster cannot be run in production environment!');
            $this->command->error('Current environment: ' . app()->environment());
            $this->command->error('This seeder truncates all data and is intended for development/testing only.');
            $this->command->info('💡 If you really need to run this in production, change the environment or modify the seeder.');
            return;
        }
        
        // Warn for staging environments
        if (app()->environment(['staging', 'stage'])) {
            $this->command->warn('⚠️  Running in STAGING environment!');
            $this->command->warn('Please ensure this is intentional as it will delete staging data.');
        }
        
        $this->command->info('🚀 Starting DataSeederMaster...');
        $this->command->info('Environment: ' . app()->environment());
        $this->command->info('This will truncate all data tables and reseed master data.');
        $this->command->info('💡 Use --force flag to skip confirmation prompts.');
        
        // First truncate all tables (with confirmation)
        $this->truncateAllTables();
        
        // Check if truncation was cancelled
        if ($this->truncationCancelled) {
            return;
        }
        
        $this->command->info('📊 Seeding master data...');
        $this->command->warn('🔧 Note: Individual seeders will skip their own truncate() calls since tables are already truncated.');
        
        // Set a flag to tell individual seeders to skip truncation
        app()->instance('seeder.skip_truncate', true);
        
        // Then seed the data
        $this->call([
            // Master/Reference data first
            PositionsSeeder::class,
            VehicleTypesSeeder::class,
            AllowanceTypeSeeder::class,
            DeductionTypeSeeder::class,
            ShipmentDeductionTypeSeeder::class,
            
            // Basic user data
            AdminSeeder::class,
            
            // Optional: Sample data (uncomment as needed)
            // CustomerSeeder::class,
            // VehiclesSeeder::class,
            // UserSeeder::class,
        ]);
        
        // Clean up the skip truncate flag
        app()->forgetInstance('seeder.skip_truncate');
        
        $this->command->info('✅ DataSeederMaster completed successfully!');
        $this->command->info('📝 Note: To add sample data, uncomment the optional seeders in DataSeederMaster.php');
    }

    /**
     * Truncate all tables in the correct order
     * 
     * This method handles foreign key constraints by:
     * 1. Disabling foreign key checks
     * 2. Truncating all tables (including master data tables)
     * 3. Re-enabling foreign key checks
     * 4. Setting a flag so individual seeders skip their own truncate calls
     */
    private function truncateAllTables(): void
    {
        $this->command->info('🗑️  About to truncate all data tables...');
        $this->command->warn('⚠️  WARNING: This will permanently delete all data from the following tables:');
        $this->command->warn('   - All user data, shipments, customers, vehicles, quotes, etc.');
        $this->command->warn('   - Master data will be reseeded automatically');
        
        // Ask for confirmation (skip if --force flag is used)
        if (!$this->command->option('force') && 
            !$this->command->confirm('Are you sure you want to proceed? This action cannot be undone.', false)) {
            $this->command->info('❌ Operation cancelled by user.');
            $this->command->info('💡 Tip: Use --force flag to skip confirmation in automated environments.');
            $this->truncationCancelled = true;
            return;
        }
        
        $this->command->info('🔄 Proceeding with data truncation...');
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Tables to truncate (mainly transactional data, not master/reference data)
        // Note: Master data tables like positions, vehicle_types, etc. will be handled by their respective seeders
        $tablesToTruncate = [
            // Child/Detail tables first
            'allowance_details',
            'deduction_details', 
            'salary_details',
            'salary_advance_deductions',
            'salary_advance_requests',
            'shipment_deductions',
            'shipment_goods',
            'shipment_reports',
            'toll_fees',
            'driver_licenses',
            'vehicle_documents',
            'maintenance_records',
            'car_rental_vehicle_logs',
            'car_rental_vehicles',
            'quote_attachments',
            'quote_items',
            'quote_histories',
            'transactions',
            'payments',
            'contracts',
            
            // Main transactional tables
            'shipments',
            'quotes',
            'vehicles',
            'car_rentals',
            'customers',
            'salary_periods',
            
            // Users table (will be reseeded with admin)
            'users',
            
            // Master/Reference tables (truncate here to avoid FK constraint issues in individual seeders)
            'positions',
            'vehicle_types',
            'allowance_types',
            'deduction_types',
            'shipment_deduction_types',
            
            // Other data tables
            'departments',
            'settings',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("✓ Truncated table: {$table}");
            } else {
                $this->command->warn("⚠ Table does not exist: {$table}");
            }
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('All data tables truncated successfully.');
    }
}
