<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $authors = DB::table('users')->pluck('id')->toArray();
        $categories = DB::table('categories')->pluck('id')->toArray();
        $tags = DB::table('tags')->pluck('id')->toArray();

        for ($i = 1; $i <= 10; $i++) {
            $title = "Bài viết số $i";
            $postId = DB::table('posts')->insertGetId([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . $i,
                'excerpt' => "Đây là tóm tắt cho $title",
                'content' => "Nội dung chi tiết của $title. Đây là dữ liệu mẫu để test.",
                'featured_image' => null,
                'author_id' => $authors[array_rand($authors)],
                'category_id' => $categories[array_rand($categories)],
                'status' => 'published',
                'views' => rand(10, 1000),
                'likes' => rand(0, 200),
                'meta_tags' => json_encode(['keyword1', 'keyword2']),
                'meta_title' => $title . " | Demo",
                'meta_description' => "Mô tả SEO cho $title",
                'published_at' => now()->subDays(rand(1, 30)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $randomTags = collect($tags)->random(rand(2, 4))->toArray();
            foreach ($randomTags as $tagId) {
                DB::table('post_tags')->insert([
                    'post_id' => $postId,
                    'tag_id' => $tagId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
