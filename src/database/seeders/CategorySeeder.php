<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Viết Content',
                'slug' => 'viet-content',
                'description' => 'Các prompt hỗ trợ viết nội dung, bài viết',
                'icon' => '✍️',
                'color' => '#3B82F6',
                'background_color' => '#EFF6FF',
                'prompt_count' => 0
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Prompt cho marketing, quảng cáo',
                'icon' => '📈',
                'color' => '#10B981',
                'background_color' => '#ECFDF5',
                'prompt_count' => 0
            ],
            [
                'name' => 'SEO',
                'slug' => 'seo',
                'description' => 'Prompt tối ưu SEO, nghiên cứu từ khóa, tạo meta description và cải thiện nội dung',
                'icon' => '🔍',
                'color' => '#3B82F6',
                'background_color' => '#EFF6FF',
                'prompt_count' => 0
            ],
            [
                'name' => 'Lập trình',
                'slug' => 'lap-trinh',
                'description' => 'Prompt hỗ trợ lập trình, code',
                'icon' => '💻',
                'color' => '#8B5CF6',
                'background_color' => '#F5F3FF',
                'prompt_count' => 0
            ],
            [
                'name' => 'Phân tích dữ liệu',
                'slug' => 'phan-tich-du-lieu',
                'description' => 'Prompt cho phân tích và xử lý dữ liệu',
                'icon' => '🔬',
                'color' => '#EF4444',
                'background_color' => '#FEF2F2',
                'prompt_count' => 0
            ],
            [
                'name' => 'Giáo dục',
                'slug' => 'giao-duc',
                'description' => 'Prompt cho giảng dạy và học tập',
                'icon' => '📚',
                'color' => '#F59E0B',
                'background_color' => '#FFFBEB',
                'prompt_count' => 0
            ],
            [
                'name' => 'Kinh doanh',
                'slug' => 'kinh-doanh',
                'description' => 'Prompt cho kinh doanh và quản lý',
                'icon' => '💼',
                'color' => '#6366F1',
                'background_color' => '#EEF2FF',
                'prompt_count' => 0
            ],
            [
                'name' => 'Trợ lý cá nhân',
                'slug' => 'tro-ly-ca-nhan',
                'description' => 'Prompt cho lập kế hoạch, quản lý thời gian, ghi chú và các công việc cá nhân',
                'icon' => '🗓️',
                'color' => '#6366F1',
                'background_color' => '#EEF2FF',
                'prompt_count' => 0
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
