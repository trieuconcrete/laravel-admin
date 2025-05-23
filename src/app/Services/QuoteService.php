<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuoteService
{
    /**
     * Tạo số báo giá mới
     */
    public function generateQuoteNumber(): string
    {
        $prefix = 'BG';
        $date = now()->format('Ymd');
        
        $lastQuote = Quote::whereDate('created_at', today())
                         ->where('quote_number', 'like', $prefix . $date . '%')
                         ->orderBy('quote_number', 'desc')
                         ->first();

        if ($lastQuote) {
            $lastNumber = intval(substr($lastQuote->quote_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Tính toán giá báo giá
     */
    public function calculateQuotePrice(array $data): array
    {
        $basePrice = $data['base_price'] ?? 0;
        $fuelSurcharge = $data['fuel_surcharge'] ?? 0;
        $loadingFee = $data['loading_fee'] ?? 0;
        $insuranceFee = $data['insurance_fee'] ?? 0;
        $additionalFee = $data['additional_fee'] ?? 0;
        $discount = $data['discount'] ?? 0;
        $vatRate = $data['vat_rate'] ?? 10;

        $totalPrice = $basePrice + $fuelSurcharge + $loadingFee + $insuranceFee + $additionalFee - $discount;
        $vatAmount = $totalPrice * ($vatRate / 100);
        $finalPrice = $totalPrice + $vatAmount;

        return [
            'total_price' => round($totalPrice, 2),
            'vat_amount' => round($vatAmount, 2),
            'final_price' => round($finalPrice, 2),
        ];
    }

    /**
     * Lấy thống kê báo giá
     */
    public function getQuoteStatistics(): array
    {
        return [
            'total_quotes' => Quote::count(),
            'draft_quotes' => Quote::where('status', 'draft')->count(),
            'sent_quotes' => Quote::where('status', 'sent')->count(),
            'approved_quotes' => Quote::where('status', 'approved')->count(),
            'rejected_quotes' => Quote::where('status', 'rejected')->count(),
            'expired_quotes' => Quote::where('status', 'expired')->count(),
            'expiring_soon' => Quote::expiringSoon()->count(),
            'total_value' => Quote::where('status', 'approved')->sum('final_price'),
            'this_month_quotes' => Quote::whereMonth('created_at', now()->month)->count(),
            'this_month_value' => Quote::whereMonth('created_at', now()->month)
                                      ->where('status', 'approved')
                                      ->sum('final_price'),
        ];
    }

    /**
     * Lấy báo giá sắp hết hạn
     */
    public function getExpiringSoonQuotes(int $days = 7)
    {
        return Quote::expiringSoon($days)
                   ->with(['creator', 'assignee'])
                   ->orderBy('valid_until', 'asc')
                   ->get();
    }

    /**
     * Đánh dấu báo giá hết hạn
     */
    public function markExpiredQuotes(): int
    {
        $expiredQuotes = Quote::where('valid_until', '<', now())
                             ->whereNotIn('status', ['expired', 'converted', 'rejected'])
                             ->get();

        $count = 0;
        foreach ($expiredQuotes as $quote) {
            $quote->markAsExpired();
            $count++;
        }

        return $count;
    }

    /**
     * Sao chép báo giá
     */
    public function duplicateQuote(Quote $originalQuote): Quote
    {
        DB::beginTransaction();
        
        try {
            $newQuoteData = $originalQuote->toArray();
            
            // Loại bỏ các trường không cần thiết
            unset($newQuoteData['id'], $newQuoteData['created_at'], $newQuoteData['updated_at']);
            
            // Cập nhật thông tin mới
            $newQuoteData['quote_number'] = $this->generateQuoteNumber();
            $newQuoteData['status'] = 'draft';
            $newQuoteData['valid_until'] = now()->addDays(15);
            $newQuoteData['created_by'] = auth()->id();
            
            $newQuote = Quote::create($newQuoteData);
            
            // Sao chép quote items
            foreach ($originalQuote->items as $item) {
                $newItemData = $item->toArray();
                unset($newItemData['id'], $newItemData['quote_id'], $newItemData['created_at'], $newItemData['updated_at']);
                $newItemData['quote_id'] = $newQuote->id;
                
                $newQuote->items()->create($newItemData);
            }
            
            DB::commit();
            
            return $newQuote;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Chuyển đổi báo giá thành đơn hàng
     */
    public function convertToOrder(Quote $quote): bool
    {
        if ($quote->status !== 'approved') {
            return false;
        }

        $quote->update(['status' => 'converted']);
        $quote->addHistory('converted', 'approved', 'converted', 'Báo giá đã được chuyển đổi thành đơn hàng');

        // TODO: Tạo đơn hàng từ báo giá
        // $order = Order::createFromQuote($quote);

        return true;
    }

    /**
     * Gửi email báo giá
     */
    public function sendQuoteEmail(Quote $quote, string $recipientEmail = null): bool
    {
        try {
            $email = $recipientEmail ?? $quote->customer_email;
            
            if (!$email) {
                return false;
            }

            // TODO: Implement email sending
            // Mail::to($email)->send(new QuoteMail($quote));
            
            $quote->update(['status' => 'sent']);
            $quote->addHistory('sent', $quote->getOriginal('status'), 'sent', 'Báo giá đã được gửi email');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send quote email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy báo cáo theo tháng
     */
    public function getMonthlyReport(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $quotes = Quote::whereBetween('created_at', [$startDate, $endDate])
                      ->selectRaw('
                          COUNT(*) as total_quotes,
                          SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_quotes,
                          SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_quotes,
                          SUM(CASE WHEN status = "expired" THEN 1 ELSE 0 END) as expired_quotes,
                          SUM(CASE WHEN status = "approved" THEN final_price ELSE 0 END) as total_value,
                          AVG(CASE WHEN status = "approved" THEN final_price ELSE NULL END) as avg_value
                      ')
                      ->first();

        return [
            'period' => $startDate->format('m/Y'),
            'total_quotes' => $quotes->total_quotes ?? 0,
            'approved_quotes' => $quotes->approved_quotes ?? 0,
            'rejected_quotes' => $quotes->rejected_quotes ?? 0,
            'expired_quotes' => $quotes->expired_quotes ?? 0,
            'total_value' => $quotes->total_value ?? 0,
            'average_value' => $quotes->avg_value ?? 0,
            'approval_rate' => $quotes->total_quotes > 0 
                ? round(($quotes->approved_quotes / $quotes->total_quotes) * 100, 2) 
                : 0,
        ];
    }
}