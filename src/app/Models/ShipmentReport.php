<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShipmentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly',
        'shipment_type', // 1: Khách chạy theo chuyến, 2: Khách thuê xe tháng, 3: Xe nâng, 4: Xe đường dài bắc-nam
        'statement_start_date',
        'statement_end_date',
        'customer_id',
        'total_amount',
        'created_by',
        'updated_by',
        'is_finalized',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_finalized' => 'boolean',
        'statement_start_date' => 'date',
        'statement_end_date' => 'date',
        'shipment_type' => 'integer',
    ];

    /**
     * Quan hệ với khách hàng
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
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

    /**
     * Scope để lọc theo tháng
     */
    public function scopeByMonth($query, $month)
    {
        return $query->where('monthly', $month);
    }

    /**
     * Scope để lọc theo khách hàng
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Lấy tổng công nợ của khách hàng
     */
    public static function getCustomerDebtSummary($customerId)
    {
        $totalReported = self::where('customer_id', $customerId)->sum('total_amount');
        
        // Tính tổng đã thanh toán từ bảng transactions
        $totalPaid = DB::table('transactions')
            ->join('payments', 'transactions.payment_id', '=', 'payments.id')
            ->where('payments.customer_id', $customerId)
            ->where('transactions.type', 'income')
            ->sum('transactions.amount');
        
        // Xử lý 2 trường hợp khác nhau
        if ($totalReported < 0) {
            // Trường hợp tổng bảng kê âm (hoàn tiền, điều chỉnh)
            $remainingDebt = $totalReported + $totalPaid; // Cộng vì totalReported đã âm
            
            return [
                'total_reported' => abs($totalReported), // Hiển thị giá trị tuyệt đối
                'total_paid' => $totalPaid,
                'remaining_debt' => $remainingDebt,
                'is_refund_case' => true, // Đánh dấu là trường hợp hoàn tiền
                'debt_type' => $remainingDebt < 0 ? 'customer_owes' : 'company_owes'
            ];
        } else {
            // Trường hợp bình thường (tổng bảng kê >= 0)
            $remainingDebt = $totalReported - $totalPaid;
            
            return [
                'total_reported' => $totalReported,
                'total_paid' => $totalPaid,
                'remaining_debt' => $remainingDebt,
                'is_refund_case' => false,
                'debt_type' => $remainingDebt > 0 ? 'customer_owes' : ($remainingDebt < 0 ? 'company_owes' : 'balanced')
            ];
        }
    }

    /**
     * Kiểm tra thời gian có chồng lên nhau với các bảng kê khác không
     */
    public static function checkTimeOverlap($customerId, $startDate, $endDate, $shipmentType = null, $excludeId = null)
    {
        $query = self::where('customer_id', $customerId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where(function ($subQ) use ($startDate, $endDate) {
                    // Kiểm tra overlap: start_date <= endDate AND end_date >= startDate
                    $subQ->where('statement_start_date', '<=', $endDate)
                          ->where('statement_end_date', '>=', $startDate);
                });
            });

        // Chỉ kiểm tra overlap cho cùng loại shipment_type
        if ($shipmentType) {
            $query->where('shipment_type', $shipmentType);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Lấy danh sách báo cáo theo khách hàng và loại chuyến xe
     */
    public static function getReportsByCustomerAndType($customerId, $shipmentType = null)
    {
        $query = self::where('customer_id', $customerId);
        
        if ($shipmentType) {
            $query->where('shipment_type', $shipmentType);
        }
        
        return $query->orderBy('monthly', 'desc')->get();
    }

    /**
     * Tạo hoặc cập nhật báo cáo tháng
     */
    public static function createOrUpdateMonthlyReport($customerId, $month, $totalAmount, $userId = null)
    {
        return self::updateOrCreate(
            [
                'customer_id' => $customerId,
                'monthly' => $month
            ],
            [
                'total_amount' => $totalAmount,
                'updated_by' => $userId,
                'is_finalized' => true
            ]
        );
    }
} 