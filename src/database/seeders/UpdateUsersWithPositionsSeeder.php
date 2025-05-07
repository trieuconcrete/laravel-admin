<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UpdateUsersWithPositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đảm bảo đã có positions
        if (Position::count() === 0) {
            $this->call(PositionsSeeder::class);
        }

        // Lấy các vị trí
        $taixePosition = Position::where('code', 'TX')->first();
        $nhanvienPosition = Position::where('code', 'NV')->first();
        $giamDocPosition = Position::where('code', 'GD')->first();
        $truongPhongPosition = Position::where('code', 'TP')->first();

        // Cập nhật users hiện có
        $users = User::all();
        
        foreach ($users as $user) {
            // Không cập nhật user đã có employee_code
            if ($user->employee_code) {
                continue;
            }

            // Gán position dựa trên role
            switch ($user->role) {
                case User::ROLE_ADMIN:
                    $user->assignPosition($giamDocPosition->id);
                    break;
                
                case User::ROLE_MANAGER:
                    $user->assignPosition($truongPhongPosition->id);
                    break;
                
                case User::ROLE_DRIVER:
                    $user->assignPosition($taixePosition->id);
                    break;
                
                case User::ROLE_STAFF:
                case User::ROLE_USER:
                    $user->assignPosition($nhanvienPosition->id);
                    break;
                
                default:
                    // Nếu không có role, không gán position
                    break;
            }
        }
    }
}
