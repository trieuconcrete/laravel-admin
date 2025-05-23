<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuotesTableSeeder extends Seeder
{
    private int $quoteCounter = 1;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ để tránh duplicate
        $this->command->info('Cleaning old data...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Quote::truncate();
        QuoteItem::truncate();
        DB::table('quote_histories')->truncate();
        DB::table('quote_attachments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tạo admin user nếu chưa có
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'full_name' => 'Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Tạo sales user
        $sales = User::firstOrCreate(
            ['email' => 'sales@test.com'],
            [
                'full_name' => 'Sales Manager',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Tạo thêm vài user khác
        $manager = User::firstOrCreate(
            ['email' => 'manager@test.com'],
            [
                'full_name' => 'Transport Manager',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $users = [$admin, $sales, $manager];

        $this->command->info('Creating quotes...');

        // Reset counter để tạo quote number unique
        $this->quoteCounter = 1;

        // Tạo báo giá với thanh progress bar
        $this->command->getOutput()->progressStart(58); // 50 + 5 + 3 = 58 total quotes

        // Tạo 50 báo giá thông thường
        for ($i = 1; $i <= 50; $i++) {
            $this->createQuote($users, 'normal', $i);
            $this->command->getOutput()->progressAdvance();
        }

        // Tạo 5 báo giá sắp hết hạn
        for ($i = 1; $i <= 5; $i++) {
            $this->createQuote($users, 'expiring_soon', $i + 50);
            $this->command->getOutput()->progressAdvance();
        }

        // Tạo 3 báo giá đã hết hạn
        for ($i = 1; $i <= 3; $i++) {
            $this->createQuote($users, 'expired', $i + 55);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Quotes created successfully!');
        
        // Hiển thị thống kê
        $this->displayStatistics();
    }

    private function createQuote(array $users, string $type = 'normal', int $sequence = null): void
    {
        $customer = $this->getRandomCustomer();
        $vehicleType = $this->getRandomVehicleType();
        $cargoType = $this->getRandomCargoType();
        
        // Xác định trạng thái dựa trên type
        $status = $this->getStatusByType($type);
        
        // Tính toán giá cả
        $pricing = $this->calculatePricing($vehicleType);
        
        // Xác định thời gian dựa trên type
        $dates = $this->getDatesByType($type);

        // Tạo quote number unique
        $quoteNumber = $this->generateUniqueQuoteNumber($dates['created_at'], $sequence);

        $quote = Quote::create([
            'quote_number' => $quoteNumber,
            'customer_name' => $customer['name'],
            'customer_phone' => $customer['phone'],
            'customer_email' => $customer['email'],
            'customer_address' => $customer['address'],
            'pickup_address' => $this->getRandomAddress('pickup'),
            'delivery_address' => $this->getRandomAddress('delivery'),
            'distance' => round(rand(10, 500) + (rand(0, 99) / 100), 2),
            'cargo_weight' => $this->getCargoWeight($vehicleType),
            'cargo_volume' => $this->getCargoVolume($vehicleType),
            'cargo_type' => $cargoType,
            'cargo_description' => $this->getCargoDescription($cargoType),
            'vehicle_type' => $vehicleType,
            'vehicle_quantity' => $this->getVehicleQuantity($vehicleType),
            'pickup_datetime' => $dates['pickup'],
            'delivery_datetime' => $dates['delivery'],
            'is_round_trip' => $this->shouldBeRoundTrip($vehicleType),
            'base_price' => $pricing['base_price'],
            'fuel_surcharge' => $pricing['fuel_surcharge'],
            'loading_fee' => $pricing['loading_fee'],
            'insurance_fee' => $pricing['insurance_fee'],
            'additional_fee' => $pricing['additional_fee'],
            'additional_fee_description' => $pricing['additional_fee'] > 0 ? $this->getAdditionalFeeDescription() : null,
            'discount' => $pricing['discount'],
            'total_price' => $pricing['total_price'],
            'vat_rate' => 10.00,
            'vat_amount' => $pricing['vat_amount'],
            'final_price' => $pricing['final_price'],
            'status' => $status,
            'valid_until' => $dates['valid_until'],
            'notes' => $this->getRandomNotes(),
            'terms_conditions' => $this->getTermsConditions(),
            'created_by' => $users[array_rand($users)]->id,
            'assigned_to' => $users[array_rand($users)]->id,
            'created_at' => $dates['created_at'],
            'updated_at' => $dates['created_at'],
        ]);

        // Tạo quote items
        $this->createQuoteItems($quote, $vehicleType);
    }

    private function getRandomCustomer(): array
    {
        $customers = [
            [
                'name' => 'Công ty TNHH Thương mại ABC',
                'phone' => '0901234567',
                'email' => 'contact@abc-trading.com',
                'address' => '123 Đường Nguyễn Văn Linh, Phường Tân Phú, Quận 7, TP.HCM',
            ],
            [
                'name' => 'Công ty Cổ phần Xuất nhập khẩu XYZ',
                'phone' => '0912345678',
                'email' => 'info@xyz-export.com',
                'address' => '456 Đường Lê Văn Việt, Phường Tăng Nhơn Phú A, TP. Thủ Đức, TP.HCM',
            ],
            [
                'name' => 'Doanh nghiệp Logistics 123',
                'phone' => '0923456789',
                'email' => 'logistics@123corp.vn',
                'address' => '789 Đường Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP.HCM',
            ],
            [
                'name' => 'Công ty TNHH Sản xuất và Thương mại Minh Phát',
                'phone' => '0934567890',
                'email' => 'minhphat@company.vn',
                'address' => '321 Đường Võ Văn Kiệt, Phường 1, Quận 5, TP.HCM',
            ],
            [
                'name' => 'Công ty Cổ phần Công nghiệp Thành Đạt',
                'phone' => '0945678901',
                'email' => 'thanhdat.industry@gmail.com',
                'address' => '654 Đường Cộng Hòa, Phường 13, Quận Tân Bình, TP.HCM',
            ],
            [
                'name' => 'Công ty TNHH MTV Vận tải Hồng Phúc',
                'phone' => '0956789012',
                'email' => 'hongphuc.transport@yahoo.com',
                'address' => '987 Đường Lạc Long Quân, Phường 10, Quận 11, TP.HCM',
            ],
            [
                'name' => 'Công ty Cổ phần Thực phẩm Tươi Ngon',
                'phone' => '0967890123',
                'email' => 'tuoingon.food@outlook.com',
                'address' => '159 Đường Phan Văn Trị, Phường 11, Quận Bình Thạnh, TP.HCM',
            ],
            [
                'name' => 'Công ty TNHH Điện tử Công nghệ cao',
                'phone' => '0978901234',
                'email' => 'hitech.electronics@company.com',
                'address' => '753 Đường Nguyễn Thị Minh Khai, Phường Đa Kao, Quận 1, TP.HCM',
            ],
        ];

        return $customers[array_rand($customers)];
    }

    private function getRandomVehicleType(): string
    {
        // Phân bố trọng số cho các loại xe
        $weights = [
            'truck' => 40,    // 40% xe tải
            'container' => 30, // 30% container
            'van' => 20,      // 20% xe tải nhỏ
            'motorcycle' => 10 // 10% xe máy
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $type => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $type;
            }
        }

        return 'truck'; // fallback
    }

    private function getRandomCargoType(): string
    {
        $cargoTypes = [
            'Thực phẩm tươi sống',
            'Thực phẩm đông lạnh',
            'Thiết bị điện tử',
            'Máy móc công nghiệp',
            'Quần áo may mặc',
            'Hóa chất công nghiệp',
            'Vật liệu xây dựng',
            'Nông sản xuất khẩu',
            'Đồ gia dụng',
            'Linh kiện ô tô',
            'Dược phẩm',
            'Hàng tiêu dùng',
        ];

        return $cargoTypes[array_rand($cargoTypes)];
    }

    private function getStatusByType(string $type): string
    {
        switch ($type) {
            case 'expiring_soon':
                return collect(['sent', 'approved'])->random();
            case 'expired':
                return 'expired';
            default:
                return collect(['draft', 'sent', 'approved', 'rejected', 'converted'])->random();
        }
    }

    private function calculatePricing(string $vehicleType): array
    {
        // Giá cơ bản theo loại xe
        $basePriceRanges = [
            'motorcycle' => [100000, 500000],
            'van' => [1000000, 5000000],
            'truck' => [5000000, 25000000],
            'container' => [15000000, 50000000],
        ];

        $range = $basePriceRanges[$vehicleType];
        $basePrice = rand($range[0], $range[1]);

        // Các phụ phí
        $fuelSurcharge = $basePrice * (rand(8, 15) / 100); // 8-15%
        $loadingFee = $vehicleType === 'motorcycle' ? rand(0, 100000) : rand(500000, 2000000);
        $insuranceFee = $basePrice * (rand(1, 3) / 100); // 1-3%
        $additionalFee = rand(0, 1) ? rand(200000, 1500000) : 0; // 50% chance
        $discount = rand(0, $basePrice * 0.08); // 0-8% discount

        $totalPrice = $basePrice + $fuelSurcharge + $loadingFee + $insuranceFee + $additionalFee - $discount;
        $vatAmount = $totalPrice * 0.1; // 10% VAT
        $finalPrice = $totalPrice + $vatAmount;

        return [
            'base_price' => round($basePrice, 2),
            'fuel_surcharge' => round($fuelSurcharge, 2),
            'loading_fee' => round($loadingFee, 2),
            'insurance_fee' => round($insuranceFee, 2),
            'additional_fee' => round($additionalFee, 2),
            'discount' => round($discount, 2),
            'total_price' => round($totalPrice, 2),
            'vat_amount' => round($vatAmount, 2),
            'final_price' => round($finalPrice, 2),
        ];
    }

    private function getDatesByType(string $type): array
    {
        $createdAt = Carbon::now()->subDays(rand(1, 90));

        switch ($type) {
            case 'expiring_soon':
                $validUntil = Carbon::now()->addDays(rand(1, 7));
                $pickupDate = Carbon::now()->addDays(rand(5, 15));
                break;
            case 'expired':
                $validUntil = Carbon::now()->subDays(rand(1, 30));
                $pickupDate = Carbon::now()->subDays(rand(1, 10));
                break;
            default:
                $validUntil = Carbon::now()->addDays(rand(7, 45));
                $pickupDate = Carbon::now()->addDays(rand(1, 30));
                break;
        }

        $deliveryDate = $pickupDate->copy()->addHours(rand(4, 72));

        return [
            'created_at' => $createdAt,
            'pickup' => $pickupDate,
            'delivery' => $deliveryDate,
            'valid_until' => $validUntil,
        ];
    }

    private function getCargoWeight(string $vehicleType): float
    {
        $weightRanges = [
            'motorcycle' => [0.01, 0.5],
            'van' => [0.5, 3.0],
            'truck' => [3.0, 20.0],
            'container' => [10.0, 25.0],
        ];

        $range = $weightRanges[$vehicleType];
        return round(rand($range[0] * 100, $range[1] * 100) / 100, 2);
    }

    private function getCargoVolume(string $vehicleType): float
    {
        $volumeRanges = [
            'motorcycle' => [0.1, 2.0],
            'van' => [2.0, 15.0],
            'truck' => [15.0, 80.0],
            'container' => [33.0, 76.0], // 20ft: 33m3, 40ft: 76m3
        ];

        $range = $volumeRanges[$vehicleType];
        return round(rand($range[0] * 100, $range[1] * 100) / 100, 2);
    }

    private function getVehicleQuantity(string $vehicleType): int
    {
        $quantityRanges = [
            'motorcycle' => [1, 5],
            'van' => [1, 3],
            'truck' => [1, 4],
            'container' => [1, 2],
        ];

        $range = $quantityRanges[$vehicleType];
        return rand($range[0], $range[1]);
    }

    private function shouldBeRoundTrip(string $vehicleType): bool
    {
        $probabilities = [
            'motorcycle' => 10, // 10% round trip
            'van' => 20,        // 20% round trip
            'truck' => 30,      // 30% round trip
            'container' => 15,  // 15% round trip
        ];

        return rand(1, 100) <= $probabilities[$vehicleType];
    }

    private function getRandomAddress(string $type): string
    {
        $pickupAddresses = [
            'Khu Công nghiệp Tân Bình, Đường Hoàng Hoa Thám, Quận Tân Bình, TP.HCM',
            'Cảng Sài Gòn, Đường Nguyễn Tất Thành, Quận 4, TP.HCM',
            'Kho bãi ICD Tân Cảng, Đường Tôn Thất Thuyết, Quận 4, TP.HCM',
            'Khu Chế xuất Tân Thuận, Đường Huỳnh Tấn Phát, Quận 7, TP.HCM',
            'Khu Công nghiệp Biên Hòa 1, Đường Võ Thị Sáu, TP. Biên Hòa, Đồng Nai',
            'Cảng Cát Lái, Đường Đỗ Xuân Hợp, TP. Thủ Đức, TP.HCM',
            'Khu Công nghiệp Sóng Thần 1, Đường DT743, Dĩ An, Bình Dương',
            'Chợ đầu mối Bình Điền, Đường Nguyễn Văn Linh, Quận 8, TP.HCM',
            'Kho Nội Bài, Đường Võ Nguyên Giáp, Sóc Sơn, Hà Nội',
            'Cảng Hải Phòng, Đường Hoàng Quốc Việt, Hải Phòng',
        ];

        $deliveryAddresses = [
            'Siêu thị Co.opmart, Đường Cộng Hòa, Quận Tân Bình, TP.HCM',
            'Trung tâm thương mại Vincom, Đường Lê Thánh Tôn, Quận 1, TP.HCM',
            'Nhà máy may Việt Tiến, Đường Quang Trung, Gò Vấp, TP.HCM',
            'Kho hàng Lazada, Đường Tô Ký, Quận 12, TP.HCM',
            'Cửa hàng điện máy Nguyễn Kim, Đường Phan Văn Trị, Bình Thạnh, TP.HCM',
            'Nhà hàng Golden Dragon, Đường Nguyễn Huệ, Quận 1, TP.HCM',
            'Văn phòng công ty Samsung, Đường Nam Kỳ Khởi Nghĩa, Quận 3, TP.HCM',
            'Bệnh viện Chợ Rẫy, Đường Nguyễn Chí Thanh, Quận 5, TP.HCM',
            'Trường Đại học Bách Khoa, Đường Lý Thường Kiệt, Quận 10, TP.HCM',
            'Khu dân cư Phú Mỹ Hưng, Đường Nguyễn Lương Bằng, Quận 7, TP.HCM',
        ];

        return $type === 'pickup' ? 
            $pickupAddresses[array_rand($pickupAddresses)] : 
            $deliveryAddresses[array_rand($deliveryAddresses)];
    }

    private function getCargoDescription(string $cargoType): string
    {
        $descriptions = [
            'Thực phẩm tươi sống' => 'Rau củ quả tươi, yêu cầu bảo quản mát 2-8°C, giao hàng trong ngày',
            'Thực phẩm đông lạnh' => 'Thịt cá đông lạnh, yêu cầu bảo quản lạnh -18°C, xe chuyên dụng',
            'Thiết bị điện tử' => 'Laptop, điện thoại, thiết bị IT, yêu cầu đóng gói cẩn thận, chống sốc',
            'Máy móc công nghiệp' => 'Máy công nghiệp, thiết bị sản xuất, cần cẩu nâng hạ chuyên dụng',
            'Quần áo may mặc' => 'Hàng may mặc xuất khẩu, đóng thùng carton, giữ khô ráo',
            'Hóa chất công nghiệp' => 'Hóa chất công nghiệp, cần giấy phép vận chuyển hàng nguy hiểm ADR',
            'Vật liệu xây dựng' => 'Xi măng, sắt thép, gạch ốp lát, vật liệu xây dựng nặng',
            'Nông sản xuất khẩu' => 'Gạo, cà phê, hồ tiêu, yêu cầu thông thoáng, tránh ẩm mốc',
            'Đồ gia dụng' => 'Nội thất, đồ gia dụng, yêu cầu bọc nilon chống trầy xước',
            'Linh kiện ô tô' => 'Phụ tùng xe hơi, linh kiện thay thế, đóng pallet chắc chắn',
            'Dược phẩm' => 'Thuốc men, dược phẩm, yêu cầu giấy phép GDP, bảo quản nhiệt độ',
            'Hàng tiêu dùng' => 'Hàng tiêu dùng nhanh, sản phẩm gia đình, đóng gói theo tiêu chuẩn',
        ];

        return $descriptions[$cargoType] ?? 'Hàng hóa thông thường, không có yêu cầu đặc biệt';
    }

    private function getAdditionalFeeDescription(): string
    {
        $descriptions = [
            'Phí chờ đợi tại điểm lấy/giao hàng',
            'Phí vận chuyển ngoài giờ hành chính',
            'Phí đi đường xa, vùng khó khăn',
            'Phí bốc xếp thêm do hàng nặng',
            'Phí bảo vệ hàng hóa 24/7',
            'Phí giao hàng tận nơi theo yêu cầu',
            'Phí xử lý hàng hóa đặc biệt',
            'Phí làm thêm giờ, ngày lễ',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            'Khách hàng yêu cầu giao hàng trong giờ hành chính (8h-17h)',
            'Cần liên hệ người nhận trước khi giao hàng 30 phút',
            'Địa chỉ giao hàng nằm trong hẻm nhỏ, xe lớn khó vào',
            'Khách hàng VIP, ưu tiên chất lượng dịch vụ và thái độ',
            'Hàng dễ vỡ, cần xử lý cẩn thận và chuyên nghiệp',
            'Yêu cầu có hóa đơn VAT đầy đủ',
            'Thanh toán chuyển khoản trong vòng 7 ngày',
            'Cần xác nhận lại thời gian trước khi vận chuyển',
            'Hàng có giá trị cao, cần mua bảo hiểm bổ sung',
            'Khách hàng thường xuyên, có thể áp dụng giá ưu đãi',
            null, null, null, // 30% không có ghi chú
        ];

        return $notes[array_rand($notes)];
    }

    private function getTermsConditions(): string
    {
        return "ĐIỀU KHOẢN VÀ ĐIỀU KIỆN:\n\n" .
               "1. Giá trên chưa bao gồm thuế VAT 10%\n" .
               "2. Thời gian hiệu lực báo giá: 15 ngày kể từ ngày lập\n" .
               "3. Phương thức thanh toán: Chuyển khoản trong vòng 7 ngày sau khi giao hàng\n" .
               "4. Công ty không chịu trách nhiệm với hàng hóa không được khai báo đầy đủ\n" .
               "5. Mọi thay đổi về lịch trình cần thông báo trước ít nhất 24 giờ\n" .
               "6. Khách hàng có trách nhiệm kiểm tra hàng hóa khi nhận\n" .
               "7. Công ty có quyền từ chối vận chuyển hàng cấm theo quy định pháp luật\n" .
               "8. Bảo hiểm hàng hóa theo yêu cầu khách hàng (phí bảo hiểm tính riêng)\n" .
               "9. Mọi tranh chấp sẽ được giải quyết tại Tòa án TP.HCM\n" .
               "10. Báo giá này chỉ có giá trị khi được ký xác nhận bởi cả hai bên";
    }

    private function createQuoteItems(Quote $quote, string $vehicleType): void
    {
        $services = $this->getServicesForVehicleType($vehicleType);
        
        foreach ($services as $sers) {
            foreach ($sers as $service) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'service_name' => $service['name'],
                    'description' => $service['description'],
                    'quantity' => $service['quantity'],
                    'unit' => $service['unit'],
                    'unit_price' => $service['unit_price'],
                    'total_price' => $service['quantity'] * $service['unit_price'],
                ]);
            }
        }
    }

    private function getServicesForVehicleType(string $vehicleType): array
    {
        return $services = [
            'motorcycle' => [
                [
                    'name' => 'Giao hàng nhanh trong thành phố',
                    'description' => 'Dịch vụ giao hàng bằng xe máy trong ngày',
                    'quantity' => 1,
                    'unit' => 'đơn hàng',
                    'unit_price' => rand(30000, 150000),
                ],
                [
                    'name' => 'Phí thu hộ COD',
                    'description' => 'Phí dịch vụ thu tiền hộ khách hàng',
                    'quantity' => 1,
                    'unit' => 'lần',
                    'unit_price' => rand(10000, 30000),
                ],
            ],
            'van' => [
                [
                    'name' => 'Vận chuyển xe tải nhỏ',
                    'description' => 'Dịch vụ vận chuyển nội thành bằng xe tải nhỏ',
                    'quantity' => 1,
                    'unit' => 'chuyến',
                    'unit_price' => rand(800000, 3000000),
                ],
                [
                    'name' => 'Bốc xếp hàng hóa',
                    'description' => 'Dịch vụ bốc xếp tại điểm đi và điểm đến',
                    'quantity' => 2,
                    'unit' => 'điểm',
                    'unit_price' => rand(200000, 500000),
                ],
                [
                    'name' => 'Phí đậu xe chờ',
                    'description' => 'Phí đậu xe trong quá trình chờ bốc xếp',
                    'quantity' => 1,
                    'unit' => 'lần',
                    'unit_price' => rand(50000, 200000),
                ],
            ],
            'truck' => [
                [
                    'name' => 'Vận chuyển xe tải',
                    'description' => 'Dịch vụ vận chuyển hàng hóa bằng xe tải',
                    'quantity' => 1,
                    'unit' => 'chuyến',
                    'unit_price' => rand(3000000, 15000000),
                ],
                [
                    'name' => 'Bốc xếp hàng hóa',
                    'description' => 'Dịch vụ bốc xếp tại kho và điểm giao hàng',
                    'quantity' => 2,
                    'unit' => 'điểm',
                    'unit_price' => rand(500000, 1500000),
                ],
                [
                    'name' => 'Phí cân hàng',
                    'description' => 'Phí cân đo trọng lượng hàng hóa',
                    'quantity' => 1,
                    'unit' => 'lần',
                    'unit_price' => rand(100000, 300000),
                ],
                [
                    'name' => 'Phí bảo vệ hàng hóa',
                    'description' => 'Dịch vụ bảo vệ hàng hóa trong quá trình vận chuyển',
                    'quantity' => 1,
                    'unit' => 'chuyến',
                    'unit_price' => rand(300000, 800000),
                ],
            ],
            'container' => [
                [
                    'name' => 'Vận chuyển container',
                    'description' => 'Dịch vụ vận chuyển container 20ft/40ft',
                    'quantity' => 1,
                    'unit' => 'container',
                    'unit_price' => rand(8000000, 25000000),
                ],
                [
                    'name' => 'Phí cảng biển',
                    'description' => 'Các loại phí tại cảng: cập bến, bốc xếp, lưu bãi',
                    'quantity' => 1,
                    'unit' => 'container',
                    'unit_price' => rand(2000000, 5000000),
                ],
                [
                    'name' => 'Phí hải quan',
                    'description' => 'Phí làm thủ tục hải quan xuất nhập khẩu',
                    'quantity' => 1,
                    'unit' => 'lô hàng',
                    'unit_price' => rand(1000000, 3000000),
                ],
                [
                    'name' => 'Phí niêm phong container',
                    'description' => 'Phí niêm phong và kiểm tra container',
                    'quantity' => 1,
                    'unit' => 'container',
                    'unit_price' => rand(200000, 500000),
                ],
                [
                    'name' => 'Phí vận chuyển nội địa',
                    'description' => 'Phí vận chuyển từ cảng đến kho nội địa',
                    'quantity' => 1,
                    'unit' => 'chuyến',
                    'unit_price' => rand(1500000, 4000000),
                ],
            ],
        ];
    }

    /**
     * Tạo quote number unique để tránh duplicate
     */
    private function generateUniqueQuoteNumber(Carbon $createdAt, ?int $sequence = null): string
    {
        $prefix = 'BG';
        $date = $createdAt->format('Ymd');
        
        if ($sequence) {
            // Sử dụng sequence number được truyền vào
            $number = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        } else {
            // Sử dụng counter nội bộ
            $number = str_pad($this->quoteCounter++, 4, '0', STR_PAD_LEFT);
        }
        
        $quoteNumber = $prefix . $date . $number;
        
        // Kiểm tra xem quote number đã tồn tại chưa
        $attempts = 0;
        while (Quote::where('quote_number', $quoteNumber)->exists() && $attempts < 100) {
            $attempts++;
            $this->quoteCounter++;
            $number = str_pad($this->quoteCounter, 4, '0', STR_PAD_LEFT);
            $quoteNumber = $prefix . $date . $number;
        }
        
        if ($attempts >= 100) {
            // Fallback: sử dụng timestamp để đảm bảo unique
            $quoteNumber = $prefix . $date . substr(time(), -4);
        }
        
        return $quoteNumber;
    }

    /**
     * Hiển thị thống kê sau khi tạo xong
     */
    private function displayStatistics(): void
    {
        $total = Quote::count();
        $byStatus = Quote::selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray();
        
        $byVehicle = Quote::selectRaw('vehicle_type, COUNT(*) as count')
                         ->groupBy('vehicle_type')
                         ->pluck('count', 'vehicle_type')
                         ->toArray();

        $this->command->info("\n📊 THỐNG KÊ DỮ LIỆU ĐÃ TẠO:");
        $this->command->info("═══════════════════════════════");
        $this->command->info("📋 Tổng số báo giá: {$total}");
        
        $this->command->info("\n📈 Phân bố theo trạng thái:");
        foreach ($byStatus as $status => $count) {
            $percentage = round(($count / $total) * 100, 1);
            $this->command->info("   • {$status}: {$count} ({$percentage}%)");
        }
        
        $this->command->info("\n🚛 Phân bố theo loại xe:");
        foreach ($byVehicle as $vehicle => $count) {
            $percentage = round(($count / $total) * 100, 1);
            $this->command->info("   • {$vehicle}: {$count} ({$percentage}%)");
        }
        
        $totalValue = Quote::where('status', 'approved')->sum('final_price');
        $this->command->info("\n💰 Tổng giá trị báo giá đã duyệt: " . number_format($totalValue) . " VND");
        
        $expiringSoon = Quote::expiringSoon()->count();
        $this->command->info("⚠️  Báo giá sắp hết hạn: {$expiringSoon}");
        
        $this->command->info("═══════════════════════════════");
        $this->command->info("✅ Hoàn thành tạo dữ liệu mẫu!\n");
    }

    /**
     * Tạo dữ liệu mẫu cho testing
     */
    private function createSampleQuotesForTesting(): void
    {
        $admin = User::where('email', 'admin@transport.com')->first();
        $sales = User::where('email', 'sales@transport.com')->first();

        // Tạo báo giá mẫu cho từng trạng thái
        $sampleQuotes = [
            [
                'customer_name' => 'Công ty TNHH Test Draft',
                'status' => 'draft',
                'vehicle_type' => 'truck',
                'final_price' => 12500000,
                'notes' => 'Báo giá mẫu ở trạng thái bản nháp',
            ],
            [
                'customer_name' => 'Công ty TNHH Test Sent',
                'status' => 'sent',
                'vehicle_type' => 'container',
                'final_price' => 28000000,
                'notes' => 'Báo giá mẫu đã gửi khách hàng',
            ],
            [
                'customer_name' => 'Công ty TNHH Test Approved',
                'status' => 'approved',
                'vehicle_type' => 'van',
                'final_price' => 5500000,
                'notes' => 'Báo giá mẫu đã được khách hàng phê duyệt',
            ],
            [
                'customer_name' => 'Công ty TNHH Test Rejected',
                'status' => 'rejected',
                'vehicle_type' => 'truck',
                'final_price' => 15000000,
                'notes' => 'Báo giá mẫu bị khách hàng từ chối',
            ],
            [
                'customer_name' => 'Công ty TNHH Test Expired',
                'status' => 'expired',
                'vehicle_type' => 'motorcycle',
                'final_price' => 350000,
                'notes' => 'Báo giá mẫu đã hết hạn',
                'valid_until' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($sampleQuotes as $index => $sampleData) {
            $customer = $this->getRandomCustomer();
            $pricing = $this->calculatePricing($sampleData['vehicle_type']);
            
            // Override pricing với giá mẫu
            $pricing['final_price'] = $sampleData['final_price'];
            $pricing['total_price'] = $sampleData['final_price'] / 1.1; // Trừ VAT
            $pricing['vat_amount'] = $pricing['total_price'] * 0.1;

            $quote = Quote::create([
                'customer_name' => $sampleData['customer_name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => "test{$index}@example.com",
                'customer_address' => $customer['address'],
                'pickup_address' => $this->getRandomAddress('pickup'),
                'delivery_address' => $this->getRandomAddress('delivery'),
                'distance' => round(rand(50, 200) + (rand(0, 99) / 100), 2),
                'cargo_weight' => $this->getCargoWeight($sampleData['vehicle_type']),
                'cargo_volume' => $this->getCargoVolume($sampleData['vehicle_type']),
                'cargo_type' => $this->getRandomCargoType(),
                'cargo_description' => $this->getCargoDescription($this->getRandomCargoType()),
                'vehicle_type' => $sampleData['vehicle_type'],
                'vehicle_quantity' => $this->getVehicleQuantity($sampleData['vehicle_type']),
                'pickup_datetime' => Carbon::now()->addDays(rand(5, 20)),
                'delivery_datetime' => Carbon::now()->addDays(rand(6, 22)),
                'is_round_trip' => $this->shouldBeRoundTrip($sampleData['vehicle_type']),
                'base_price' => $pricing['base_price'],
                'fuel_surcharge' => $pricing['fuel_surcharge'],
                'loading_fee' => $pricing['loading_fee'],
                'insurance_fee' => $pricing['insurance_fee'],
                'additional_fee' => $pricing['additional_fee'],
                'discount' => $pricing['discount'],
                'total_price' => $pricing['total_price'],
                'vat_rate' => 10.00,
                'vat_amount' => $pricing['vat_amount'],
                'final_price' => $pricing['final_price'],
                'status' => $sampleData['status'],
                'valid_until' => $sampleData['valid_until'] ?? Carbon::now()->addDays(15),
                'notes' => $sampleData['notes'],
                'terms_conditions' => $this->getTermsConditions(),
                'created_by' => $admin->id,
                'assigned_to' => $sales->id,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            $this->createQuoteItems($quote, $sampleData['vehicle_type']);
        }
    }

    /**
     * Tạo báo giá với lịch sử phong phú
     */
    private function createQuotesWithRichHistory(): void
    {
        $admin = User::where('email', 'admin@transport.com')->first();
        $sales = User::where('email', 'sales@transport.com')->first();
        $customer = $this->getRandomCustomer();

        // Tạo báo giá có nhiều thay đổi
        $quote = Quote::create([
            'customer_name' => 'Công ty TNHH Lịch sử phong phú',
            'customer_phone' => '0987654321',
            'customer_email' => 'history@example.com',
            'customer_address' => $customer['address'],
            'pickup_address' => $this->getRandomAddress('pickup'),
            'delivery_address' => $this->getRandomAddress('delivery'),
            'distance' => 125.50,
            'cargo_weight' => 8.75,
            'cargo_volume' => 45.25,
            'cargo_type' => 'Thiết bị điện tử',
            'cargo_description' => 'Máy tính, thiết bị văn phòng, yêu cầu bảo quản cẩn thận',
            'vehicle_type' => 'truck',
            'vehicle_quantity' => 2,
            'pickup_datetime' => Carbon::now()->addDays(10),
            'delivery_datetime' => Carbon::now()->addDays(12),
            'is_round_trip' => false,
            'base_price' => 10000000,
            'fuel_surcharge' => 1200000,
            'loading_fee' => 800000,
            'insurance_fee' => 200000,
            'additional_fee' => 500000,
            'additional_fee_description' => 'Phí giao hàng cuối tuần',
            'discount' => 300000,
            'total_price' => 12400000,
            'vat_rate' => 10.00,
            'vat_amount' => 1240000,
            'final_price' => 13640000,
            'status' => 'approved',
            'valid_until' => Carbon::now()->addDays(20),
            'notes' => 'Báo giá có lịch sử thay đổi phong phú để test',
            'terms_conditions' => $this->getTermsConditions(),
            'created_by' => $sales->id,
            'assigned_to' => $admin->id,
            'created_at' => Carbon::now()->subDays(15),
        ]);

        // Tạo quote items
        $this->createQuoteItems($quote, 'truck');

        // Mô phỏng lịch sử thay đổi
        $this->simulateQuoteHistory($quote, $admin, $sales);
    }

    /**
     * Mô phỏng lịch sử thay đổi báo giá
     */
    private function simulateQuoteHistory(Quote $quote, User $admin, User $sales): void
    {
        $histories = [
            [
                'action' => 'created',
                'description' => 'Báo giá được tạo bởi nhân viên kinh doanh',
                'performed_by' => $sales->name,
                'created_at' => $quote->created_at,
            ],
            [
                'action' => 'price_updated',
                'old_status' => 'draft',
                'new_status' => 'draft',
                'description' => 'Cập nhật giá cơ bản từ 9,000,000 VND lên 10,000,000 VND',
                'performed_by' => $sales->name,
                'created_at' => $quote->created_at->addHours(2),
            ],
            [
                'action' => 'updated',
                'description' => 'Cập nhật thông tin khách hàng và địa chỉ giao hàng',
                'performed_by' => $sales->name,
                'created_at' => $quote->created_at->addHours(4),
            ],
            [
                'action' => 'sent',
                'old_status' => 'draft',
                'new_status' => 'sent',
                'description' => 'Gửi báo giá qua email cho khách hàng',
                'performed_by' => $sales->name,
                'created_at' => $quote->created_at->addDays(1),
            ],
            [
                'action' => 'status_changed',
                'old_status' => 'sent',
                'new_status' => 'approved',
                'description' => 'Khách hàng xác nhận chấp thuận báo giá',
                'performed_by' => $admin->name,
                'created_at' => $quote->created_at->addDays(3),
            ],
        ];

        foreach ($histories as $historyData) {
            DB::table('quote_histories')->insert([
                'quote_id' => $quote->id,
                'action' => $historyData['action'],
                'old_status' => $historyData['old_status'] ?? null,
                'new_status' => $historyData['new_status'] ?? null,
                'description' => $historyData['description'],
                'performed_by' => $historyData['performed_by'],
                'created_at' => $historyData['created_at'],
                'updated_at' => $historyData['created_at'],
            ]);
        }
    }
}