<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra xem có dữ liệu cho tài xế, phương tiện và hợp đồng không
        $driversCount = User::where('role', 'driver')->count();
        $vehiclesCount = Vehicle::count();
        $contractsCount = Contract::where('status', 'active')->count();
        $customersCount = Customer::count();
        
        if ($driversCount == 0 || $vehiclesCount == 0) {
            $this->command->info('Cần có dữ liệu cho tài xế và phương tiện trước khi tạo shipment. Vui lòng chạy seeders tương ứng.');
            return;
        }
        
        // Lấy danh sách ID
        $driverIds = User::where('role', 'driver')->pluck('id')->toArray();
        $coDriverIds = User::where('role', 'driver')->pluck('id')->toArray();
        $vehicleIds = Vehicle::pluck('vehicle_id')->toArray();
        $contractIds = Contract::pluck('id')->toArray();
        $customerIds = Customer::pluck('id')->toArray();
        
        // Tạo danh sách mô tả hàng hóa mẫu
        $cargoDescriptions = [
            'Hàng tiêu dùng đóng thùng carton',
            'Thiết bị điện tử, dễ vỡ',
            'Hàng may mặc, quần áo',
            'Vật liệu xây dựng: xi măng, cát, sỏi',
            'Linh kiện ô tô, phụ tùng xe máy',
            'Thực phẩm khô, đóng gói',
            'Nội thất, đồ gỗ',
            'Sản phẩm nhựa, bao bì',
            'Máy móc, thiết bị công nghiệp',
            'Nông sản, trái cây',
            'Thủy sản đông lạnh',
            'Sắt thép, kim loại các loại',
            'Giấy, văn phòng phẩm',
            'Gạch men, đá ốp lát',
            'Phân bón, hóa chất nông nghiệp'
        ];
        
        // Tạo danh sách điểm đi và điểm đến mẫu
        $locations = [
            'Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng',
            'Nha Trang', 'Đà Lạt', 'Huế', 'Vinh', 'Biên Hòa', 'Bình Dương',
            'Long An', 'Vũng Tàu', 'Quy Nhơn', 'Buôn Ma Thuột', 'Hạ Long'
        ];
        
        // Tạo 50 shipment mẫu
        for ($i = 0; $i < 50; $i++) {
            // Xác định nguồn dữ liệu (từ hợp đồng hoặc tạo mới)
            $useContract = !empty($contractIds) && rand(0, 1) == 1;
            
            if ($useContract) {
                $contractId = $contractIds[array_rand($contractIds)];
                $contract = Contract::find($contractId);
                $customerId = $contract->customer_id;
                
                // Lấy thông tin từ hợp đồng
                $routeInfo = json_decode($contract->route_information, true);
                $origin = $routeInfo['origin'] ?? $locations[array_rand($locations)];
                $destination = $routeInfo['destination'] ?? $locations[array_rand($locations)];
                
                $cargoInfo = json_decode($contract->cargo_details, true);
                $cargoDescription = $cargoInfo['type'] ?? $cargoDescriptions[array_rand($cargoDescriptions)];
            } else {
                $contractId = null;
                $customerId = $customerIds[array_rand($customerIds)];
                
                // Tạo thông tin ngẫu nhiên
                $origin = $locations[array_rand($locations)];
                $destination = $locations[array_rand($locations)];
                while ($destination == $origin) {
                    $destination = $locations[array_rand($locations)];
                }
                
                $cargoDescription = $cargoDescriptions[array_rand($cargoDescriptions)];
            }
            
            // Tạo thông tin thời gian
            $departureTime = Carbon::now()->subDays(rand(-30, 30))->addDays(rand(0, 60));
            $distance = rand(50, 1000); // 50-1000km
            $estimatedArrivalTime = (clone $departureTime)->addHours(ceil($distance / 50)); // Ước tính 50km/h
            
            // Xác định trạng thái dựa trên thời gian
            $status = $this->determineStatusByDate($departureTime, $estimatedArrivalTime);
            
            // Xác định giá và dịch vụ cẩu hàng
            $unitPrice = rand(100, 300) * 100; // 10,000 - 30,000 VNĐ/km
            $hasCraneService = rand(0, 10) < 3; // 30% cơ hội có dịch vụ cẩu
            $cranePrice = $hasCraneService ? rand(500, 2000) * 1000 : 0; // 500,000 - 2,000,000 VNĐ
            
            // Tạo shipment
            Shipment::create([
                'shipment_code' => Shipment::generateShipmentCode(),
                'contract_id' => $contractId,
                'origin' => $origin,
                'destination' => $destination,
                'departure_time' => $departureTime,
                'estimated_arrival_time' => $estimatedArrivalTime,
                'cargo_weight' => rand(500, 20000), // 500kg - 20 tấn
                'cargo_description' => $cargoDescription,
                'driver_id' => !empty($driverIds) ? $driverIds[array_rand($driverIds)] : null,
                'co_driver_id' => !empty($coDriverIds) && rand(0, 1) == 1 ? $coDriverIds[array_rand($coDriverIds)] : null,
                'vehicle_id' => !empty($vehicleIds) ? $vehicleIds[array_rand($vehicleIds)] : null,
                'distance' => $distance,
                'unit_price' => $unitPrice,
                'crane_price' => $cranePrice,
                'has_crane_service' => $hasCraneService,
                'notes' => $this->generateRandomNotes($status),
                'status' => $status,
                'created_by' => 1,
                'created_at' => $departureTime->copy()->subDays(rand(1, 5)),
                'updated_at' => $departureTime->copy()->subHours(rand(1, 24))
            ]);
        }
    }

    /**
     * Xác định trạng thái dựa trên ngày khởi hành và dự kiến đến
     */
    private function determineStatusByDate($departureTime, $estimatedArrivalTime)
    {
        $now = Carbon::now();
        
        if ($departureTime->gt($now)) {
            return rand(0, 1) ? 'pending' : 'confirmed'; // Chưa đến ngày khởi hành
        } elseif ($departureTime->lte($now) && $estimatedArrivalTime->gt($now)) {
            return 'in_transit'; // Đang vận chuyển
        } else {
            // Đã qua thời gian dự kiến đến
            $status = ['completed', 'delivered', 'delayed', 'cancelled'];
            $weights = [70, 15, 10, 5]; // Trọng số để hoàn thành xuất hiện nhiều hơn
            return $this->getRandomWeightedElement($status, $weights);
        }
    }

    /**
     * Chọn ngẫu nhiên một phần tử dựa trên trọng số
     */
    private function getRandomWeightedElement(array $elements, array $weights)
    {
        $totalWeight = array_sum($weights);
        $randomValue = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($elements as $key => $element) {
            $currentWeight += $weights[$key];
            if ($randomValue <= $currentWeight) {
                return $element;
            }
        }
        
        return $elements[0]; // Mặc định trả về phần tử đầu tiên nếu có lỗi
    }

    /**
     * Tạo ghi chú ngẫu nhiên theo trạng thái
     */
    private function generateRandomNotes($status)
    {
        $notes = [
            'pending' => [
                'Đang chờ xác nhận từ khách hàng.',
                'Cần kiểm tra lại thông tin trước khi khởi hành.',
                'Chờ bổ sung giấy tờ vận chuyển.',
                'Đang chuẩn bị phương tiện và nhân sự.',
                'Chờ xác nhận từ bộ phận điều vận.'
            ],
            'confirmed' => [
                'Đã xác nhận với khách hàng, sẵn sàng khởi hành.',
                'Tài xế đã được phân công, đang chuẩn bị phương tiện.',
                'Lịch trình đã được xác nhận.',
                'Đã chuẩn bị đầy đủ giấy tờ vận chuyển.',
                'Khách hàng đã xác nhận lịch lấy hàng.'
            ],
            'in_transit' => [
                'Đang trên đường vận chuyển, mọi thứ diễn ra bình thường.',
                'Tài xế báo cáo không có vấn đề gì trong quá trình vận chuyển.',
                'Đã qua 50% quãng đường, dự kiến đúng giờ.',
                'Đang vận chuyển, dự kiến sẽ đến sớm 30 phút.',
                'Tài xế báo cáo có kẹt xe nhẹ, nhưng vẫn đúng tiến độ.'
            ],
            'delivered' => [
                'Đã giao hàng, chờ xác nhận hoàn thành.',
                'Khách hàng đã nhận hàng, đang kiểm tra.',
                'Đã giao hàng đúng hẹn, chờ thanh toán.',
                'Giao hàng thành công, khách hàng hài lòng.',
                'Đã bàn giao đầy đủ hàng hóa và chứng từ.'
            ],
            'completed' => [
                'Chuyến hàng đã hoàn thành tốt đẹp.',
                'Hoàn thành chuyến và đã thanh toán đầy đủ.',
                'Khách hàng đánh giá cao dịch vụ.',
                'Hoàn thành đúng tiến độ và không có vấn đề phát sinh.',
                'Đã hoàn thành và lưu trữ đầy đủ chứng từ.'
            ],
            'delayed' => [
                'Bị trễ do kẹt xe trên đường cao tốc.',
                'Trễ khoảng 2 giờ do thời tiết xấu.',
                'Phương tiện gặp sự cố nhỏ, đang khắc phục.',
                'Chậm trễ do thủ tục tại trạm kiểm soát.',
                'Bị hoãn do đường ngập nước sau mưa lớn.'
            ],
            'cancelled' => [
                'Hủy theo yêu cầu của khách hàng.',
                'Phương tiện gặp sự cố không thể khắc phục kịp thời.',
                'Hủy do điều kiện thời tiết cực đoan.',
                'Khách hàng thay đổi kế hoạch đột xuất.',
                'Hủy do vấn đề giấy tờ pháp lý.'
            ]
        ];

        return $notes[$status][array_rand($notes[$status])];
    }
}