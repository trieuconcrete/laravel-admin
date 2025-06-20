<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Số tiền</th>
                <th>Phương thức</th>
                <th>Trạng thái</th>
                <th>Thu/Chi</th>
                <th>Loại giao dịch</th>
                <th>Người tạo</th>
                <th>Chú thích</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td>@formatDate($transaction->payment_date)</td>
                <td class="{{ $transaction->type == \App\Models\Transaction::TYPE_INCOME ? 'text-success' : 'text-danger' }}">
                    {{ number_format($transaction->amount) }} VNĐ
                </td>
                <td>
                    <span class="badge bg-{{ $transaction?->payment?->payment_method_badge_class }}">
                        {{ $transaction?->payment?->method_label }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $transaction?->payment?->status_badge_class }}">
                        {{ $transaction?->payment?->status }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $transaction->type_badge_class }}">
                        {{ $transaction->type_label }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $transaction->category_badge_class }}">
                        {{ $transaction->category_label }}
                    </span>
                </td>
                <td>{{ $transaction->created_by_name }}</td>
                <td>{{ $transaction->notes ?: $transaction->description }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-transaction" 
                            data-id="{{ $transaction->id }}" 
                            data-amount="{{ $transaction->amount }}" 
                            data-payment-date="@formatDateForInput($transaction->payment_date)" 
                            data-payment-method="{{ $transaction->method }}" 
                            data-notes="{{ $transaction->notes }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#editTransactionModal">Sửa</button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-transaction" 
                            data-id="{{ $transaction->id }}" 
                            data-customer-id="{{ $customer->id }}">Xóa</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="alert alert-info d-inline-block mb-0">
                        Không có giao dịch nào được tìm thấy.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->count() > 0)
<div class="d-flex justify-content-center mt-4" id="transaction-pagination">
    {{ $transactions->appends(request()->except('page'))->links() }}
</div>
@endif
