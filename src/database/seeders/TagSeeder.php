<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'AI', 'slug' => 'ai'],
            ['name' => 'GPT', 'slug' => 'gpt'],
            ['name' => 'Blog', 'slug' => 'blog'],
            ['name' => 'SEO', 'slug' => 'seo'],
            ['name' => 'Content', 'slug' => 'content'],
            ['name' => 'Marketing', 'slug' => 'marketing'],
            ['name' => 'Code', 'slug' => 'code'],
            ['name' => 'Python', 'slug' => 'python'],
            ['name' => 'JavaScript', 'slug' => 'javascript'],
            ['name' => 'Data', 'slug' => 'data'],
            ['name' => 'Analysis', 'slug' => 'analysis'],
            ['name' => 'Business', 'slug' => 'business'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Writing', 'slug' => 'writing'],
            ['name' => 'Creative', 'slug' => 'creative'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
