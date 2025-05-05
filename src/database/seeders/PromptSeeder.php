<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Prompt;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PromptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $category_writing = Category::where('slug', 'viet-content')->first();
        $category_marketing = Category::where('slug', 'marketing')->first();
        $category_coding = Category::where('slug', 'lap-trinh')->first();

        $prompts = [
            [
                'user_id' => $user->id,
                'category_id' => $category_writing->id,
                'title' => 'Viết bài blog chuyên nghiệp',
                'slug' => 'viet-bai-blog-chuyen-nghiep',
                'description' => 'Prompt giúp viết bài blog chuyên nghiệp với cấu trúc rõ ràng',
                'content' => "Bạn là một chuyên gia viết content. Hãy viết một bài blog về chủ đề: {{topic}}

Yêu cầu:
- Độ dài: {{length}} từ
- Tone giọng: {{tone}}
- Đối tượng độc giả: {{audience}}

Cấu trúc bài viết:
1. Tiêu đề hấp dẫn
2. Mở đầu thu hút (đặt vấn đề)
3. Nội dung chính (chia thành các phần nhỏ với heading)
4. Kết luận và call-to-action
5. FAQ (nếu cần)

Hãy viết bài với phong cách tự nhiên, dễ đọc và cung cấp giá trị thực sự cho người đọc.",
                'example_output' => "Tiêu đề: 5 Bí Quyết Tăng Năng Suất Làm Việc Tại Nhà Hiệu Quả

Trong thời đại làm việc từ xa ngày càng phổ biến, việc duy trì năng suất cao tại nhà đã trở thành thách thức với nhiều người...",
                'variable_descriptions' => json_encode([
                    'topic' => 'Chủ đề bài viết',
                    'length' => 'Độ dài bài viết (số từ)',
                    'tone' => 'Giọng điệu (chuyên nghiệp, thân thiện, hài hước...)',
                    'audience' => 'Đối tượng độc giả mục tiêu'
                ]),
                'is_public' => true,
                'view_count' => 150,
                'use_count' => 45
            ],
            [
                'user_id' => $user->id,
                'category_id' => $category_marketing->id,
                'title' => 'Tạo quảng cáo Facebook hiệu quả',
                'slug' => 'tao-quang-cao-facebook-hieu-qua',
                'description' => 'Prompt tạo nội dung quảng cáo Facebook thu hút và chuyển đổi cao',
                'content' => "Bạn là chuyên gia marketing với kinh nghiệm tạo quảng cáo Facebook. Hãy tạo một quảng cáo cho:

Sản phẩm/Dịch vụ: {{product}}
Đối tượng mục tiêu: {{target_audience}}
Mục tiêu chiến dịch: {{campaign_goal}}
Ngân sách: {{budget}}

Yêu cầu:
1. Tiêu đề quảng cáo (Headline)
2. Nội dung chính (Primary text)
3. Mô tả (Description)
4. Call-to-action phù hợp
5. Gợi ý hình ảnh/video

Hãy tạo nội dung thu hút, ngắn gọn và thúc đẩy hành động.",
                'example_output' => "Tiêu đề: Giảm 50% - Khóa Học Marketing Online Chỉ Hôm Nay!

Nội dung chính: 🚀 Bạn muốn trở thành chuyên gia marketing?...",
                'variable_descriptions' => json_encode([
                    'product' => 'Sản phẩm hoặc dịch vụ cần quảng cáo',
                    'target_audience' => 'Đối tượng khách hàng mục tiêu',
                    'campaign_goal' => 'Mục tiêu chiến dịch (awareness, conversion, traffic...)',
                    'budget' => 'Ngân sách dự kiến'
                ]),
                'is_public' => true,
                'view_count' => 200,
                'use_count' => 80
            ],
            [
                'user_id' => $user->id,
                'category_id' => $category_coding->id,
                'title' => 'Giải thích code và debug',
                'slug' => 'giai-thich-code-va-debug',
                'description' => 'Prompt giúp giải thích code và tìm lỗi trong chương trình',
                'content' => "Bạn là một lập trình viên senior với nhiều năm kinh nghiệm. Hãy giúp tôi:

1. Giải thích code sau:
{{code}}

2. Ngôn ngữ lập trình: {{language}}

3. Vấn đề gặp phải: {{problem}}

Yêu cầu:
- Giải thích từng phần của code
- Xác định lỗi (nếu có)
- Đề xuất cách sửa lỗi
- Suggest cách tối ưu code (nếu cần)",
                'example_output' => "Giải thích code:
Đoạn code này thực hiện việc sắp xếp mảng sử dụng thuật toán bubble sort...",
                'variable_descriptions' => json_encode([
                    'code' => 'Đoạn code cần giải thích hoặc debug',
                    'language' => 'Ngôn ngữ lập trình',
                    'problem' => 'Mô tả vấn đề gặp phải'
                ]),
                'is_public' => true,
                'view_count' => 120,
                'use_count' => 60
            ]
        ];

        // Tạo prompts
        foreach ($prompts as $promptData) {
            $prompt = Prompt::create($promptData);
            
            // Gán tags cho mỗi prompt
            if ($prompt->category->slug === 'viet-content') {
                $tags = Tag::whereIn('slug', ['content', 'writing', 'blog', 'seo'])->get();
            } elseif ($prompt->category->slug === 'marketing') {
                $tags = Tag::whereIn('slug', ['marketing', 'content', 'business'])->get();
            } else {
                $tags = Tag::whereIn('slug', ['code', 'python', 'javascript'])->get();
            }
            
            $prompt->tags()->attach($tags);
        }
    }
}
