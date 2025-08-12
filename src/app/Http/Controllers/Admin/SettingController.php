<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\SettingService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipmentDeductionType;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\Setting\UpdateSettingRequest;

class SettingController extends Controller
{
    /**
     * @var SettingService
     */
    protected $settingService;

    /**
     * SettingController constructor.
     *
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Hiển thị trang cài đặt
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('group', session('last_active_tab', 'company'));
        $groups = ['company', 'system', 'shipment', 'shepment-fee', 'notifications'];
        
        $settings = [];
        foreach ($groups as $group) {
            $settings[$group] = $this->settingService->getByGroup($group);
        }

        $query = ShipmentDeductionType::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['id', 'name', 'type', 'status', 'order', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->ordered();
        }

        $deductionTypes = $query->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $deductionTypes,
            ]);
        }

        return view('admin.settings.index', compact('settings', 'activeTab', 'groups', 'deductionTypes'));
    }

    /**
     * Cập nhật cài đặt
     *
     * @param UpdateSettingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSettingRequest $request, $tab = 'company')
    {
        try {
            session(['last_active_tab' => $tab]);
            
            // Lấy dữ liệu theo group từ request
            $companySettings = $request->input('company', []);
            $systemSettings = $request->input('system', []);
            $shipmentSettings = $request->input('shipment', []);
            $notificationSettings = $request->input('notifications', []);
            
            // Xử lý từng nhóm cài đặt
            $this->processGroupSettings($companySettings, 'company');
            $this->processGroupSettings($systemSettings, 'system');
            $this->processGroupSettings($shipmentSettings, 'shipment');
            $this->processGroupSettings($notificationSettings, 'notifications');
            
            // Xử lý upload file logo nếu có
            if ($request->hasFile('company') && isset($request->file('company')['company_logo'])) {
                $file = $request->file('company')['company_logo'];
                $path = \App\Helpers\ImageHelper::upload($file, 'settings');
                $this->settingService->set('company_logo', $path, 'company', 'string', 'Logo công ty');
            }
            
            // Xóa cache
            $this->settingService->clearCache();
            
            // Nếu thay đổi múi giờ, cập nhật cấu hình ứng dụng
            if (isset($systemSettings['timezone'])) {
                config(['app.timezone' => $systemSettings['timezone']]);
            }
            
            return redirect()->route('admin.settings.index')->withFragment($tab)
                ->with('success', 'Cài đặt đã được cập nhật thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật cài đặt: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'Đã xảy ra lỗi khi cập nhật cài đặt: ' . $e->getMessage());
        }
    }

    /**
     * Khởi tạo lại cài đặt mặc định
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetToDefault()
    {
        try {
            $this->settingService->initDefaultSettings();
            $this->settingService->clearCache();
            
            return redirect()->route('admin.settings.index')
                ->with('success', 'Cài đặt đã được khôi phục về mặc định.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi khôi phục cài đặt mặc định: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi khôi phục cài đặt mặc định: ' . $e->getMessage());
        }
    }

    /**
     * Xóa cache
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        try {
            $this->settingService->clearCache();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            return redirect()->route('admin.settings.index')
                ->with('success', 'Cache đã được xóa thành công.');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa cache: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi xóa cache: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý cài đặt theo nhóm
     *
     * @param array $settings Mảng cài đặt
     * @param string $group Tên nhóm
     * @return void
     */
    private function processGroupSettings(array $settings, string $group)
    {
        foreach ($settings as $key => $value) {
            $setting = $this->settingService->settingRepository->getByKey($key);
            
            // Xác định type và description
            $type = 'string'; // Mặc định là string
            $description = null;
            
            if ($setting) {
                // Nếu setting đã tồn tại, lấy thông tin từ setting
                $type = $setting->type;
                $description = $setting->description;
                
                // Xử lý giá trị dựa trên loại
                switch ($type) {
                    case 'boolean':
                        $value = (bool) $value;
                        break;
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'float':
                        $value = (float) $value;
                        break;
                    case 'array':
                    case 'json':
                        // Nếu là chuỗi JSON, giữ nguyên
                        if (is_string($value) && json_decode($value) !== null) {
                            break;
                        }
                        // Nếu là mảng, chuyển thành JSON
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }
                        break;
                }
            }
            
            // Lưu hoặc cập nhật setting
            $this->settingService->set($key, $value, $group, $type, $description);
        }
    }
}
