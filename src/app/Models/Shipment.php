<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enum\UserStatus;

class Shipment extends Model
{
    // ...
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    use HasFactory, SoftDeletes;

    /**
     * Các thuộc tính có thể gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_code',
        'contract_id',
        'customer_id', // khách hàng
        'origin', // điểm xuất phát
        'destination', // điểm đến
        'departure_time', // thời gian khởi hành
        'estimated_arrival_time', // thời gian đến
        'cargo_weight', // trọng lượng hàng hóa
        'cargo_description',
        'driver_id',
        'co_driver_id',
        'vehicle_id',
        'distance', // số km
        'unit_price', // giá chuyến hàng
        'trip_count', // số lượng chuyến hàng
        'crane_price',
        'has_crane_service',
        'notes',
        'status',
        'created_by',
        'updated_by'
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
        'crane_price' => 'decimal:2', // Cast cho trường đơn giá cẩu hàng
        'has_crane_service' => 'boolean', // Cast cho trường có dịch vụ cẩu hàng
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Mảng các giá trị enum cho trạng thái chuyến hàng.
     */
    public static $statuses = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'in_transit' => 'Đang vận chuyển',
        'delivered' => 'Đã giao hàng',
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

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_IN_TRANSIT => 'Đang vận chuyển',
            self::STATUS_DELIVERED => 'Đã giao hàng',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_DELAYED => 'Bị trễ',
            self::STATUS_COMPLETED => 'Hoàn thành'
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
        return $this->belongsTo(Vehicle::class, 'vehicle_id');  
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
     * Kiểm tra xem shipment có đã hoàn thành hay không
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
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
        return $this->shipmentDeductions()->whereHas('shipmentDeductionType', function($query) {
            $query->where('type', 'driver_and_busboy');
        })->whereHas('user', function($subQuery) {
            $subQuery->where('status', UserStatus::ACTIVE);
        })->whereNotNull('user_id')->first()->user ?? null;
    }

    /**
     * Summary of shipmentDeductionTypeDriverAndBusboy
     * @param mixed $userId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentDeduction, Shipment>
     */
    public function shipmentDeductionTypeDriverAndBusboy($userId)
    {
        return $this->shipmentDeductions()->whereHas('shipmentDeductionType', function($query) {
            $query->where('type', ShipmentDeductionType::TYPE_DRIVER_AND_BUSBOY);
        })->where('user_id', $userId);
    }

    /**
     * Summary of shipmentDeductionTypeExpense
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentDeduction, Shipment>
     */
    public function shipmentDeductionTypeExpense()
    {
        return  $this->shipmentDeductions()->whereHas('shipmentDeductionType', function($query) {
            $query->where('type', ShipmentDeductionType::TYPE_EXPENSE);
        });
    }
}