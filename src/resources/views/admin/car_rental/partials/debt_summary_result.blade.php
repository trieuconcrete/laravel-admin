<!-- Thông tin filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h6 class="mb-2"><i class="las la-filter align-middle"></i> Thông tin lọc</h6>
            <div class="row">
                <div class="col-md-4">
                    <strong>Khách hàng:</strong> {{ $carRental->customer->name ?? 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>Loại thuê xe:</strong> 
                    @if($carRental->type == 1)
                        Thuê nguyên xe tính theo chuyến
                    @elseif($carRental->type == 2)
                        Thuê xe theo kiểu khoáng
                    @else
                        N/A
                    @endif
                </div>
                <div class="col-md-4">
                    <strong>Khoảng thời gian:</strong> 
                    @if($startDate && $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @else
                        Tất cả thời gian
                    @endif
                </div>
            </div>
            @if($notes)
            <div class="mt-2">
                <strong>Ghi chú:</strong> {{ $notes }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Tổng kết công nợ -->
<div class="row mb-4">
    <div class="col-12">
        <h6 class="text-muted mb-3">Tổng kết công nợ</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td class="fw-bold">Phí thuê xe theo tháng:</td>
                        <td class="text-end">{{ number_format($debtSummary['monthly_rental_fee']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tổng chi phí tăng ca:</td>
                        <td class="text-end">{{ number_format($debtSummary['total_overtime_cost']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tổng phí cầu đường:</td>
                        <td class="text-end">{{ number_format($debtSummary['total_toll_fees']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tổng phí đỗ xe:</td>
                        <td class="text-end">{{ number_format($debtSummary['total_parking_fees']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tổng quãng đường:</td>
                        <td class="text-end">{{ number_format($debtSummary['total_distance']) }} km</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Phí vượt quãng đường:</td>
                        <td class="text-end">{{ number_format($debtSummary['over_distance_fee']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr class="table-light">
                        <td class="fw-bold">Tổng cộng (chưa VAT):</td>
                        <td class="text-end fw-bold">{{ number_format($debtSummary['subtotal']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Thuế VAT ({{ $debtSummary['vat_rate'] }}%):</td>
                        <td class="text-end">{{ number_format($debtSummary['vat_amount']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td class="fw-bold">Tổng cộng (có VAT):</td>
                        <td class="text-end fw-bold fs-5">{{ number_format($debtSummary['total_with_vat']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Đã thanh toán:</td>
                        <td class="text-end">{{ number_format($debtSummary['paid_amount']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                    <tr class="table-danger">
                        <td class="fw-bold">Còn nợ:</td>
                        <td class="text-end fw-bold fs-5 text-danger">{{ number_format($debtSummary['remaining_debt']) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-2">
            <i class="las la-info-circle"></i> 
            Cập nhật lần cuối: {{ $debtSummary['calculation_date'] }}
        </p>
    </div>
</div>

<!-- Chi tiết từng chuyến -->
<div class="row">
    <div class="col-12">
        <h6 class="text-muted mb-3">Chi tiết từng chuyến ({{ $shipments->count() }} chuyến)</h6>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped table-sm">
                <thead class="sticky-top bg-light">
                    <tr>
                        <th>Ngày chạy</th>
                        <th>Biển số xe</th>
                        <th>Tài xế</th>
                        <th>Quãng đường</th>
                        <th>Giờ bắt đầu</th>
                        <th>Giờ kết thúc</th>
                        <th>Tăng ca</th>
                        <th>Phí cầu đường</th>
                        <th>Phí đỗ xe</th>
                        <th>Tổng chuyến</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $shipment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($shipment->run_date)->format('d/m/Y') }}</td>
                        <td>{{ $shipment->vehicle->plate_number ?? 'N/A' }}</td>
                        <td>{{ $shipment->driver->full_name ?? 'N/A' }}</td>
                        <td>{{ number_format($shipment->distance ?? 0) }} km</td>
                        <td>{{ $shipment->start_time ? \Carbon\Carbon::parse($shipment->start_time)->format('H:i') : 'N/A' }}</td>
                        <td>{{ $shipment->end_time ? \Carbon\Carbon::parse($shipment->end_time)->format('H:i') : 'N/A' }}</td>
                        <td>
                            @if($shipment->overtime_hours > 0)
                                {{ $shipment->overtime_hours }}h
                                ({{ number_format($shipment->overtime_cost) }} {{ $debtSummary['currency'] }})
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($shipment->tollFees->count() > 0)
                                {{ number_format($shipment->tollFees->sum('fee_amount')) }} {{ $debtSummary['currency'] }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($shipment->parking_fee > 0)
                                {{ number_format($shipment->parking_fee) }} {{ $debtSummary['currency'] }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="fw-bold">
                            {{ number_format(($shipment->overtime_cost ?? 0) + ($shipment->tollFees->sum('fee_amount') ?? 0) + ($shipment->parking_fee ?? 0)) }} {{ $debtSummary['currency'] }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">Chưa có chuyến nào trong khoảng thời gian này</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($shipments->count() > 0)
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="3">TỔNG CỘNG</td>
                        <td>{{ number_format($shipments->sum('distance')) }} km</td>
                        <td colspan="2"></td>
                        <td>{{ number_format($shipments->sum('overtime_cost')) }} {{ $debtSummary['currency'] }}</td>
                        <td>{{ number_format($shipments->sum(function($s) { return $s->tollFees->sum('fee_amount'); })) }} {{ $debtSummary['currency'] }}</td>
                        <td>{{ number_format($shipments->sum('parking_fee')) }} {{ $debtSummary['currency'] }}</td>
                        <td>{{ number_format($shipments->sum(function($s) { return ($s->overtime_cost ?? 0) + ($s->tollFees->sum('fee_amount') ?? 0) + ($s->parking_fee ?? 0); })) }} {{ $debtSummary['currency'] }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div> 