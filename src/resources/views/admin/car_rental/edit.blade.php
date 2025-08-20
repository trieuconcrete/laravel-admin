@extends('admin.layout')
@section('title', 'Chi tiết thuê xe')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    @if ($carRental->status == \App\Models\CarRental::STATUS_APPROVED)
                                        <a href="{{ route('admin.car-rental.download-vehicle-log', ['car_rental_id' => $carRental->id]) }}" class="btn btn-outline-primary me-2">
                                            <i class="las la-file-invoice align-middle"></i> Xuất bảng kê
                                        </a>
                                        <button type="button" id="summarizeReport" class="btn btn-secondary position-relative">
                                            <i class="las la-calculator align-middle me-1"></i> Tổng kết công nợ
                                            @if($carRental->shipmentReports && $carRental->shipmentReports->count() > 0)
                                                <span class="badge bg-success ms-1">
                                                    <i class="las la-check align-middle"></i> Đã từng tổng kết
                                                </span>
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $carRental->shipmentReports->count() }}
                                                    <span class="visually-hidden">báo cáo đã tổng kết</span>
                                                </span>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ session('active_tab') !== 'vehicle-logs' ? 'active' : '' }}" data-bs-toggle="tab" href="#rental-info" role="tab">
                                        Thông tin thuê xe
                                    </a>
                                </li>
                                @if ($carRental->status == \App\Models\CarRental::STATUS_APPROVED)
                                <li class="nav-item">
                                    <a class="nav-link {{ session('active_tab') === 'vehicle-logs' ? 'active' : '' }}" data-bs-toggle="tab" href="#vehicle-logs" role="tab">
                                        Nhật ký lộ trình xe
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- Rental Info Tab -->
                                <div class="tab-pane {{ session('active_tab') !== 'vehicle-logs' ? 'active' : '' }}" id="rental-info" role="tabpanel">
                                    <form action="{{ route('admin.car-rental.update', $carRental->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                    <div class="row mb-3">
                                            <label class="form-label fs-5">Loại thuê xe <span class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <div class="form-check form-radio-primary mb-3">
                                                    <input class="form-check-input" @disabled($carRental->shipmentReports && $carRental->shipmentReports->count() > 0) type="radio" name="type" value="1" id="type1" @checked(old('type', $carRental->type) == 1)>
                                                    <label class="form-check-label" for="type1">
                                                        Thuê nguyên xe tính theo chuyến
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-radio-primary mb-3">
                                                    <input class="form-check-input" @disabled($carRental->shipmentReports && $carRental->shipmentReports->count() > 0) type="radio" name="type" value="2" id="type2" @checked(old('type', $carRental->type) == 2)>
                                                    <label class="form-check-label" for="type2">
                                                        Thuê xe theo kiểu khoáng
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                                @if ($carRental->shipmentReports && $carRental->shipmentReports->count() > 0)
                                                    <input type="hidden" name="customer_id" value="{{ $carRental->customer_id }}">
                                                @endif
                                                <select class="form-select" name="customer_id" @disabled($carRental->shipmentReports && $carRental->shipmentReports->count() > 0)>
                                                    <option value="">Chọn khách hàng</option>
                                                    @foreach ($customers as $key => $customer)
                                                    <option value="{{ $key }}" {{ $key == $carRental->customer_id ? 'selected' : '' }}>
                                                        {{ $customer }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger error" data-field="customer_id"></div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                                <select class="form-select" name="status">
                                                    <option value="">Chọn trạng thái</option>
                                                    @foreach ($carRentalstatuses as $val => $label)
                                                    <option value="{{ $val }}" {{ $val == $carRental->status ? 'selected' : '' }}>
                                                        {{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger error" data-field="status"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Phí thuê xe theo tháng <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control number" name="monthly_rental_fee" value="{{ old('monthly_rental_fee', number_format($carRental->monthly_rental_fee)) }}">
                                                    @error('monthly_rental_fee')
                                                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Phí tăng ca/giờ</label>
                                                    <input type="text" class="form-control number" name="overtime_fee_per_hour" value="{{ old('overtime_fee_per_hour', number_format($carRental->overtime_fee_per_hour)) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control date-input" name="start_date" required value="{{ old('start_date', $carRental->start_date) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control date-input" name="end_date" value="{{ old('end_date', $carRental->end_date) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Điểm đi</label>
                                                    <input type="text" class="form-control" name="departure_point" placeholder="Nhập điểm đi" value="{{ old('departure_point', $carRental->departure_point) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Điểm đến</label>
                                                    <input type="text" class="form-control" name="destination_point" placeholder="Nhập điểm đến" value="{{ old('destination_point', $carRental->destination_point) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Số km tối đa</label>
                                                    <input type="text" class="form-control number" name="max_distance" value="{{ old('max_distance', number_format($carRental->max_distance)) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Phí theo km chạy vượt</label>
                                                    <input type="text" class="form-control number" name="over_distance_fee_per_km" value="{{ old('over_distance_fee_per_km', number_format($carRental->over_distance_fee_per_km)) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Giờ bắt đầu làm việc trong ngày</label>
                                                    <input type="time" class="form-control" name="start_working_hour" value="{{ old('start_working_hour', $carRental->start_working_hour ?? '07:30') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label class="form-label">Giờ kết thúc làm việc trong ngày</label>
                                                    <input type="time" class="form-control" name="end_working_hour" value="{{ old('end_working_hour', $carRental->end_working_hour ?? '17:00') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tên hàng hóa</label>
                                            <input class="form-control" placeholder="Nhập tên hàng hóa" name="product_name" value="{{ old('product_name', $carRental->product_name) }}"></input>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Mô tả dịch vụ</label>
                                            <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ" name="description" value="{{ old('description', $carRental->description) }}">{{ $carRental->description }}</textarea>
                                            @error('description')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label">Ghi chú</label>
                                            <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ" name="notes" value="{{ old('notes', $carRental->notes) }}">{{ $carRental->notes }}</textarea>
                                            @error('notes')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label class="form-label">Số hóa đơn</label>
                                                    <input type="text" class="form-control" name="invoice_number" value="{{ old('invoice_number', $carRental->invoice_number) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label class="form-label">Số bảng kê</label>
                                                    <input type="text" class="form-control" name="statement_number" value="{{ old('statement_number', $carRental->statement_number) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label class="form-label">Hợp đồng số</label>
                                                    <input type="text" class="form-control" name="contract_number" value="{{ old('contract_number', $carRental->contract_number) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="block text-gray-700">File báo giá</label>
                                            <input type="file" name="file" class="form-control mt-1 border p-2 rounded">
                                            @if (!empty($carRental->file))
                                            <div class="mt-2">
                                                <label class="block text-gray-600">Tệp hiện tại:</label>
                                                <a href="{{ asset('storage/uploads/car_rentals/' . $carRental->file) }}" target="_blank" class="text-blue-600 underline">
                                                    {{ $carRental->file }}
                                                </a>
                                            </div>
                                            @endif
                                        </div>

                                        <hr>

                                        <div>
                                            <button type="submit" class="btn rounded-pill btn-secondary waves-effect">Save</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Vehicle Logs Tab -->
                                <div class="tab-pane {{ session('active_tab') === 'vehicle-logs' ? 'active' : '' }}" id="vehicle-logs" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title mb-0">Danh sách Nhật ký lộ trình xe</h5>
                                        <div>
                                            {{-- <a href="{{ route('admin.car-rental.download-vehicle-log', ['car_rental_id' => $carRental->id]) }}" class="btn btn-success me-2">
                                                <i class="ri-download-2-line align-bottom me-1"></i> Download nhật ký
                                            </a> --}}
                                            <a  href="{{ route('admin.car-rental.shipment-create', parameters: $carRental->id) }}" class="btn btn-primary">
                                                <i class="ri-add-line align-bottom me-1"></i> Thêm nhật ký
                                            </a>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Xe</th>
                                                    <th class="text-center">Ngày chạy</th>
                                                    <th class="text-center">Giờ làm việc</th>
                                                    <th class="text-center">Lịch trình</th>
                                                    <th>Thời gian tăng ca</th>
                                                    <th>Đơn giá tăng ca</th>
                                                    <th>Chi phí tăng ca</th>
                                                    <th>Km bắt đầu</th>
                                                    <th>Km kết thúc</th>
                                                    <th>Tổng km</th>
                                                    <th>Phí cầu đường</th>
                                                    <th>Phí đậu xe</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalOvertimeCost = 0;
                                                    $totalOvertimeHours = 0;
                                                    $totalTollFees = 0;
                                                    $totalParkingFees = 0;
                                                @endphp
                                                @if (!$carRentalVehicleLogs->isEmpty())
                                                    @foreach($carRentalVehicleLogs as $shipment)
                                                    @php
                                                        $totalOvertimeCost += $shipment->total_overtime_cost ?? 0;
                                                        $totalOvertimeHours += $shipment->overtime_hours ?? 0;
                                                        
                                                        // Tính tổng phí cầu đường
                                                        if (isset($shipment->tollFees) && $shipment->tollFees->count() > 0) {
                                                            $totalTollFees += $shipment->tollFees->sum('fee_amount');
                                                        }
                                                        
                                                        // Tính tổng phí đậu xe
                                                        $totalParkingFees += $shipment->parking_fee ?? 0;
                                                    @endphp
                                                    
                                                    <tr>
                                                        <td>{{ $shipment->vehicle->vehicleType->name ?? 'N/A' }} - {{ $shipment->vehicle->plate_number ?? 'N/A' }}</td>
                                                        <td class="text-center">{{ $shipment->run_date ? \Carbon\Carbon::parse($shipment->run_date)->format('Y-m-d') : '' }}</td>
                                                        <td class="text-center">{{ $shipment->start_time ? \Carbon\Carbon::createFromFormat('H:i:s', $shipment->start_time)->format('H:i') : '' }} - {{ $shipment->end_time ? \Carbon\Carbon::createFromFormat('H:i:s', $shipment->end_time)->format('H:i') : '' }}</td>
                                                        <td class="text-center">{{ $shipment->origin }} -> {{ $shipment->destination }}</td>
                                                        <td>{{ number_format($shipment->overtime_hours, 1) }} giờ</td>
                                                        <td>{{ number_format($shipment->overtime_rate) }}</td>
                                                        <td>{{ number_format($shipment->total_overtime_cost) }}</td>
                                                        <td>{{ number_format($shipment->start_odometer) }}</td>
                                                        <td>{{ number_format($shipment->end_odometer) }}</td>
                                                        <td>{{ number_format($shipment->actual_distance) }}</td>
                                                        <td>
                                                            @if(isset($shipment->tollFees) && $shipment->tollFees->count() > 0)
                                                                <span class="badge bg-info">{{ number_format($shipment->total_toll_fee) }}</span>
                                                                <small class="d-block text-muted">{{ $shipment->tollFees->count() }} trạm</small>
                                                            @else
                                                                <span class="text-muted">0</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($shipment->parking_fee) }}</td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <a href="{{ route('admin.car-rental.edit-vehicle-log', $shipment->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="ri-edit-line"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteVehicleLog({{ $shipment->id }})">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="13" class="text-center">Chưa có nhật ký lộ trình xe</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
            </div>

            {{--  <!-- Thông tin báo cáo đã tổng kết -->
            @if($carRental->shipmentReports && $carRental->shipmentReports->count() > 0)
                @php
                    $latestReport = $carRental->shipmentReports->sortByDesc('created_at')->first();
                @endphp
                <div class="card mb-4">
                    <div class="card-header bg-info bg-opacity-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-info">
                                <i class="las la-file-alt me-2"></i>
                                Báo cáo tổng kết công nợ gần nhất
                            </h5>
                            <span class="badge bg-success">
                                <i class="las la-check me-1"></i>
                                Đã tổng kết
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width: 40%;">Kỳ báo cáo:</td>
                                            <td>{{ $latestReport->monthly }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Từ ngày:</td>
                                            <td>{{ $latestReport->statement_start_date ? \Carbon\Carbon::parse($latestReport->statement_start_date)->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Đến ngày:</td>
                                            <td>{{ $latestReport->statement_end_date ? \Carbon\Carbon::parse($latestReport->statement_end_date)->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Loại báo cáo:</td>
                                            <td>
                                                @if($latestReport->shipment_type == 21)
                                                    <span class="badge bg-primary">Thuê nguyên xe tính theo chuyến</span>
                                                @elseif($latestReport->shipment_type == 22)
                                                    <span class="badge bg-warning">Thuê xe theo kiểu khoáng</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $latestReport->shipment_type }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold" style="width: 40%;">Tổng tiền:</td>
                                            <td class="text-success fw-bold fs-5">{{ number_format($latestReport->total_amount, 0, ',', '.') }} VNĐ</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Ngày tạo:</td>
                                            <td>{{ $latestReport->created_at ? \Carbon\Carbon::parse($latestReport->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Ngày cập nhật:</td>
                                            <td>{{ $latestReport->updated_at ? \Carbon\Carbon::parse($latestReport->updated_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Trạng thái:</td>
                                            <td>
                                                @if($latestReport->is_finalized)
                                                    <span class="badge bg-success">Đã hoàn thành</span>
                                                @else
                                                    <span class="badge bg-warning">Đang xử lý</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="las la-info-circle me-1"></i>
                                    Báo cáo này đã được lưu vào hệ thống và có thể xuất Excel bất cứ lúc nào
                                </small>
                                <a href="{{ route('admin.car-rental.export-summary', ['carRental' => $carRental->id, 'start_date' => $latestReport->statement_start_date, 'end_date' => $latestReport->statement_end_date]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="las la-download me-1"></i>
                                    Xuất Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif  --}}

            <!-- Chi tiết phí thuê xe -->
            <div class="card mb-4">
                <div class="card-header bg-primary bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">
                            <i class="ri-calculator-line me-2"></i>
                            Chi tiết phí thuê xe
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table-borderless table-sm" style="margin-bottom: 0;">
                            <tbody>
                                <tr class="py-1">
                                    <td class="text-start py-1" style="width: 60%; padding: 0.25rem 0.5rem;">
                                        - Phí thuê xe tháng:
                                        <span class="fw-bold text-primary">{{ number_format($carRental->monthly_rental_fee ?? 0, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                </tr>
                                
                                <tr class="py-1">
                                    <td class="text-start py-1" style="padding: 0.25rem 0.5rem;">
                                        - Phát sinh phí tăng ca ({{ number_format($totalOvertimeHours, 2) }} giờ x 50.000 VND):
                                        <span class="fw-bold text-warning">{{ number_format($totalOvertimeCost, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                </tr>
                                <tr class="py-1">
                                    <td class="text-start py-1" style="padding: 0.25rem 0.5rem;">
                                        - Phát sinh phụ phí cầu đường:
                                        <span class="fw-bold text-info">{{ number_format($totalTollFees, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                </tr>
                                <tr class="py-1">
                                    <td class="text-start py-1" style="padding: 0.25rem 0.5rem;">
                                        - Phí bãi xe:
                                        <span class="fw-bold text-secondary">{{ number_format($totalParkingFees, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                </tr>

                                <tr class="py-1">
                                    <td class="text-start py-1" style="padding: 0.25rem 0.5rem;">
                                        - Phát sinh phí vượt giới hạn km:
                                        <span class="fw-bold text-info">{{ number_format($carRental->over_distance_fee, 0, ',', '.') }} VNĐ</span>
                                        @if($carRental->over_distance_fee > 0)
                                            <br><small class="text-muted">
                                                ({{ number_format($carRental->total_distance, 0, ',', '.') }} km - {{ number_format($carRental->max_distance, 0, ',', '.') }} km) × {{ number_format($carRental->over_distance_fee_per_km_unit, 0, ',', '.') }} VNĐ/km
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                
                                <tr class="border-top py-1">
                                    <td class="text-start fw-bold py-1" style="padding: 0.25rem 0.5rem;">
                                        <i class="ri-calculator-line text-dark me-2"></i>Tổng cộng (chưa thuế VAT):
                                        <span class="fw-bold text-danger">{{ number_format(($carRental->monthly_rental_fee ?? 0) + $totalOvertimeCost + $totalTollFees + $totalParkingFees + ($carRental->over_distance_fee ?? 0), 0, ',', '.') }} VNĐ</span>
                                    </td>
                                </tr>
                                
                                @php
                                    $subtotal = ($carRental->monthly_rental_fee ?? 0) + $totalOvertimeCost + $totalTollFees + $totalParkingFees + ($carRental->over_distance_fee ?? 0);
                                    $vatAmount = $subtotal * 0.08;
                                    $totalWithVat = $subtotal + $vatAmount;
                                @endphp
                                
                                <tr class="py-1">
                                    <td class="text-start py-1" style="padding: 0.25rem 0.5rem;">
                                        <i class="ri-percent-line text-muted me-2"></i>Thuế VAT 8%:
                                        <span class="fw-bold text-muted">{{ number_format($vatAmount, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                </tr>
                                
                                <tr class="border-top py-1">
                                    <td class="text-start fw-bold fs-5 py-1" style="padding: 0.25rem 0.5rem;">
                                        <i class="ri-money-dollar-circle-line text-success me-2"></i>Tổng cộng bao gồm thuế VAT:
                                        <span class="fw-bold text-success">{{ number_format($totalWithVat, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Tổng kết công nợ -->
<div class="modal fade" id="summarizeReportModal" tabindex="-1" aria-labelledby="summarizeReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summarizeReportModalLabel">Xác nhận tổng kết công nợ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="summarizeReportForm">
                    <div class="mb-3">
                        <label for="reportStartDate" class="form-label">Từ ngày</label>
                        <input type="text" class="form-control" readonly id="reportStartDate" name="start_date" 
                            value="{{ $carRental->start_date ? date('Y-m-d', strtotime($carRental->start_date)) : '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="reportEndDate" class="form-label">Đến ngày</label>
                        <input type="text" class="form-control" readonly id="reportEndDate" name="end_date" 
                            value="{{ $carRental->end_date ? date('Y-m-d', strtotime($carRental->end_date)) : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Loại tổng kết</label>
                        <input type="text" class="form-control" readonly 
                            value="{{ $carRental->type == 1 ? 'Thuê nguyên xe tính theo chuyến' : 'Thuê xe theo kiểu khoáng' }}">
                    </div>
                    <input type="hidden" id="carRentalId" value="{{ $carRental->id }}">
                    <input type="hidden" id="carRentalType" value="{{ $carRental->type }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="submitSummarize">Tổng kết</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    // Handle vehicle selection to show/hide driver selection (Issue #180)
    $('#add_vehicle_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const isRental = selectedOption.data('is-rental') == 1;
        
        if (isRental) {
            // Xe HPL thuê - ẩn select box tài xế
            $('#driver_selection_row').hide();
            $('#add_driver_id').prop('required', false);
            $('.driver-required').hide();
        } else {
            // Xe thường - hiện select box tài xế
            $('#driver_selection_row').show();
            $('#add_driver_id').prop('required', true);
            $('.driver-required').show();
        }
    });

    // Trigger change event on page load
    $('#add_vehicle_id').trigger('change');





    // Initialize datetime pickers
    $('input[type="datetime-local"]').flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });

    // Format number inputs for vehicle log form
    $('input[name="start_odometer"], input[name="end_odometer"], input[name="parking_fee"], .toll-fee-amount, input[name="monthly_rental_fee"]').on('input', function () {
        let value = $(this).val();
        // Remove all non-numeric characters except dots
        value = value.replace(/[^0-9.]/g, '');
        // Giới hạn tối đa 9 chữ số (không tính dấu chấm thập phân)
        let parts = value.split('.');
        if (parts[0].length > 9) {
            parts[0] = parts[0].substring(0, 9);
            value = parts.join('.');
        }
        // Handle decimal formatting
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
        $(this).val(integerPart + decimalPart);
    });

    // Format number inputs for edit form (only numeric fields)
    $('input[id="edit_start_odometer"], input[id="edit_end_odometer"], input[id="edit_parking_fee"], .edit-toll-fee-amount').on('input', function () {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');
        // Giới hạn tối đa 9 chữ số (không tính dấu chấm thập phân)
        let parts = value.split('.');
        if (parts[0].length > 9) {
            parts[0] = parts[0].substring(0, 9);
            value = parts.join('.');
        }
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
        $(this).val(integerPart + decimalPart);
    });

    // Format number inputs on change for edit form
    $('input[id="edit_start_odometer"], input[id="edit_end_odometer"], input[id="edit_parking_fee"], .edit-toll-fee-amount').on('change', function () {
        let value = $(this).val();
        if (value) {
            // Remove all non-numeric characters except dots
            value = value.replace(/[^0-9.]/g, '');
            // Giới hạn tối đa 9 chữ số (không tính dấu chấm thập phân)
            let parts = value.split('.');
            if (parts[0].length > 9) {
                parts[0] = parts[0].substring(0, 9);
                value = parts.join('.');
            }
            if (value && !isNaN(value)) {
                let numericValue = parseFloat(value);
                let formatted = numericValue.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
                $(this).val(formatted);
            }
        }
    });

    // Initialize formatting for edit form inputs when modal is shown
    $('#editCarRentalVehicleLogModal').on('shown.bs.modal', function() {
        // Format only numeric inputs in edit form
        $('input[id="edit_start_odometer"], input[id="edit_end_odometer"], input[id="edit_parking_fee"], .edit-toll-fee-amount').each(function() {
            let value = $(this).val();
            if (value && !isNaN(value.replace(/,/g, ''))) {
                let numericValue = parseFloat(value.replace(/,/g, ''));
                let formatted = numericValue.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
                $(this).val(formatted);
            }
        });
    });

    // Tự động chuyển tab dựa trên active_tab từ session
    @if(session('active_tab') === 'vehicle-logs')
        // Nếu có active_tab = 'vehicle-logs', chuyển đến tab đó
        $(document).ready(function() {
            // Chờ DOM load xong
            setTimeout(function() {
                // Tìm tab "Nhật ký lộ trình xe" và click vào nó
                const vehicleLogsTab = $('a[href="#vehicle-logs"]');
                if (vehicleLogsTab.length > 0) {
                    vehicleLogsTab.tab('show');
                }
            }, 100);
        });
    @endif
});

// Function to edit vehicle log (updated for shipment-based approach)
window.editVehicleLog = function(shipmentId) {
    // Show loading
    Swal.fire({
        title: 'Đang tải dữ liệu...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Fetch log data using shipment ID
    $.ajax({
        url: `/admin/car-rental/shipment/${shipmentId}/edit-vehicle-log`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: (response) => {
            Swal.close();
            
            // Populate form fields
            $('#edit_log_id').val(response.log.id);
            $('#edit_vehicle_id').val(response.log.vehicle_id);
            $('#edit_driver_id').val(response.log.driver_id || ''); // Load driver_id
            
            // Trigger vehicle change event to handle driver selection visibility
            $('#edit_vehicle_id').trigger('change');
            
            // Convert date format for Flatpickr input
            let startTime = response.log.start_time;
            let endTime = response.log.end_time;
            if (startTime.includes('T')) startTime = startTime.replace('T', ' ');
            if (endTime.includes('T')) endTime = endTime.replace('T', ' ');
            
            $('#edit_start_time').val(startTime);
            $('#edit_end_time').val(endTime);
            $('#edit_run_date').val(response.log.run_date ? String(response.log.run_date).substring(0, 10) : '');
            
            // Nếu Flatpickr đã khởi tạo, update lại giá trị
            if ($('#edit_start_time').hasClass('flatpickr-input')) {
                $('#edit_start_time')[0]._flatpickr.setDate(startTime, true, "Y-m-d H:i");
            }
            if ($('#edit_end_time').hasClass('flatpickr-input')) {
                $('#edit_end_time')[0]._flatpickr.setDate(endTime, true, "Y-m-d H:i");
            }
            $('#edit_start_odometer').val(response.log.start_odometer);
            $('#edit_end_odometer').val(response.log.end_odometer);
            $('#edit_parking_fee').val(response.log.parking_fee);
            $('#edit_notes').val(response.log.notes);
            $('#edit_start_location').val(response.log.start_location ? String(response.log.start_location) : '');
            $('#edit_end_location').val(response.log.end_location ? String(response.log.end_location) : '');

            // Clear and populate toll fees
            $('#editTollFeesTable tbody').empty();
            if (response.log.toll_fees && response.log.toll_fees.length > 0) {
                response.log.toll_fees.forEach(function(tollFee) {
                    addEditTollFeeRow(tollFee);
                });
            } else {
                addEditTollFeeRow(); // Add empty row
            }

            // Set form action
            $('#editCarRentalVehicleLogForm').attr('action', `/admin/car-rental/shipment/${response.log.id}/update-vehicle-log`);
            
            // Show modal
            $('#editCarRentalVehicleLogModal').modal('show');
        },
        error: (xhr) => {
            Swal.close();
            let message = 'Có lỗi xảy ra khi tải dữ liệu.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                title: "Lỗi!",
                text: message,
                icon: "error"
            });
        }
    });
};

// Function to format number for display
function formatNumber(number) {
    if (number === null || number === undefined || number === '') return '';
    return parseFloat(number).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

// Edit Vehicle Log Form AJAX Submission
$('#editCarRentalVehicleLogForm').on('submit', function(e) {
    e.preventDefault();
    
    // Chuẩn hóa start_time, end_time về H:i
    let startTimeInput = $('[name="start_time"]', this);
    let endTimeInput = $('[name="end_time"]', this);
    let startTime = startTimeInput.val();
    let endTime = endTimeInput.val();
    if (startTime && startTime.length > 5) startTimeInput.val(startTime.substring(0,5));
    if (endTime && endTime.length > 5) endTimeInput.val(endTime.substring(0,5));
    
    // Loại bỏ dấu phẩy trong các input fee_amount (toll_fees)
    $(this).find('input[name^="toll_fees"][name$="[fee_amount]"]').each(function() {
        let val = $(this).val();
        if (val) $(this).val(val.replace(/,/g, ''));
    });
    // Clear previous error messages
    $('.text-danger').remove();
    
    // Validate required fields
    let hasErrors = false;
    
    // Check vehicle and driver selection (Issue #180)
    const selectedVehicle = $('#edit_vehicle_id option:selected');
    const isRental = selectedVehicle.data('is-rental') == 1;
    const driverId = $('#edit_driver_id').val();
    
    if (!isRental && !driverId) {
        showFieldError($('#edit_driver_id'), 'Vui lòng chọn tài xế cho xe này');
        hasErrors = true;
    }
    
    // Check start_time and end_time
    startTime = $('[name="start_time"]', this).val();
    endTime = $('[name="end_time"]', this).val();
    
    if (!startTime) {
        showFieldError($('[name="start_time"]', this), 'Thời gian bắt đầu là bắt buộc');
        hasErrors = true;
    }
    
    if (!endTime) {
        showFieldError($('[name="end_time"]', this), 'Thời gian kết thúc là bắt buộc');
        hasErrors = true;
    }
    
    // Check toll fees required fields
    $('#editTollFeesTable tbody tr').each(function(index) {
        const stationName = $(this).find('[name*="[station_name]"]').val();
        const transactionCode = $(this).find('[name*="[transaction_code]"]').val();
        const feeAmount = $(this).find('[name*="[fee_amount]"]').val();
        
        if (!stationName) {
            showFieldError($(this).find('[name*="[station_name]"]'), 'Tên trạm là bắt buộc');
            hasErrors = true;
        }
        
        if (!transactionCode) {
            showFieldError($(this).find('[name*="[transaction_code]"]'), 'Mã giao dịch là bắt buộc');
            hasErrors = true;
        }
        
        if (!feeAmount) {
            showFieldError($(this).find('[name*="[fee_amount]"]'), 'Số tiền là bắt buộc');
            hasErrors = true;
        }
    });
    
    if (hasErrors) {
        Swal.fire({
            title: "Lỗi!",
            text: "Vui lòng điền đầy đủ thông tin bắt buộc",
            icon: "error"
        });
        return;
    }
    
    // Get form data
    const formData = new FormData(this);
    
    // Basic validation
    const startOdo = parseFloat($('[name="start_odometer"]', this).val().replace(/,/g, ''));
    const endOdo = parseFloat($('[name="end_odometer"]', this).val().replace(/,/g, ''));

    if (new Date(endTime) <= new Date(startTime)) {
        Swal.fire({
            title: "Lỗi!",
            text: "Thời gian kết thúc phải sau thời gian bắt đầu",
            icon: "error"
        });
        return;
    }

    if (endOdo <= startOdo) {
        Swal.fire({
            title: "Lỗi!",
            text: "Km kết thúc phải lớn hơn Km bắt đầu",
            icon: "error"
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Đang cập nhật...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit via AJAX
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: (response) => {
            if (response.success) {
                // Close modal
                $('#editCarRentalVehicleLogModal').modal('hide');
                
                // Reset form
                this.reset();

                // Show success message
                Swal.fire({
                    title: "Thành công!",
                    text: response.message,
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Reload page to show updated log
                    location.reload();
                });
            }
        },
        error: (xhr) => {
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(field => {
                    const input = $(`[name="${field}"]`, this);
                    if (input.length) {
                        const errorDiv = $('<div>')
                            .addClass('text-danger mt-1')
                            .text(errors[field][0]);
                        input.parent().append(errorDiv);
                    }
                });

                Swal.fire({
                    title: "Lỗi!",
                    text: "Vui lòng kiểm tra lại thông tin nhập liệu",
                    icon: "error"
                });
            } else {
                // Other errors
                Swal.fire({
                    title: "Lỗi!",
                    text: xhr.responseJSON?.message || "Có lỗi xảy ra, vui lòng thử lại",
                    icon: "error"
                });
            }
        }
    });
});

// Function to delete vehicle log (updated for shipment-based approach)
window.deleteVehicleLog = function(shipmentId) {
    Swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc chắn muốn xóa nhật ký xe này?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Đang xóa...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Delete via AJAX using shipment ID
            $.ajax({
                url: `/admin/car-rental/shipment/${shipmentId}/destroy-vehicle-log`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (response.success) {
                        Swal.fire({
                            title: "Thành công!",
                            text: response.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Reload page to show updated list
                            location.reload();
                        });
                    }
                },
                error: (xhr) => {
                    let message = 'Có lỗi xảy ra khi xóa nhật ký xe.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: "Lỗi!",
                        text: message,
                        icon: "error"
                    });
                }
            });
        }
    });
};

// Toll Fee Management Functions
let tollFeeRowIndex = 0;
let editTollFeeRowIndex = 0;

// Add toll fee row to create form
function addTollFeeRow() {
    const tbody = $('#tollFeesTable tbody');
    const row = `
        <tr data-index="${tollFeeRowIndex}">
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${tollFeeRowIndex}][station_name]" placeholder="Tên trạm" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${tollFeeRowIndex}][transaction_code]" placeholder="Mã giao dịch" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm toll-fee-amount" name="toll_fees[${tollFeeRowIndex}][fee_amount]" placeholder="Số tiền" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${tollFeeRowIndex}][notes]" placeholder="Ghi chú">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTollFeeRow(${tollFeeRowIndex})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.append(row);
    tollFeeRowIndex++;
    
    // Initialize number formatting for new row
    initializeTollFeeFormatting();
}

// Add toll fee row to edit form
function addEditTollFeeRow(tollFee = null) {
    const tbody = $('#editTollFeesTable tbody');
    const row = `
        <tr data-index="${editTollFeeRowIndex}">
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${editTollFeeRowIndex}][station_name]" placeholder="Tên trạm" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${editTollFeeRowIndex}][transaction_code]" placeholder="Mã giao dịch" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm edit-toll-fee-amount" name="toll_fees[${editTollFeeRowIndex}][fee_amount]" placeholder="Số tiền" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm" name="toll_fees[${editTollFeeRowIndex}][notes]" placeholder="Ghi chú">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEditTollFeeRow(${editTollFeeRowIndex})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        </tr>
    `;
    tbody.append(row);
    editTollFeeRowIndex++;
    
    // Initialize number formatting for new row
    initializeEditTollFeeFormatting();

    if (tollFee) {
        $('input[name="toll_fees[' + (editTollFeeRowIndex - 1) + '][station_name]"]').val(tollFee.station_name);
        $('input[name="toll_fees[' + (editTollFeeRowIndex - 1) + '][transaction_code]"]').val(tollFee.transaction_code || '');
        $('input[name="toll_fees[' + (editTollFeeRowIndex - 1) + '][fee_amount]"]').val(formatNumber(tollFee.fee_amount));
        $('input[name="toll_fees[' + (editTollFeeRowIndex - 1) + '][notes]"]').val(tollFee.notes || '');
    }
}

// Remove toll fee row from create form
function removeTollFeeRow(index) {
    $(`#tollFeesTable tbody tr[data-index="${index}"]`).remove();
}



// Initialize number formatting for toll fee amounts in create form
function initializeTollFeeFormatting() {
    $('.toll-fee-amount').off('input').on('input', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');
        
        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
        
        $(this).val(integerPart + decimalPart);
    });
}





// Function to show field error
function showFieldError(selector, message) {
    const input = $(selector);
    const errorDiv = $('<div>')
        .addClass('text-danger mt-1')
        .text(message);
    input.parent().append(errorDiv);
    input.addClass('is-invalid');
}

// Remove error styling when user starts typing
$(document).on('input', 'input, textarea', function() {
    $(this).removeClass('is-invalid');
    $(this).parent().find('.text-danger').remove();
});

// Format number inputs for new fields
$(document).ready(function() {
    // Format max_distance input
    $('input[name="max_distance"]').on('input', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');
        
        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
        
        $(this).val(integerPart + decimalPart);
    });

    // Format over_distance_fee_per_km input
    $('input[name="over_distance_fee_per_km"]').on('input', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');
        
        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
        
        $(this).val(integerPart + decimalPart);
    });

    // Format overtime_fee_per_hour input
    $('input[name="overtime_fee_per_hour"]').on('input', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');
        
        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
        
        $(this).val(integerPart + decimalPart);
    });
});

// Tổng kết công nợ
$(document).ready(function() {
    // Mở modal tổng kết công nợ
    $("#summarizeReport").click(function() {
        $("#summarizeReportModal").modal("show");
    });

    // Xử lý khi nhấn nút tổng kết
    $("#submitSummarize").click(function() {
        const startDate = $("#reportStartDate").val();
        const endDate = $("#reportEndDate").val();
        const carRentalId = $("#carRentalId").val();
        const carRentalType = $("#carRentalType").val();

        if (!startDate || !endDate) {
            Swal.fire({
                title: "Lỗi",
                text: "Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc",
                icon: "error"
            });
            return;
        }

        // Hiển thị loading
        Swal.fire({
            title: "Đang xử lý...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Gọi API tổng kết công nợ
        $.ajax({
            url: `/admin/car-rental/${carRentalId}/summarize-report`,
            method: "POST",
            data: {
                start_date: startDate,
                end_date: endDate,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    // Đóng modal tổng kết
                    $("#summarizeReportModal").modal("hide");
                    
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        title: "Thành công!",
                        text: "Đã tổng kết công nợ thành công",
                        icon: "success",
                        confirmButtonText: "Đóng"
                    }).then(() => {
                        // Reload trang để hiển thị trạng thái mới
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Lỗi",
                        text: response.message || "Đã xảy ra lỗi khi tổng kết công nợ",
                        icon: "error"
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                
                let errorMessage = "Đã xảy ra lỗi khi tổng kết công nợ";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: "Lỗi",
                    text: errorMessage,
                    icon: "error"
                });
            }
        });
    });
});

</script>
@endpush
