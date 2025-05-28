<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactMessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy một số service IDs để gán cho messages
        $websiteDesignId = Service::where('slug', 'thiet-ke-website')->first()?->id;
        $mobileAppId = Service::where('slug', 'phat-trien-ung-dung-mobile')->first()?->id;
        $digitalMarketingId = Service::where('slug', 'digital-marketing')->first()?->id;
        $managementSoftwareId = Service::where('slug', 'phan-mem-quan-ly')->first()?->id;

        $messages = [
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@example.com',
                'phone' => '0901234567',
                'service_id' => $websiteDesignId,
                'subject' => 'Tư vấn thiết kế website bán hàng',
                'message' => 'Chào bạn, tôi đang cần tư vấn về việc thiết kế website bán hàng cho cửa hàng thời trang của tôi. Vui lòng liên hệ lại giúp tôi.',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'status' => 'new',
                'created_at' => now()->subDays(2)
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@example.com',
                'phone' => '0912345678',
                'service_id' => $mobileAppId,
                'subject' => 'Báo giá phát triển app mobile',
                'message' => 'Công ty tôi cần phát triển một ứng dụng mobile cho nhân viên nội bộ. Xin gửi báo giá chi tiết về dịch vụ này.',
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'status' => 'read',
                'read_at' => now()->subDay(),
                'created_at' => now()->subDays(3)
            ],
            [
                'name' => 'Lê Hoàng Cường',
                'email' => 'lehoangcuong@example.com',
                'phone' => '0923456789',
                'service_id' => $digitalMarketingId,
                'subject' => 'Hợp tác dài hạn về Digital Marketing',
                'message' => 'Chúng tôi đang tìm đối tác để triển khai chiến dịch Digital Marketing dài hạn. Mong muốn được trao đổi thêm về khả năng hợp tác.',
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X)',
                'status' => 'replied',
                'read_at' => now()->subDays(4),
                'replied_at' => now()->subDays(3),
                'admin_notes' => 'Đã gửi email báo giá và lịch hẹn meeting',
                'created_at' => now()->subDays(5)
            ],
            [
                'name' => 'Phạm Minh Đức',
                'email' => 'phamminhduc@example.com',
                'service_id' => $managementSoftwareId,
                'subject' => 'Yêu cầu demo phần mềm quản lý',
                'message' => 'Tôi muốn xem demo phần mềm quản lý kho hàng. Liệu có thể sắp xếp một buổi demo online được không?',
                'ip_address' => '192.168.1.103',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124',
                'status' => 'new',
                'created_at' => now()->subHours(6)
            ],
            [
                'name' => 'Hoàng Thị Mai',
                'email' => 'hoangthimai@example.com',
                'phone' => '0934567890',
                'service_id' => null, // Liên hệ chung, không chọn dịch vụ cụ thể
                'subject' => 'Tư vấn tổng thể về giải pháp công nghệ',
                'message' => 'Chúng tôi cần tư vấn tổng thể về việc chuyển đổi số cho doanh nghiệp. Mong muốn được tư vấn về nhiều dịch vụ khác nhau.',
                'ip_address' => '192.168.1.105',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/89.0',
                'status' => 'new',
                'created_at' => now()->subDays(1)
            ],
            [
                'name' => 'Test Spam',
                'email' => 'spam@spam.com',
                'service_id' => null,
                'subject' => 'Buy cheap products',
                'message' => 'Click here to buy cheap products...',
                'ip_address' => '192.168.1.104',
                'user_agent' => 'SpamBot/1.0',
                'status' => 'spam',
                'created_at' => now()->subDays(10)
            ]
        ];

        foreach ($messages as $message) {
            ContactMessage::create($message);
        }
    }
}
