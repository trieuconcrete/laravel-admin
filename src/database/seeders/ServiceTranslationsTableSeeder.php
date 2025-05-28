<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceTranslationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Tiếng Anh
            'en' => [
                'Thiết kế Website' => [
                    'title' => 'Website Design',
                    'description' => 'Professional, responsive, SEO-optimized website design with modern technology. Suitable for all types of businesses from Landing Pages, Introduction Websites to E-commerce.'
                ],
                'Phát triển Ứng dụng Mobile' => [
                    'title' => 'Mobile App Development',
                    'description' => 'Native and cross-platform mobile application development for iOS and Android. Integration of modern features, user-friendly interface, high performance.'
                ],
                'Tư vấn Giải pháp CNTT' => [
                    'title' => 'IT Solution Consulting',
                    'description' => 'Digital transformation strategy consulting, system architecture, suitable technology selection. Help businesses optimize processes and improve operational efficiency.'
                ],
                'Digital Marketing' => [
                    'title' => 'Digital Marketing',
                    'description' => 'Comprehensive digital marketing services: SEO, Google Ads, Facebook Ads, Content Marketing. Help businesses reach the right target customers.'
                ],
                'Quản trị Hệ thống' => [
                    'title' => 'System Administration',
                    'description' => 'Hosting, domain, SSL, data backup services. Server administration, performance optimization, 24/7 system security.'
                ],
                'Phần mềm Quản lý' => [
                    'title' => 'Management Software',
                    'description' => 'Build custom management software: ERP, CRM, HRM. Full feature integration, intuitive interface, suitable for business processes.'
                ]
            ],
            // Tiếng Nhật
            'ja' => [
                'Thiết kế Website' => [
                    'title' => 'ウェブサイトデザイン',
                    'description' => 'モダンな技術を使用した、プロフェッショナルでレスポンシブなSEO最適化されたウェブサイトデザイン。ランディングページ、紹介サイト、Eコマースまで、あらゆるタイプのビジネスに適しています。'
                ],
                'Phát triển Ứng dụng Mobile' => [
                    'title' => 'モバイルアプリ開発',
                    'description' => 'iOSとAndroid向けのネイティブおよびクロスプラットフォームモバイルアプリケーション開発。最新機能の統合、ユーザーフレンドリーなインターフェース、高性能。'
                ],
                'Tư vấn Giải pháp CNTT' => [
                    'title' => 'ITソリューションコンサルティング',
                    'description' => 'デジタルトランスフォーメーション戦略コンサルティング、システムアーキテクチャ、適切な技術選択。企業のプロセス最適化と運用効率の向上を支援。'
                ],
                'Digital Marketing' => [
                    'title' => 'デジタルマーケティング',
                    'description' => '包括的なデジタルマーケティングサービス：SEO、Google広告、Facebook広告、コンテンツマーケティング。企業が適切なターゲット顧客にリーチするのを支援。'
                ],
                'Quản trị Hệ thống' => [
                    'title' => 'システム管理',
                    'description' => 'ホスティング、ドメイン、SSL、データバックアップサービス。サーバー管理、パフォーマンス最適化、24時間365日のシステムセキュリティ。'
                ],
                'Phần mềm Quản lý' => [
                    'title' => '管理ソフトウェア',
                    'description' => 'カスタム管理ソフトウェアの構築：ERP、CRM、HRM。完全な機能統合、直感的なインターフェース、ビジネスプロセスに適合。'
                ]
            ]
        ];

        // Lấy tất cả services
        $services = Service::all();

        foreach ($services as $service) {
            foreach ($translations as $locale => $items) {
                if (isset($items[$service->title])) {
                    ServiceTranslation::create([
                        'service_id' => $service->id,
                        'locale' => $locale,
                        'title' => $items[$service->title]['title'],
                        'description' => $items[$service->title]['description']
                    ]);
                }
            }
        }
    }
}
