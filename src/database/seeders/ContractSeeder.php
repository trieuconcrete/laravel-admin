<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách ID của khách hàng
        $customerIds = Customer::pluck('id')->toArray();
        
        if (empty($customerIds)) {
            $this->command->info('Không có khách hàng để tạo hợp đồng. Vui lòng chạy CustomerSeeder trước.');
            return;
        }
        
        // Tạo dữ liệu mẫu cho các hợp đồng
        $this->createSampleContracts($customerIds);

        Contract::factory(10)->create(); // Hợp đồng ngẫu nhiên
        Contract::factory(5)->active()->create(); // Hợp đồng đang hoạt động
        Contract::factory(3)->expiringSoon()->create(); // Hợp đồng sắp hết hạn
        Contract::factory(2)->draft()->create(); // Hợp đồng nháp
        Contract::factory(5)->containerShipping()->active()->create(); // Hợp đồng vận chuyển container đang hoạt động
    }

    /**
     * Tạo dữ liệu mẫu cho hợp đồng
     */
    private function createSampleContracts(array $customerIds): void
    {
        $contractTypes = [
            [
                'title_prefix' => 'Hợp đồng vận chuyển hàng hóa',
                'service' => 'Dịch vụ vận chuyển hàng hóa theo tuyến cố định từ kho đến các điểm phân phối.',
                'route' => [
                    'origin' => ['Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng'],
                    'destination' => ['Bình Dương', 'Đồng Nai', 'Long An', 'Bắc Ninh', 'Hưng Yên', 'Quảng Nam', 'Hải Dương']
                ],
                'value_range' => [50000000, 500000000]
            ],
            [
                'title_prefix' => 'Hợp đồng logistics tổng hợp',
                'service' => 'Dịch vụ logistics tổng hợp bao gồm vận chuyển, lưu kho, và phân phối hàng hóa.',
                'route' => [
                    'origin' => ['Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng'],
                    'destination' => ['Toàn quốc']
                ],
                'value_range' => [200000000, 2000000000]
            ],
            [
                'title_prefix' => 'Hợp đồng vận chuyển container',
                'service' => 'Dịch vụ vận chuyển container từ cảng về kho và ngược lại.',
                'route' => [
                    'origin' => ['Cảng Cát Lái', 'Cảng Hải Phòng', 'Cảng Đà Nẵng'],
                    'destination' => ['Các khu công nghiệp lân cận']
                ],
                'value_range' => [100000000, 800000000]
            ],
            [
                'title_prefix' => 'Hợp đồng vận chuyển hàng nội địa',
                'service' => 'Dịch vụ vận chuyển hàng hóa giữa các tỉnh thành trong nước.',
                'route' => [
                    'origin' => ['Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ'],
                    'destination' => ['Toàn quốc']
                ],
                'value_range' => [80000000, 600000000]
            ],
            [
                'title_prefix' => 'Hợp đồng thuê xe tải dài hạn',
                'service' => 'Dịch vụ cho thuê xe tải dài hạn kèm tài xế và nhiên liệu.',
                'route' => [
                    'origin' => ['Theo yêu cầu khách hàng'],
                    'destination' => ['Theo yêu cầu khách hàng']
                ],
                'value_range' => [150000000, 1200000000]
            ],
        ];

        $vehicleTypes = [
            [
                'type' => 'Xe tải nhỏ',
                'capacity' => '1-2 tấn',
                'quantity' => '2-5 xe'
            ],
            [
                'type' => 'Xe tải vừa',
                'capacity' => '3.5-8 tấn',
                'quantity' => '3-8 xe'
            ],
            [
                'type' => 'Xe tải lớn',
                'capacity' => '10-15 tấn',
                'quantity' => '2-4 xe'
            ],
            [
                'type' => 'Xe đầu kéo container',
                'capacity' => '20-40 feet',
                'quantity' => '2-6 xe'
            ],
            [
                'type' => 'Xe tải đông lạnh',
                'capacity' => '5-10 tấn',
                'quantity' => '1-3 xe'
            ],
        ];

        $cargoTypes = [
            [
                'type' => 'Hàng tiêu dùng',
                'packaging' => 'Thùng carton',
                'special_requirements' => 'Không có'
            ],
            [
                'type' => 'Hàng điện tử',
                'packaging' => 'Thùng carton có lót xốp',
                'special_requirements' => 'Tránh va đập, không để dưới trời mưa'
            ],
            [
                'type' => 'Hàng may mặc',
                'packaging' => 'Kiện hàng và thùng carton',
                'special_requirements' => 'Giữ khô ráo, tránh ẩm mốc'
            ],
            [
                'type' => 'Hàng thực phẩm',
                'packaging' => 'Thùng nhựa, bao bì kín',
                'special_requirements' => 'Nhiệt độ phù hợp, đảm bảo vệ sinh'
            ],
            [
                'type' => 'Vật liệu xây dựng',
                'packaging' => 'Bao xi măng, kiện gạch, sắt thép',
                'special_requirements' => 'Xe tải chịu tải trọng cao'
            ],
        ];

        $statuses = ['draft', 'pending', 'active', 'completed', 'terminated', 'cancelled'];
        $statusWeights = [10, 15, 40, 20, 10, 5]; // Trọng số cho mỗi trạng thái

        $paymentMethods = ['cash', 'bank_transfer', 'credit_card', 'other'];
        $paymentTermsTemplates = [
            'Thanh toán 50% giá trị hợp đồng khi ký kết, 50% còn lại sau khi hoàn thành.',
            'Thanh toán theo tiến độ: 30% khi ký hợp đồng, 40% sau 1 tháng, 30% khi kết thúc hợp đồng.',
            'Thanh toán hàng tháng theo số lượng chuyến vận chuyển thực tế.',
            'Thanh toán trọn gói sau khi hoàn thành dịch vụ.',
            'Thanh toán 70% trước khi bắt đầu, 30% sau khi hoàn thành.'
        ];

        // Tạo 20 hợp đồng mẫu
        for ($i = 0; $i < 20; $i++) {
            // Chọn ngẫu nhiên thông tin
            $customerId = $customerIds[array_rand($customerIds)];
            $customer = Customer::find($customerId);
            
            $contractType = $contractTypes[array_rand($contractTypes)];
            $vehicleType = $vehicleTypes[array_rand($vehicleTypes)];
            $cargoType = $cargoTypes[array_rand($cargoTypes)];
            
            // Chọn trạng thái theo trọng số
            $status = $this->getRandomWeightedElement($statuses, $statusWeights);
            
            // Tạo các ngày có ý nghĩa
            $signingDate = Carbon::now()->subDays(rand(10, 365));
            $startDate = (clone $signingDate)->addDays(rand(1, 30));
            
            // Nếu trạng thái là hoàn thành hoặc chấm dứt, thì ngày kết thúc phải ở quá khứ
            $endDate = null;
            if (in_array($status, ['completed', 'terminated'])) {
                $endDate = (clone $startDate)->addDays(rand(30, 180))->subDays(rand(10, 30));
            } elseif ($status !== 'cancelled') { // Nếu không phải là hủy, thì có ngày kết thúc trong tương lai
                $endDate = (clone $startDate)->addDays(rand(90, 730)); // 3 tháng đến 2 năm
            }
            
            // Tạo thông tin tuyến đường
            $origin = $contractType['route']['origin'][array_rand($contractType['route']['origin'])];
            $destination = $contractType['route']['destination'][array_rand($contractType['route']['destination'])];
            if ($destination === 'Toàn quốc') {
                $destination = ['Miền Bắc', 'Miền Trung', 'Miền Nam'][array_rand(['Miền Bắc', 'Miền Trung', 'Miền Nam'])];
            }
            if ($destination === 'Các khu công nghiệp lân cận') {
                $destination = 'KCN ' . ['Tân Thuận', 'Sóng Thần', 'VSIP', 'Quế Võ', 'Đình Vũ'][array_rand(['Tân Thuận', 'Sóng Thần', 'VSIP', 'Quế Võ', 'Đình Vũ'])];
            }
            
            // Tạo mã hợp đồng
            $contractCode = 'CONTRACT-' . date('Ym', strtotime($signingDate)) . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            
            // Tạo giá trị hợp đồng
            $value = rand($contractType['value_range'][0], $contractType['value_range'][1]);
            $value = round($value, -4); // Làm tròn đến hàng chục nghìn
            
            // Thông tin người liên hệ (sử dụng thông tin liên hệ của khách hàng hoặc tạo mới)
            $useCustomerContact = rand(0, 1) === 1;
            $contactName = $useCustomerContact && $customer->primary_contact_name ? $customer->primary_contact_name : $this->generateRandomName();
            $contactPhone = $useCustomerContact && $customer->primary_contact_phone ? $customer->primary_contact_phone : $this->generateRandomPhone();
            $contactEmail = $useCustomerContact && $customer->primary_contact_email ? $customer->primary_contact_email : strtolower(str_replace(' ', '.', $contactName)) . '@example.com';
            $contactPosition = $useCustomerContact && $customer->primary_contact_position ? $customer->primary_contact_position : ['Giám đốc', 'Trưởng phòng logistics', 'Trưởng phòng vận tải', 'Quản lý chuỗi cung ứng', 'Nhân viên đặt hàng'][array_rand(['Giám đốc', 'Trưởng phòng logistics', 'Trưởng phòng vận tải', 'Quản lý chuỗi cung ứng', 'Nhân viên đặt hàng'])];
            
            // Tạo hợp đồng
            Contract::create([
                'contract_code' => $contractCode,
                'title' => $contractType['title_prefix'] . ' - ' . $customer->name,
                'customer_id' => $customerId,
                'contact_name' => $contactName,
                'contact_phone' => $contactPhone,
                'contact_email' => $contactEmail,
                'contact_position' => $contactPosition,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'signing_date' => $signingDate,
                'total_value' => $value,
                'currency' => 'VND',
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_terms' => $paymentTermsTemplates[array_rand($paymentTermsTemplates)],
                'service_description' => $contractType['service'],
                'status' => $status,
                'notes' => $this->generateRandomNotes($status),
                'termination_reason' => $status === 'terminated' ? $this->generateRandomTerminationReason() : null,
                'created_by' => 1,
                'updated_by' => rand(0, 1) === 1 ? 1 : null,
                'approved_by' => in_array($status, ['active', 'completed', 'terminated']) ? 1 : null,
                'approved_at' => in_array($status, ['active', 'completed', 'terminated']) ? $startDate->copy()->subDays(rand(1, 5)) : null,
                'created_at' => $signingDate,
                'updated_at' => $status !== 'draft' ? $signingDate->copy()->addDays(rand(1, 10)) : $signingDate,
            ]);
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
     * Tạo tên ngẫu nhiên
     */
    private function generateRandomName()
    {
        $firstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng'];
        $middleNames = ['Văn', 'Thị', 'Đức', 'Hữu', 'Minh', 'Quang', 'Thanh', 'Kim', 'Đình', 'Hoài'];
        $lastNames = ['An', 'Bình', 'Cường', 'Dũng', 'Hà', 'Hải', 'Hiếu', 'Hùng', 'Linh', 'Mai', 'Nam', 'Phong', 'Thanh', 'Thảo', 'Tuấn', 'Vân', 'Yến'];
        
        return $firstNames[array_rand($firstNames)] . ' ' . $middleNames[array_rand($middleNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * Tạo số điện thoại ngẫu nhiên
     */
    private function generateRandomPhone()
    {
        $prefixes = ['090', '091', '092', '093', '094', '096', '097', '098', '099', '086', '088', '089', '085', '084', '083', '082', '081', '070', '079', '077', '076', '078', '032', '033', '034', '035', '036', '037', '038', '039'];
        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = sprintf('%07d', rand(0, 9999999));
        return $prefix . $suffix;
    }

    /**
     * Tạo ghi chú ngẫu nhiên theo trạng thái
     */
    private function generateRandomNotes($status)
    {
        $notes = [
            'draft' => [
                'Cần xem xét lại giá trị hợp đồng.',
                'Đang chờ bổ sung thông tin từ khách hàng.',
                'Bản nháp, cần chỉnh sửa trước khi gửi cho khách hàng.',
                'Chờ cập nhật điều khoản thanh toán.',
                'Đang chờ ý kiến của phòng kỹ thuật về yêu cầu phương tiện.'
            ],
            'pending' => [
                'Đã gửi cho khách hàng xem xét.',
                'Chờ phê duyệt từ ban giám đốc.',
                'Khách hàng đã xem và đang cân nhắc.',
                'Cần trao đổi thêm về điều khoản thanh toán.',
                'Đang chờ đối tác xác nhận lịch ký kết.'
            ],
            'active' => [
                'Hợp đồng đang thực hiện tốt.',
                'Khách hàng hài lòng với dịch vụ.',
                'Cần thêm phương tiện vào các ngày cuối tuần.',
                'Có thể mở rộng hợp đồng trong tương lai.',
                'Khách hàng đã thanh toán đúng hạn các kỳ trước.'
            ],
            'completed' => [
                'Hợp đồng đã hoàn thành đúng tiến độ.',
                'Khách hàng rất hài lòng, có thể hợp tác lại trong tương lai.',
                'Đã bàn giao đầy đủ hồ sơ và thanh lý hợp đồng.',
                'Hoàn thành tốt, nhưng có một số vấn đề nhỏ về thời gian giao hàng.',
                'Đã hoàn thành và được đánh giá cao về chất lượng dịch vụ.'
            ],
            'terminated' => [
                'Đã chấm dứt theo thỏa thuận của hai bên.',
                'Cần theo dõi việc thanh toán số dư còn lại.',
                'Đã bàn giao và làm thủ tục chấm dứt hợp đồng.',
                'Khách hàng đã chấm dứt sớm do thay đổi kế hoạch kinh doanh.',
                'Cần rút kinh nghiệm cho các hợp đồng tương tự.'
            ],
            'cancelled' => [
                'Hủy do không đạt được thỏa thuận về giá.',
                'Khách hàng hủy do thay đổi kế hoạch.',
                'Hủy trước khi ký kết chính thức.',
                'Không đủ năng lực đáp ứng yêu cầu của khách hàng.',
                'Hủy do phát sinh vấn đề pháp lý.'
            ]
        ];

        return $notes[$status][array_rand($notes[$status])];
    }

    /**
     * Tạo lý do chấm dứt ngẫu nhiên
     */
    private function generateRandomTerminationReason()
    {
        $reasons = [
            'Khách hàng thay đổi chiến lược kinh doanh.',
            'Không đủ khả năng đáp ứng nhu cầu tăng cao của khách hàng.',
            'Khách hàng cắt giảm chi phí do tình hình kinh tế.',
            'Kết thúc dự án sớm hơn dự kiến.',
            'Thay đổi nhà cung cấp dịch vụ vận tải.',
            'Khó khăn trong việc duy trì chất lượng dịch vụ.',
            'Tái cơ cấu hoạt động của khách hàng.',
            'Chuyển đổi mô hình vận chuyển sang hình thức khác.'
        ];

        return $reasons[array_rand($reasons)];
    }
}