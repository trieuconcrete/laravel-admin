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
                <tr>
                    <td>
                        <div class="btn-group">
                            <a href="#"
                                class="btn btn-sm btn-outline-primary" target="_blank">Chi tiết</a>
                        </div>
                    </td>
                    <td>{{ $shipmentReport->monthly }}</td>
                    <td>{{ number_format($shipmentReport->total_amount) }}</td>
                    <td>{{ $shipmentReport->getShipmentTypeLabel() }}</td>
                    <td>@formatDate($shipmentReport->statement_start_date)</td>
                    <td>@formatDate($shipmentReport->statement_end_date)</td>
                    <td>@formatDate($shipmentReport->created_at)</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->count() > 0)
<div class="d-flex justify-content-center mt-4" id="transaction-pagination">
    {{ $transactions->appends(request()->except('page'))->links() }}
</div>
@endif
