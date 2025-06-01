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
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'Bài viết về Laravel Framework - PHP framework phổ biến cho web development',
                'color' => '#FF2D20',
                'order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'PHP',
                'slug' => 'php',
                'description' => 'Hướng dẫn và kinh nghiệm lập trình PHP',
                'color' => '#777BB4',
                'order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'JavaScript',
                'slug' => 'javascript',
                'description' => 'JavaScript tips, tricks và các framework như Vue, React',
                'color' => '#F7DF1E',
                'order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Python',
                'slug' => 'python',
                'description' => 'Python programming và ứng dụng trong AI/ML',
                'color' => '#3776AB',
                'order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'DevOps',
                'slug' => 'devops',
                'description' => 'CI/CD, Docker, Kubernetes và các công cụ DevOps',
                'color' => '#0082C9',
                'order' => 5,
                'is_active' => true
            ],
            [
                'name' => 'AWS',
                'slug' => 'aws',
                'description' => 'Amazon Web Services - Cloud Computing',
                'color' => '#FF9900',
                'order' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Database',
                'slug' => 'database',
                'description' => 'MySQL, PostgreSQL, MongoDB và database optimization',
                'color' => '#336791',
                'order' => 7,
                'is_active' => true
            ],
            [
                'name' => 'AI & Machine Learning',
                'slug' => 'ai-machine-learning',
                'description' => 'Artificial Intelligence, Machine Learning và Deep Learning',
                'color' => '#FF6F61',
                'order' => 8,
                'is_active' => true
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Kinh nghiệm và best practices trong phát triển web',
                'color' => '#4CAF50',
                'order' => 9,
                'is_active' => true
            ],
            [
                'name' => 'Tips & Tricks',
                'slug' => 'tips-tricks',
                'description' => 'Mẹo vặt và thủ thuật lập trình',
                'color' => '#9C27B0',
                'order' => 10,
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}