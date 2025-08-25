<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataProdCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contract::truncate();
        // Customer::truncate();
        $csvFile = database_path('customers.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File CSV không tồn tại: {$csvFile}");
            return;
        }

        $this->command->info('Bắt đầu import dữ liệu khách hàng từ CSV...');
        
        DB::beginTransaction();
        
        try {
            $handle = fopen($csvFile, 'r');
            
            if ($handle === false) {
                throw new \Exception('Không thể đọc file CSV');
            }

            // Đọc header row
            $header = fgetcsv($handle);
            if (!$header) {
                throw new \Exception('File CSV trống hoặc không có header');
            }

            $header = array_map('trim', $header);
            $this->command->info('Header columns: ' . implode(', ', $header));

            $rowCount = 0;
            $successCount = 0;
            $errorCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                $rowCount++;
                
                try {
                    // Tạo array dữ liệu từ CSV row
                    $data = array_combine($header, $row);
                    
                    if (!$data) {
                        $this->command->warn("Dòng {$rowCount}: Không thể parse dữ liệu");
                        $errorCount++;
                        continue;
                    }

                    // Validate và prepare data
                    $customerData = $this->prepareCustomerData($data, $rowCount);
                    
                    if (!$customerData) {
                        $errorCount++;
                        continue;
                    }

                    // Tạo hoặc cập nhật customer
                    $customer = Customer::updateOrCreate(
                        [
                            'customer_code' => $customerData['customer_code']
                        ],
                        $customerData
                    );

                    $successCount++;
                    
                    if ($rowCount % 100 == 0) {
                        $this->command->info("Đã xử lý {$rowCount} dòng...");
                    }

                } catch (\Exception $e) {
                    $this->command->error("Lỗi tại dòng {$rowCount}: " . $e->getMessage());
                    $errorCount++;
                    continue;
                }
            }

            fclose($handle);
            
            DB::commit();
            
            $this->command->info("Import hoàn thành!");
            $this->command->info("- Tổng số dòng: {$rowCount}");
            $this->command->info("- Thành công: {$successCount}");
            $this->command->info("- Lỗi: {$errorCount}");

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Lỗi import: ' . $e->getMessage());
            Log::error('Customer import error: ' . $e->getMessage());
        }
    }

    /**
     * Prepare customer data from CSV row
     */
    private function prepareCustomerData(array $data, int $rowNumber): ?array
    {
        try {
            // Validate required fields
            $customerName = trim($data['Tên khách hàng'] ?? '');
            if (empty($customerName)) {
                $this->command->warn("Dòng {$rowNumber}: Thiếu tên khách hàng");
                return null;
            }

            // Xác định type từ nhóm khách hàng hoặc tên
            $type = $this->determineCustomerType($data);

            // Parse địa chỉ để tách province, district, ward
            $addressInfo = $this->parseAddress($data['Địa chỉ'] ?? '');

            // Prepare base data
            $customerData = [
                'name' => $customerName,
                'type' => $type,
                'phone' => $this->cleanPhoneNumber($data['Điện thoại'] ?? ''),
                'email' => null, // CSV không có email
                'address' => $addressInfo['address'],
                'province' => $addressInfo['province'],
                'district' => $addressInfo['district'], 
                'ward' => $addressInfo['ward'],
                'notes' => '',
                'is_active' => !$this->parseBoolean($data['Ngừng theo dõi'] ?? 'FALSE'),
                'created_by' => 1, // Default admin user
                'updated_by' => 1,
            ];

            // Customer code
            $customerCode = trim($data['Mã khách hàng'] ?? '');
            if (!empty($customerCode)) {
                $customerData['customer_code'] = $customerCode;
            } else {
                $customerData['customer_code'] = Customer::generateCustomerCode($type);
            }

            // Business specific fields
            $customerData['tax_code'] = trim($data['Mã số thuế'] ?? '');

            return $customerData;

        } catch (\Exception $e) {
            $this->command->error("Lỗi prepare data dòng {$rowNumber}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Determine customer type from data
     */
    private function determineCustomerType(array $data): string
    {
        $groupType = strtolower(trim($data['Nhóm KH NCC'] ?? ''));
        $customerName = strtolower(trim($data['Tên khách hàng'] ?? ''));
        
        // Check group type first
        if (in_array($groupType, ['doanh nghiệp', 'business', 'công ty'])) {
            return Customer::TYPE_BUSINESS;
        }
        
        if (in_array($groupType, ['cá nhân', 'individual', 'ca nhan'])) {
            return Customer::TYPE_INDIVIDUAL;
        }
        
        // Check by company name patterns
        $businessKeywords = [
            'công ty', 'chi nhánh', 'tập đoàn', 'corporation', 'company', 
            'co.', 'ltd', 'limited', 'joint stock', 'jsc'
        ];
        
        foreach ($businessKeywords as $keyword) {
            if (str_contains($customerName, $keyword)) {
                return Customer::TYPE_BUSINESS;
            }
        }
        
        // Default to individual
        return Customer::TYPE_BUSINESS;
    }

    /**
     * Parse Vietnamese address to extract components
     */
    private function parseAddress(string $fullAddress): array
    {
        $result = [
            'address' => $fullAddress,
            'ward' => '',
            'district' => '',
            'province' => ''
        ];

        if (empty($fullAddress)) {
            return $result;
        }

        // Common patterns for Vietnamese addresses
        $patterns = [
            // Pattern: ..., Ward, District, Province, Country
            '/^(.*?),\s*(Phường|Xã|Thị trấn)\s+([^,]+),\s*(Quận|Huyện|Thành phố|Thị xã)\s+([^,]+),\s*(Tỉnh|Thành phố)\s+([^,]+)(?:,\s*(.+))?$/ui',
            // Pattern: ..., Ward, District, Province
            '/^(.*?),\s*(Phường|Xã|Thị trấn)\s+([^,]+),\s*(Quận|Huyện|Thành phố|Thị xã)\s+([^,]+),\s*(.+)$/ui',
            // Simplified pattern: ..., District, Province
            '/^(.*?),\s*(Quận|Huyện|Thành phố|Thị xã)\s+([^,]+),\s*(.+)$/ui',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $fullAddress, $matches)) {
                switch (count($matches) - 1) { // -1 because first element is full match
                    case 8: // Full pattern with country
                        $result['ward'] = trim($matches[2] . ' ' . $matches[3]);
                        $result['district'] = trim($matches[4] . ' ' . $matches[5]);
                        $result['province'] = trim($matches[6] . ' ' . $matches[7]);
                        break;
                    case 6: // Full pattern without country
                        $result['ward'] = trim($matches[2] . ' ' . $matches[3]);
                        $result['district'] = trim($matches[4] . ' ' . $matches[5]);
                        $result['province'] = trim($matches[6]);
                        break;
                    case 4: // Simplified pattern
                        $result['district'] = trim($matches[2] . ' ' . $matches[3]);
                        $result['province'] = trim($matches[4]);
                        break;
                }
                break;
            }
        }

        return $result;
    }

    /**
     * Clean phone number
     */
    private function cleanPhoneNumber(string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        
        // Remove non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (strlen($phone) < 10) {
            return null;
        }
        
        return $phone;
    }

    /**
     * Clean email address
     */
    private function cleanEmail(string $email): ?string
    {
        if (empty($email)) {
            return null;
        }
        
        $email = trim(strtolower($email));
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        
        return $email;
    }

    /**
     * Clean URL
     */
    private function cleanUrl(string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        
        $url = trim($url);
        
        // Add http:// if no protocol specified
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'http://' . $url;
        }
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        
        return $url;
    }

    /**
     * Parse date string
     */
    private function parseDate(string $date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            $parsedDate = \Carbon\Carbon::parse($date);
            return $parsedDate->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse boolean value
     */
    private function parseBoolean(string $value): bool
    {
        $value = strtolower(trim($value));
        
        // For "Ngừng theo dõi" field - TRUE means stopped/inactive
        return in_array($value, ['true', '1', 'yes', 'có', 'co']);
    }
}