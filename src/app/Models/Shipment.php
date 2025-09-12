<?php

namespace App\Models;

use App\Enum\UserStatus;
use App\Models\ShipmentDeductionType;
use App\Traits\HasCompletedStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory, SoftDeletes, HasCompletedStatus;

    // ...
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Các thuộc tính có thể gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_code',
        'contract_id',
        'customer_id', // khách hàng
        'origin', // điểm đi 1
        'origin2', // điểm đi 2
        'origin3', // điểm đi 3
        'destination', // điểm đến 1
        'destination2', // điểm đến 2
        'destination3', // điểm đến 2
        'company',
        'company2', 
        'company3',
        'address_destination',  // điểm đến địa chỉ 1
        'address_destination2', // điểm đến địa chỉ 2
        'address_destination3', // điểm đến địa chỉ 3
        'departure_time', // thời gian khởi hành
        'estimated_arrival_time', // thời gian đến
        'cargo_weight', // trọng lượng hàng hóa
        'cargo_description',
        'driver_id',
        'co_driver_id',
        'vehicle_id',
        'distance', // số km
        'unit_price', // giá chuyến hàng
        'unit_price_for_car_rental', // giá chuyến hàng
        'unit_price_for_driver', // giá chuyến hàng cho tài xế
        'trip_count', // số lượng chuyến hàng
        'crane_price',
        'has_crane_service',
        'notes',
        'status',
        'is_car_rental', // đánh dấu chuyến hàng sử dụng xe thuê
        'shipment_type', // 1: Khách chạy theo chuyến, 2: Khách thuê xe tháng, 3: Xe nâng, 4: Xe đường dài bắc-nam
        'created_by',
        'updated_by',
        
        // Từ CarRentalVehicleLog
        'car_rental_id',
        'shipment_report_id', // ID báo cáo chuyến hàng
        'start_time',
        'end_time',
        'run_date',
        'overtime_hours',
        'start_odometer',
        'end_odometer',
        'overtime_rate',
        'total_overtime_cost',
        'parking_fee',
        'weighing_fee', // Phí cân hàng
        'testing_surcharge', // Phụ phí kiểm tra
        'is_overtime_at_noon'
    ];

    /**
     * Các thuộc tính cần chuyển đổi.
     *
     * @var array
     */
    protected $casts = [
        'departure_time' => 'datetime',
        'estimated_arrival_time' => 'datetime',
        'cargo_weight' => 'decimal:2',
        'distance' => 'decimal:2', // Cast cho trường số km
        'unit_price' => 'decimal:2',
        'trip_count' => 'decimal:2',
        'unit_price_for_car_rental' => 'decimal:2',
        'unit_price_for_driver' => 'decimal:2',
        'crane_price' => 'decimal:2', // Cast cho trường đơn giá cẩu hàng
        'has_crane_service' => 'boolean', // Cast cho trường có dịch vụ cẩu hàng
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        
        // Từ CarRentalVehicleLog
        'start_time' => 'string', // time
        'end_time' => 'string',   // time  
        'run_date' => 'date',
        'overtime_hours' => 'decimal:2',
        'start_odometer' => 'decimal:2',
        'end_odometer' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'total_overtime_cost' => 'decimal:2',
        'parking_fee' => 'decimal:2',
        'weighing_fee' => 'decimal:2',
        'testing_surcharge' => 'decimal:2',
        'is_overtime_at_noon' => 'boolean',
    ];

    /**
     * Mảng các giá trị enum cho trạng thái chuyến hàng.
     */
    public static $statuses = [
        'pending' => 'Tạo mới',
        // 'confirmed' => 'Đã xác nhận',
        'in_transit' => 'Đang vận chuyển',
        'cancelled' => 'Đã hủy',
        'delayed' => 'Bị trễ',
        'completed' => 'Hoàn thành'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DELAYED = 'delayed';
    const STATUS_COMPLETED = 'completed';

    // Shipment Type Constants (đáp ứng issue #180)
    const SHIPMENT_TYPE_PER_TRIP = 1;      // Khách chạy theo chuyến
    const SHIPMENT_TYPE_MONTHLY_RENTAL = 2; // Khách thuê xe tháng
    const SHIPMENT_TYPE_CRANE = 3;         // Xe nâng
    const SHIPMENT_TYPE_LONG_DISTANCE = 4; // Xe đường dài bắc-nam

    public static $shipmentTypes = [
        '1' => 'Khách chạy theo chuyến',
        '2' => 'Khách thuê xe tháng',
        '3' => 'Xe nâng',
        '4' => 'Xe đường dài bắc-nam',
    ];

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Tạo mới',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_IN_TRANSIT => 'Đang vận chuyển',
            self::STATUS_DELIVERED => 'Đã giao hàng',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_DELAYED => 'Bị trễ',
            self::STATUS_COMPLETED => 'Hoàn thành'
        ];
    }

    public static function getShipmentTypes()
    {
        return [
            self::SHIPMENT_TYPE_PER_TRIP => 'Khách chạy theo chuyến',
            self::SHIPMENT_TYPE_MONTHLY_RENTAL => 'Khách thuê xe tháng',
            self::SHIPMENT_TYPE_CRANE => 'Xe nâng',
            self::SHIPMENT_TYPE_LONG_DISTANCE => 'Xe đường dài bắc-nam'
        ];
    }
    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? '';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_CONFIRMED => 'bg-info',
            self::STATUS_IN_TRANSIT => 'bg-primary',
            self::STATUS_DELIVERED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            self::STATUS_DELAYED => 'bg-danger',
            self::STATUS_COMPLETED => 'bg-success',
        };
    }

    /**
     * Quan hệ với hợp đồng
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Quan hệ với hợp đồng thuê xe
     */
    public function carRental()
    {
        return $this->belongsTo(CarRental::class, 'car_rental_id');
    }

    /**
     * Quan hệ với tài xế
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Quan hệ với lơ xe
     */
    public function coDriver()
    {
        return $this->belongsTo(User::class, 'co_driver_id');
    }

    /**
     * Quan hệ với phương tiện
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    /**
     * Lấy thông tin phí cầu đường
     */
    public function tollFees()
    {
        return $this->hasMany(TollFee::class, 'shipment_id');
    }

    /**
     * Quan hệ với người tạo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Quan hệ với người cập nhật
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function goods()
    {
        return $this->hasMany(ShipmentGood::class);
    }

    public function shipmentDeductions()
    {
        return $this->hasMany(ShipmentDeduction::class);
    }

    public function shipmentExtraFee()
    {
        return $this->hasMany(ShipmentDeduction::class)->whereHas('shipmentDeductionType', function($query) {
            $query->where('type', ShipmentDeductionType::TYPE_EXPENSE);
        });
    }

    public function shipmentDeductionTypes()
    {
        return $this->hasMany(ShipmentDeductionType::class);
    }

    /**
     * Scope để lọc shipment theo trạng thái
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope để lọc shipment theo chuyến xe
     */
    public function scopeShipmentType($query, $shipmentType)
    {
        return $query->where('shipment_type', $shipmentType);
    }

    /**
     * Scope để tìm shipment đang vận chuyển
     */
    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    /**
     * Scope để tìm shipment theo khoảng thời gian khởi hành
     */
    public function scopeDepartureBetween($query, $start, $end)
    {
        return $query->whereBetween('departure_time', [$start, $end]);
    }

    /**
     * Scope để tìm shipment đang chậm trễ
     */
    public function scopeDelayed($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'delayed')
              ->orWhere(function($sq) {
                  $sq->where('status', 'in_transit')
                    ->whereNotNull('estimated_arrival_time')
                    ->where('estimated_arrival_time', '<', now());
              });
        });
    }

    /**
     * Scope để tìm shipment có dịch vụ cẩu hàng
     */
    public function scopeWithCraneService($query)
    {
        return $query->where('has_crane_service', true);
    }

    /**
     * Scope để tìm kiếm shipment
     */
    public function scopeSearch($query, $term)
    {
        if ($term) {
            return $query->where(function($q) use ($term) {
                $q->where('shipment_code', 'LIKE', "%{$term}%")
                  ->orWhere('origin', 'LIKE', "%{$term}%")
                  ->orWhere('destination', 'LIKE', "%{$term}%")
                  ->orWhere('cargo_description', 'LIKE', "%{$term}%") // Thêm tìm kiếm theo mô tả hàng hóa
                  ->orWhereHas('driver', function($subQuery) use ($term) {
                      $subQuery->where('full_name', 'LIKE', "%{$term}%");
                  })
                  ->orWhereHas('vehicle', function($subQuery) use ($term) {
                      $subQuery->where('plate_number', 'LIKE', "%{$term}%");
                  });
            });
        }
        
        return $query;
    }

    /**
     * Tính tổng giá trị chuyến hàng (không bao gồm cẩu hàng)
     */
    public function getTransportValueAttribute()
    {
        return $this->distance * $this->unit_price;
    }

    /**
     * Tính giá trị dịch vụ cẩu hàng
     */
    public function getCraneValueAttribute()
    {
        return $this->has_crane_service ? $this->crane_price : 0;
    }

    /**
     * Tính tổng giá trị chuyến hàng (bao gồm cả cẩu hàng nếu có)
     */
    public function getTotalValueAttribute()
    {
        $transportValue = $this->distance * $this->unit_price;
        $craneValue = $this->has_crane_service ? $this->crane_price : 0;
        
        return $transportValue + $craneValue;
    }

    /**
     * Kiểm tra xem shipment có đang vận chuyển hay không
     */
    public function isInTransit()
    {
        return $this->status === 'in_transit';
    }



    /**
     * Kiểm tra xem shipment có đang chậm trễ hay không
     */
    public function isDelayed()
    {
        return $this->status === 'delayed' || 
               ($this->status === 'in_transit' && 
                $this->estimated_arrival_time && 
                now()->gt($this->estimated_arrival_time));
    }

    /**
     * Cập nhật trạng thái shipment
     */
    public function updateStatus($status, $userId = null, $notes = null)
    {
        $this->status = $status;
        $this->updated_by = $userId;
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Tạo mã shipment ngẫu nhiên
     */
    public static function generateShipmentCode()
    {
        $prefix = 'SHP';
        $date = now()->format('ymd');
        $random = strtoupper(substr(md5(microtime()), 0, 4));
        
        return $prefix . $date . $random;
    }

    /**
     * Summary of getDriverFromShipmentDeductions
     */
    public function getDriverFromShipmentDeductions()
    {
        // Ưu tiên lấy tài xế có is_main_driver = true
        $mainDriver = $this->shipmentDeductions()
            ->whereHas('shipmentDeductionType', function($query) {
                $query->whereIn('type', ['driver', 'bus_driver']);
            })
            ->whereHas('user', function($subQuery) {
                $subQuery->where('status', UserStatus::ACTIVE);
            })
            ->whereNotNull('user_id')
            ->where('is_main_driver', true)
            ->first();
            
        if ($mainDriver) {
            return $mainDriver->user;
        }
        
        // Nếu không có main driver, lấy tài xế đầu tiên
        $firstDriver = $this->shipmentDeductions()
            ->whereHas('shipmentDeductionType', function($query) {
                $query->whereIn('type', ['driver', 'bus_driver']);
            })
            ->whereHas('user', function($subQuery) {
                $subQuery->where('status', UserStatus::ACTIVE);
            })
            ->whereNotNull('user_id')
            ->first();
            
        return $firstDriver ? $firstDriver->user : null;
    }

    /**
     * Get main driver from shipment deductions
     */
    public function getMainDriverFromShipmentDeductions()
    {
        $mainDriverDeduction = $this->shipmentDeductions()
            ->whereHas('shipmentDeductionType', function($query) {
                $query->whereIn('type', ['driver', 'bus_driver']);
            })
            ->whereHas('user', function($subQuery) {
                $subQuery->where('status', UserStatus::ACTIVE);
            })
            ->whereNotNull('user_id')
            ->where('is_main_driver', true)
            ->first();
            
        return $mainDriverDeduction ? $mainDriverDeduction->user : null;
    }

    /**
     * Get all drivers from shipment deductions
     */
    public function getAllDriversFromShipmentDeductions()
    {
        return $this->shipmentDeductions()
            ->whereHas('shipmentDeductionType', function($query) {
                $query->whereIn('type', ['driver', 'bus_driver']);
            })
            ->whereHas('user', function($subQuery) {
                $subQuery->where('status', UserStatus::ACTIVE);
            })
            ->whereNotNull('user_id')
            ->with('user')
            ->get()
            ->map(function($deduction) {
                return [
                    'user' => $deduction->user,
                    'is_main_driver' => $deduction->is_main_driver,
                    'type' => $deduction->shipmentDeductionType->type ?? 'unknown'
                ];
            });
    }

    /**
     * Get driver with fallback methods
     */
    public function getDriverDisplay()
    {
        // Try shipment deductions first (with main driver priority)
        $driver = $this->getDriverFromShipmentDeductions();
        if ($driver) {
            return $driver->full_name;
        }
        
        // Fallback to direct driver relationship
        if ($this->driver) {
            return $this->driver->full_name;
        }
        
        // Fallback to co-driver
        if ($this->coDriver) {
            return $this->coDriver->full_name;
        }
        
        return 'Xe HPL thuê';
    }

    /**
     * Summary of shipmentDeductionTypeDriverAndBusboy
     * @param mixed $userId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentDeduction, Shipment>
     */
    public function shipmentDeductionTypeDriverAndBusboy($userId)
    {
        return $this->shipmentDeductions()->whereHas('shipmentDeductionType', function($query) {
            $query->whereIn('type', [ShipmentDeductionType::TYPE_DRIVER, ShipmentDeductionType::TYPE_BUS_DRIVER])
                ->where('status', 'active');
        })->where('user_id', $userId);
    }

    /**
     * Summary of shipmentDeductionTypeExpense
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentDeduction, Shipment>
     */
    public function shipmentDeductionTypeExpense()
    {
        return $this->hasMany(ShipmentDeduction::class)
            ->whereHas('shipmentDeductionType', function($query) {
                $query->where('type', ShipmentDeductionType::TYPE_EXPENSE)
                    ->where('status', ShipmentDeductionType::STATUS_ACTIVE);
        });
    }

    /**
     * PHỤ THU KẾT HỢP
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentDeduction, Shipment>
     */
    public function shipmentDeductionTypeExpenseCombinedSurchar()
    {
        return $this->hasMany(ShipmentDeduction::class)
            ->whereHas('shipmentDeductionType', function($query) {
                $query->where('type', ShipmentDeductionType::TYPE_EXPENSE)
                    ->where('name', 'PHỤ THU KẾT HỢP')
                    ->where('status', ShipmentDeductionType::STATUS_ACTIVE);
        });
    }

    /**
     * BỐC XẾP
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentDeduction, Shipment>
     */
    public function shipmentDeductionTypeExpenseCargoHandling()
    {
        return $this->hasMany(ShipmentDeduction::class)
            ->whereHas('shipmentDeductionType', function($query) {
                $query->where('type', ShipmentDeductionType::TYPE_EXPENSE)
                    ->where('name', 'BỐC XẾP')
                    ->where('status', ShipmentDeductionType::STATUS_ACTIVE);
        });
    }

    /**
     * Lấy tổng chi phí phát sinh (expense)
     * @return float
     */
    public function getTotalExpenseDeductionsAttribute()
    {
        return $this->shipmentDeductionTypeExpense()->sum('amount');
    }

    /**
     * Lấy tổng tiền PHỤ THU KẾT HỢP
     * @return float
     */
    public function getTotalCombinedSurchargeAttribute()
    {
        return $this->shipmentDeductionTypeExpenseCombinedSurchar()->sum('amount');
    }

    /**
     * Lấy tổng tiền Bốc 
     * @return float
     */
    public function getTotalCombinedCargoHandlingAttribute()
    {
        return $this->shipmentDeductionTypeExpenseCargoHandling()->sum('amount');
    }

    /**
     * Tính tổng chi phí phát sinh (từ CarRentalVehicleLog)
     */
    public function getTotalExtraCostAttribute()
    {
        return $this->total_overtime_cost + $this->parking_fee;
    }

    /**
     * Tính thời gian hoạt động (giờ) - chỉ khi có start_time và end_time
     */
    public function getOperatingHoursAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }
        
        try {
            $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time);
            $end = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time);
            
            // Nếu end_time nhỏ hơn start_time, có nghĩa là qua ngày
            if ($end->lt($start)) {
                $end->addDay();
            }
            
            return $start->diffInHours($end);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Tính quãng đường thực tế từ đồng hồ
     */
    public function getActualDistanceAttribute()
    {
        if (!$this->start_odometer || !$this->end_odometer) {
            return $this->distance; // Fallback to manual distance
        }
        
        return $this->end_odometer - $this->start_odometer;
    }

    /**
     * Kiểm tra có phải xe HPL thuê không (đáp ứng yêu cầu issue #180)
     */
    public function isHplRental()
    {
        return $this->vehicle && $this->vehicle->is_car_rental;
    }

    /**
     * Kiểm tra có cần chọn tài xế không (logic theo issue #180)
     */
    public function requiresDriverSelection()
    {
        return !$this->isHplRental();
    }

    /**
     * Lấy label cho shipment type
     */
    public function getShipmentTypeLabelAttribute()
    {
        return self::getShipmentTypes()[$this->shipment_type] ?? 'Không xác định';
    }
}