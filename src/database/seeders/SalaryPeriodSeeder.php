<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalaryPeriod;
use Carbon\Carbon;
class SalaryPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo 3 kỳ lương gần nhất
        $currentMonth = Carbon::now();
        
        for ($i = 2; $i >= 0; $i--) {
            $month = $currentMonth->copy()->subMonths($i);
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
            $paymentDate = $endDate->copy()->addDays(10);
            
            $status = 'closed';
            if ($i == 0) {
                $status = 'open';
            } elseif ($i == 1) {
                $status = 'processing';
            }
            
            SalaryPeriod::create([
                'period_name' => 'Tháng ' . $month->format('m/Y'),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => $paymentDate,
                'status' => $status,
                'notes' => 'Kỳ lương tháng ' . $month->format('m/Y'),
            ]);
        }
    }
}
