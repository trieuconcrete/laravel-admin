<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Thiết kế Website',
                'description' => 'Thiết kế website chuyên nghiệp, responsive, tối ưu SEO với công nghệ hiện đại. Phù hợp với mọi loại hình doanh nghiệp từ Landing Page, Website giới thiệu đến Thương mại điện tử.',
                'icon' => 'fas fa-laptop-code',
                'icon_bg_class' => 'bg-primary',
                'order' => 1
            ],
            [
                'title' => 'Phát triển Ứng dụng Mobile',
                'description' => 'Phát triển ứng dụng di động native và cross-platform cho iOS và Android. Tích hợp các tính năng hiện đại, giao diện thân thiện, hiệu năng cao.',
                'icon' => 'fas fa-mobile-alt',
                'icon_bg_class' => 'bg-success',
                'order' => 2
            ],
            [
                'title' => 'Tư vấn Giải pháp CNTT',
                'description' => 'Tư vấn chiến lược số hóa, kiến trúc hệ thống, lựa chọn công nghệ phù hợp. Giúp doanh nghiệp tối ưu hóa quy trình và nâng cao hiệu quả hoạt động.',
                'icon' => 'fas fa-project-diagram',
                'icon_bg_class' => 'bg-info',
                'order' => 3
            ],
            [
                'title' => 'Digital Marketing',
                'description' => 'Dịch vụ marketing số toàn diện: SEO, Google Ads, Facebook Ads, Content Marketing. Giúp doanh nghiệp tiếp cận đúng khách hàng mục tiêu.',
                'icon' => 'fas fa-bullhorn',
                'icon_bg_class' => 'bg-warning',
                'order' => 4
            ],
            [
                'title' => 'Quản trị Hệ thống',
                'description' => 'Dịch vụ hosting, domain, SSL, backup dữ liệu. Quản trị server, tối ưu hóa hiệu năng, bảo mật hệ thống 24/7.',
                'icon' => 'fas fa-server',
                'icon_bg_class' => 'bg-danger',
                'order' => 5
            ],
            [
                'title' => 'Phần mềm Quản lý',
                'description' => 'Xây dựng phần mềm quản lý theo yêu cầu: ERP, CRM, HRM. Tích hợp đầy đủ tính năng, giao diện trực quan, phù hợp với quy trình doanh nghiệp.',
                'icon' => 'fas fa-cogs',
                'icon_bg_class' => 'bg-secondary',
                'order' => 6
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
