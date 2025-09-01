<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Công nghệ', 'color' => '#3B82F6'],
            ['name' => 'Kinh doanh', 'color' => '#10B981'],
            ['name' => 'Đời sống', 'color' => '#F59E0B'],
            ['name' => 'Giáo dục', 'color' => '#EF4444'],
            ['name' => 'Du lịch', 'color' => '#8B5CF6'],
        ];

        foreach ($categories as $index => $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => 'Mô tả cho ' . $category['name'],
                'color' => $category['color'],
                'order' => $index,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
