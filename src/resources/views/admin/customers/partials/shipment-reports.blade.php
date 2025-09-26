<div class="table-responsive">
    <table class="table table-hover">
        <thead>
           <thead class="table-light text-uppercase">
                <tr>
                    <th>Thao tác</th>
                    <th>Tháng</th>
                    <th>Thành tiền(VAT)</th>
                    <th>Loại</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
        </thead>
        <tbody>
            @foreach ($shipmentReports as $shipmentReport)
                <tr data-debt="{{ $shipmentReport->id }}"
                    data-amount="{{ $shipmentReport->total_amount }}"
                    data-notes="{{ $shipmentReport->getShipmentTypeLabel() }}">
                    <td>
                        <div class="btn-group">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#transactionModal" class="btn btn-sm btn-outline-primary">Thanh toán</a>
                        </div>
                    </td>
                    <td>{{ format_date($shipmentReport->monthly, 'm/Y') }}</td>
                    <td>{{ number_format($shipmentReport->total_amount) }}</td>
                    <td>{{ $shipmentReport->getShipmentTypeLabel() }}</td>
                    <td>{{ format_date($shipmentReport->statement_start_date, 'd/m/Y') }}</td>
                    <td>{{ format_date($shipmentReport->statement_end_date, 'd/m/Y') }}</td>
                    <td>{{ format_date($shipmentReport->created_at, 'd/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($shipmentReports instanceof \Illuminate\Pagination\LengthAwarePaginator && $shipmentReports->count() > 0)
    {{ $shipmentReports->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
@endif
