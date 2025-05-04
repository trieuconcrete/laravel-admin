<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id('vehicle_type_id');
            $table->string('name', 100);
            $table->boolean('status')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
        // Seed data
        $this->seedData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }

    /**
     * Seed initial data
     */
    private function seedData(): void
    {
        $vehicleTypes = [
            [
                'name' => 'Xe tải',
                'description' => 'Xe tải',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Xe đầu kéo',
                'description' => 'Xe đầu kéo container, phù hợp cho vận chuyển hàng hóa quốc tế',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Xe thùng',
                'description' => 'Xe thùng',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Container',
                'description' => 'Container',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Xe ben',
                'description' => 'Xe tải chuyên dụng cho vận chuyển vật liệu xây dựng, cát, đá',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Xe bồn',
                'description' => 'Xe chở chất lỏng như xăng dầu, hóa chất, nước',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Xe chuyên dụng',
                'description' => 'Các loại xe chuyên dụng khác như xe chở rác, xe cứu hỏa',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Xe van',
                'description' => 'Xe van vận chuyển hàng hóa nhỏ trong đô thị',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('vehicle_types')->insert($vehicleTypes);
    }
};
