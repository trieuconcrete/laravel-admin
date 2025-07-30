<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentReport;
use App\Models\Transaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialCalculationService
{
    /**
     * Tính tổng tiền từ shipment đã hoàn thành cho một khách hàng trong tháng
     */
    public function calculateCustomerMonthlyCompletedAmount(int $customerId, string $month): float
    {
        return Shipment::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->whereYear('departure_time', Carbon::parse($month)->year)
            ->whereMonth('departure_time', Carbon::parse($month)->month)
            ->get()
            ->sum(function ($shipment) {
                $baseAmount = ($shipment->trip_count ?? 1) * ($shipment->unit_price ?? 0);
                $extraFees = $shipment->shipmentExtraFee->sum('amount');
                return $baseAmount + $extraFees;
            });
    }

    /**
     * Tính tổng tiền từ tất cả shipment đã hoàn thành
     */
    public function calculateTotalCompletedShipmentsAmount(): float
    {
        return Shipment::where('status', 'completed')
            ->get()
            ->sum(function ($shipment) {
                $baseAmount = ($shipment->trip_count ?? 1) * ($shipment->unit_price ?? 0);
                $extraFees = $shipment->shipmentExtraFee->sum('amount');
                return $baseAmount + $extraFees;
            });
    }

    /**
     * Tính tổng tiền đã thanh toán từ transactions
     */
    public function calculateTotalPaidAmount(): float
    {
        return DB::table('transactions')
            ->join('payments', 'transactions.payment_id', '=', 'payments.id')
            ->where('transactions.type', Transaction::TYPE_INCOME)
            ->sum('transactions.amount');
    }

    /**
     * Tính công nợ dựa trên shipment đã hoàn thành
     */
    public function calculateDebtFromCompletedShipments(): float
    {
        $totalCompletedAmount = $this->calculateTotalCompletedShipmentsAmount();
        $totalPaidAmount = $this->calculateTotalPaidAmount();
        
        return $totalCompletedAmount - $totalPaidAmount;
    }

    /**
     * Tính công nợ cho một khách hàng dựa trên shipment đã hoàn thành
     */
    public function calculateCustomerDebtFromCompletedShipments(int $customerId): float
    {
        $totalCompletedAmount = Shipment::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->get()
            ->sum(function ($shipment) {
                $baseAmount = ($shipment->trip_count ?? 1) * ($shipment->unit_price ?? 0);
                $extraFees = $shipment->shipmentExtraFee->sum('amount');
                return $baseAmount + $extraFees;
            });

        $totalPaidAmount = DB::table('transactions')
            ->join('payments', 'transactions.payment_id', '=', 'payments.id')
            ->where('payments.customer_id', $customerId)
            ->where('transactions.type', Transaction::TYPE_INCOME)
            ->sum('transactions.amount');

        return $totalCompletedAmount - $totalPaidAmount;
    }

    /**
     * Tạo hoặc cập nhật báo cáo tháng chỉ cho shipment đã hoàn thành
     */
    public function createOrUpdateMonthlyReportFromCompletedShipments(int $customerId, string $month): ShipmentReport
    {
        $totalAmount = $this->calculateCustomerMonthlyCompletedAmount($customerId, $month);
        
        return ShipmentReport::createOrUpdateMonthlyReport(
            $customerId,
            $month,
            $totalAmount,
            auth('admin')->id()
        );
    }

    /**
     * Lấy thống kê tài chính tổng quan
     */
    public function getFinancialSummary(): array
    {
        $totalCompletedAmount = $this->calculateTotalCompletedShipmentsAmount();
        $totalPaidAmount = $this->calculateTotalPaidAmount();
        $totalDebt = $this->calculateDebtFromCompletedShipments();
        
        $completedShipmentsCount = Shipment::where('status', 'completed')->count();
        $totalShipmentsCount = Shipment::count();
        
        return [
            'total_completed_amount' => $totalCompletedAmount,
            'total_paid_amount' => $totalPaidAmount,
            'total_debt' => $totalDebt,
            'completed_shipments_count' => $completedShipmentsCount,
            'total_shipments_count' => $totalShipmentsCount,
            'completion_rate' => $totalShipmentsCount > 0 ? ($completedShipmentsCount / $totalShipmentsCount) * 100 : 0,
            'payment_rate' => $totalCompletedAmount > 0 ? ($totalPaidAmount / $totalCompletedAmount) * 100 : 0
        ];
    }

    /**
     * Lấy thống kê tài chính theo tháng
     */
    public function getMonthlyFinancialStats(int $year): array
    {
        $stats = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStr = sprintf('%04d-%02d', $year, $month);
            
            $completedAmount = Shipment::where('status', 'completed')
                ->whereYear('departure_time', $year)
                ->whereMonth('departure_time', $month)
                ->get()
                ->sum(function ($shipment) {
                    $baseAmount = ($shipment->trip_count ?? 1) * ($shipment->unit_price ?? 0);
                    $extraFees = $shipment->shipmentExtraFee->sum('amount');
                    return $baseAmount + $extraFees;
                });

            $paidAmount = DB::table('transactions')
                ->join('payments', 'transactions.payment_id', '=', 'payments.id')
                ->where('transactions.type', Transaction::TYPE_INCOME)
                ->whereYear('payments.payment_date', $year)
                ->whereMonth('payments.payment_date', $month)
                ->sum('transactions.amount');

            $stats[$monthStr] = [
                'completed_amount' => $completedAmount,
                'paid_amount' => $paidAmount,
                'debt' => $completedAmount - $paidAmount
            ];
        }
        
        return $stats;
    }
} 