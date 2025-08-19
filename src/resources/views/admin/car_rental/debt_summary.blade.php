@extends('admin.layouts.app')

@section('title', 'Tổng kết công nợ - Thuê xe')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Tổng kết công nợ</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.car-rental.index') }}">Danh sách thuê xe</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.car-rental.edit', $carRental->id) }}">Chi tiết thuê xe</a></li>
                            <li class="breadcrumb-item active">Tổng kết công nợ</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Thông tin thuê xe #{{ $carRental->id }}</h4>
                            <div>
                                <a href="{{ route('admin.car-rental.export-debt-summary', $carRental->id) }}" class="btn btn-success me-2">
                                    <i class="las la-file-excel align-middle"></i> Xuất Excel
                                </a>
                                <a href="{{ route('admin.car-rental.edit', $carRental->id) }}" class="btn btn-secondary">
                                    <i class="las la-arrow-left align-middle"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Thông tin cơ bản -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Thông tin khách hàng</h6>
                                <p><strong>Khách hàng:</strong> {{ $carRental->customer->name ?? 'N/A' }}</p>
                                <p><strong>Loại thuê xe:</strong> 
                                    @if($carRental->type == 1)
                                        Thuê nguyên xe tính theo chuyến
                                    @elseif($carRental->type == 2)
                                        Thuê xe theo kiểu khoáng
                                    @else
                                        N/A
                                    @endif
                                </p>
                                <p><strong>Trạng thái:</strong> 
                                    @if($carRental->status == 1)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @elseif($carRental->status == 2)
                                        <span class="badge bg-warning">Tạm dừng</span>
                                    @elseif($carRental->status == 3)
                                        <span class="badge bg-danger">Kết thúc</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Thông tin thời gian</h6>
                                <p><strong>Ngày bắt đầu:</strong> {{ $carRental->start_date ? \Carbon\Carbon::parse($carRental->start_date)->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Ngày kết thúc:</strong> {{ $carRental->end_date ? \Carbon\Carbon::parse($carRental->end_date)->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Giờ làm việc kết thúc:</strong> {{ $carRental->end_working_hour ? \Carbon\Carbon::parse($carRental->end_working_hour)->format('H:i') : '17:30' }}</p>
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
                                <h6 class="text-muted mb-3">Chi tiết từng chuyến</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
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
                                                <td colspan="10" class="text-center text-muted">Chưa có chuyến nào</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .table td {
        vertical-align: middle;
    }
    .text-end {
        text-align: right;
    }
</style>
@endpush 