<div class="table-responsive">
    <table class="table table-hover">
        <thead>
           <thead class="table-light text-uppercase">
                <tr>
                    <th>Thao tác</th>
                    <th>Trạng thái</th>
                    <th>Thành tiền(VAT)</th>
                    <th>Loại</th>
                    <th>Tổng kết công nợ</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Ngày tạo</th>
                    <th>File báo giá</th>
                </tr>
            </thead>
        </thead>
        <tbody>
            @foreach ($carRentals as $carRental)
                <tr>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.car-rental.edit', $carRental) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank">Chi tiết</a>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger delete-car-rental-btn"
                                data-car-rental-id="{{ $carRental->id }}">
                                Xóa
                            </button>

                            <form action="{{ route('admin.car-rental.destroy', $carRental) }}"
                                method="POST" class="delete-quote-form"
                                id="delete-form-{{ $carRental->id }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                    <td><span class="badge bg-{{ $carRental->getStatusColorAttribute() }}">{{ $carRental->getStatusLabelAttribute() }}</span></td>
                    <td>{{ number_format($carRental->total_amount_with_vat) }}</td>
                    <td>{{ $carRental->getTypeLabelAttribute() }}</td>
                    <td>
                        @if($carRental->shipmentReports && $carRental->shipmentReports->count() > 0)
                            <span class="badge bg-success">
                                <i class="las la-check me-1"></i>
                                Đã tổng kết
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="las la-clock me-1"></i>
                                Chưa tổng kết
                            </span>
                        @endif
                    </td>
                    <td>{{ format_date($carRental->start_date, 'd/m/Y') }}</td>
                    <td>{{ format_date($carRental->end_date, 'd/m/Y') }}</td>
                    <td>{{ format_date($carRental->created_at, 'd/m/Y') }}</td>
                    <td>
                        @if ($carRental->file)
                            <a href="{{ $carRental->file }}" class="" target="_blank">Tải báo giá</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($carRentals instanceof \Illuminate\Pagination\LengthAwarePaginator && $carRentals->count() > 0)
    {{ $carRentals->appends(request()->except('page'))->links() }}
@endif
