@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Chi tiết thuê xe</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#rental-info" role="tab">
                                        Thông tin thuê xe
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#vehicle-logs" role="tab">
                                        Nhật ký lộ trình xe
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- Rental Info Tab -->
                                <div class="tab-pane active" id="rental-info" role="tabpanel">
                            <form action="{{ route('admin.car-rental.update', $carRental->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <script>
                                $(document).ready(function() {
                                    $('form[action*="car-rental.update"]').on('submit', function(e) {
                                        // Loại bỏ dấu phẩy trong monthly_rental_fee trước khi submit
                                        let monthlyRentalFeeInput = $('input[name="monthly_rental_fee"]', this);
                                        let monthlyRentalFeeValue = monthlyRentalFeeInput.val();
                                        if (monthlyRentalFeeValue) {
                                            monthlyRentalFeeInput.val(monthlyRentalFeeValue.replace(/,/g, ''));
                                        }
                                    });
                                });
                                </script>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                                        <select class="form-select" name="customer_id">
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


                                <div class="mb-4">
                                    <label class="form-label">Phí thuê xe theo tháng</label>
                                    <input type="text" class="form-control" name="monthly_rental_fee" value="{{ old('monthly_rental_fee', number_format($carRental->monthly_rental_fee)) }}">
                                    @error('monthly_rental_fee')
                                    <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                    @enderror
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

                                <div class="mb-4">
                                    <label class="block text-gray-700">File thuê xe</label>
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
{{--  
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Danh sách xe thuê</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addGoodBtn">
                                            <i class="fas fa-plus me-1"></i>Thêm phương tiện
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-fixed" id="vehiclesTable">
                                            <thead>
                                                <tr>
                                                    <th width="200">Phương tiện <span class="text-danger">*</span>
                                                    </th>
                                                    <th width="250">Tên hàng</th>
                                                    <th width="120">Đơn vị</th>
                                                    <th width="100">Số lượng</th>
                                                    <th width="100">Đơn giá</th>
                                                    <th width="100">Thành tiền</th>
                                                    <th width="150">Ngày bắt đầu</th>
                                                    <th width="150">Ngày kết thúc</th>
                                                    <th width="100">Ghi chú</th>
                                                    <th width="100"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($carRentalVehicles) > 0)
                                                @foreach ($carRentalVehicles as $i => $vehicle)
                                                <tr>
                                                    <td>
                                                        <select class="form-select" name="vehicles[{{ $i }}][vehicle_id]" required>
                                                            <option value="">Chọn phương tiện</option>
                                                            @foreach ($vehicleTypes as $id => $name)
                                                            <option value="{{ $id }}" {{ $id == $vehicle->vehicle_id ? "selected" : "" }}>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('vehicle_ids')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][product_name]" class="form-control" value="{{ old('vehicles.' . $i . '.product_name', $vehicle['product_name'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.product_name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select class="form-select" name="vehicles[{{ $i }}][[unit]]" required>
                                                            <option value="tháng" {{ "tháng" == $vehicle->unit }}>Tháng</option>
                                                            <option value="ngày" {{ "ngày" == $vehicle->unit }}>Ngày</option>
                                                        </select>
                                                        @error('vehicles.' . $i . '.quantity')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[{{ $i }}][amount]" class="form-control" min="1" value="{{ old('vehicles.' . $i . '.amount', $vehicle['amount'] ?? '1') }}">
                                                        @error('vehicles.' . $i . '.amount')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[{{ $i }}][price]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.unit', $vehicle['price'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][money]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.money', $vehicle['money'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.money')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][start_date]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.start_date', $vehicle['start_date'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.start_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[{{ $i }}][end_date]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.end_date', $vehicle['end_date'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.end_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[{{ $i }}][notes]" class="form-control form-control-sm unit-input" value="{{ old('vehicles.' . $i . '.notes', $vehicle['notes'] ?? '') }}">
                                                        @error('vehicles.' . $i . '.notes')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ri-delete-bin-fill"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td>
                                                        <select class="form-select" name="vehicle_ids" required>
                                                            <option value="">Chọn phương tiện</option>
                                                        </select>
                                                        <div class="text-danger" id="error-vehicles-0-name">
                                                            @error('vehicles.0.name')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vehicles[0][notes]" class="form-control" value="{{ old('vehicles.0.notes') }}">
                                                        <div class="text-danger" id="error-vehicles-0-notes">
                                                            @error('vehicles.0.notes')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select class="form-select" name="vehicle_ids" required>
                                                            <option value="1">Tháng</option>
                                                            <option value="1">Ngày</option>
                                                        </select>
                                                        <div class="text-danger" id="error-vehicles-0-quantity">
                                                            @error('vehicles.0.quantity')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][weight]" class="form-control" min="1" value="{{ old('vehicles.0.weight') ?? 1 }}">
                                                        <div class="text-danger" id="error-vehicles-0-weight">
                                                            @error('vehicles.0.weight')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vehicles[0][unit]" class="form-control unit-input" value="{{ old('vehicles.0.unit') }}">
                                                        <div class="text-danger" id="error-vehicles-0-unit">
                                                            @error('vehicles.0.unit')
                                                            {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger" onclick="removeGoodRow(this, 0)"><i class="ri-delete-bin-fill"></i></button>
                                                        <input type="hidden" name="vehicles_rows[]" value="0">
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>  --}}

                                <div>
                                    <button type="submit" class="btn rounded-pill btn-secondary waves-effect">Save</button>
                                </div>
                            </form>
                                </div>

                                <!-- Vehicle Logs Tab -->
                                <div class="tab-pane" id="vehicle-logs" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title mb-0">Danh sách Nhật ký lộ trình xe</h5>
                                        <div>
                                            <a href="{{ route('admin.car-rental.download-vehicle-log', ['car_rental_id' => $carRental->id]) }}" class="btn btn-success me-2">
                                                <i class="ri-download-2-line align-bottom me-1"></i> Download nhật ký
                                            </a>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarRentalVehicleLogModal">
                                                <i class="ri-add-line align-bottom me-1"></i> Thêm nhật ký
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Ngày chạy</th>
                                                    <th class="text-center">Giờ làm việc</th>
                                                    <th class="text-center">Vị trí</th>
                                                    <th>Thời gian tăng ca</th>
                                                    <th>Km bắt đầu</th>
                                                    <th>Km kết thúc</th>
                                                    <th>Tổng km</th>
                                                    <th>Chi phí tăng ca</th>
                                                    <th>Phí cầu đường</th>
                                                    <th>Phí đậu xe</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($carRentalVehicleLogs as $log)
                                                <tr>
                                                    <td class="text-center">{{ $log->run_date ? \Carbon\Carbon::parse($log->run_date)->format('Y-m-d') : '' }}</td>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->end_time)->format('H:i') }}</td>
                                                    <td class="text-center">{{ $log->start_location }} -> {{ $log->end_location }}</td>
                                                    <td>{{ number_format($log->overtime_hours, 1) }} giờ</td>
                                                    <td>{{ number_format($log->start_odometer) }}</td>
                                                    <td>{{ number_format($log->end_odometer) }}</td>
                                                    <td>{{ number_format($log->total_distance) }}</td>
                                                    <td>{{ number_format($log->total_overtime_cost) }}</td>
                                                    <td>
                                                        @if($log->tollFees->count() > 0)
                                                            <span class="badge bg-info">{{ number_format($log->total_toll_fee) }}</span>
                                                            <small class="d-block text-muted">{{ $log->tollFees->count() }} trạm</small>
                                                        @else
                                                            <span class="text-muted">0</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($log->parking_fee) }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-sm btn-primary" onclick="editVehicleLog({{ $log->id }})">
                                                                <i class="ri-edit-line"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteVehicleLog({{ $log->id }})">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
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
</div>

<!-- Add Vehicle Log Modal -->
<div class="modal fade" id="addCarRentalVehicleLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm nhật ký xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.car-rental.store-vehicle-log') }}" method="POST" id="addCarRentalVehicleLogForm">
                @csrf
                <input type="hidden" name="car_rental_id" value="{{ $carRental->id }}">
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Chọn xe <span class="text-danger">*</span></label>
                            <select class="form-select" name="vehicle_id" required>
                                <option value="">Chọn xe</option>
                                @foreach($carRental->carRentalVehicles as $carRentalVehicle)
                                    <option value="{{ $carRentalVehicle->vehicle_id }}">
                                        {{ $carRentalVehicle->vehicle->plate_number ?? 'N/A' }} - 
                                        {{ $carRentalVehicle->vehicle->name ?? $carRentalVehicle->vehicle->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Ngày chạy <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="run_date" id="run_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="start_time" id="start_time" required inputmode="numeric" style="cursor:pointer;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="end_time" id="end_time" required inputmode="numeric" style="cursor:pointer;">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Vị trí đi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="start_location" required id="start_location">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vị trí đến <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="end_location" required id="end_location">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Km bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="start_odometer" id="start_odometer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Km kết thúc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="end_odometer" id="end_odometer" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Đơn giá tăng ca (VNĐ/giờ)</label>
                            <input type="text" class="form-control" value="50,000" readonly style="background-color: #f8f9fa;">
                            <small class="text-muted">Đơn giá cố định: 50,000 VNĐ/giờ</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phí đậu xe</label>
                            <input type="text" class="form-control" name="parking_fee">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Phí cầu đường</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTollFeeRow()">
                                <i class="fas fa-plus me-1"></i>Thêm trạm
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm" id="tollFeesTable">
                                <thead>
                                    <tr>
                                        <th>Tên trạm</th>
                                        <th>Mã giao dịch</th>
                                        <th>Số tiền</th>
                                        <th>Ghi chú</th>
                                        <th width="80"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Toll fee rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu nhật ký</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vehicle Log Modal -->
<div class="modal fade" id="editCarRentalVehicleLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa nhật ký xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editCarRentalVehicleLogForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="car_rental_id" value="{{ $carRental->id }}">
                <input type="hidden" name="log_id" id="edit_log_id">
                
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Chọn xe <span class="text-danger">*</span></label>
                            <select class="form-select" name="vehicle_id" id="edit_vehicle_id" required>
                                <option value="">Chọn xe</option>
                                @foreach($carRental->carRentalVehicles as $carRentalVehicle)
                                    <option value="{{ $carRentalVehicle->vehicle_id }}">
                                        {{ $carRentalVehicle->vehicle->plate_number ?? 'N/A' }} - 
                                        {{ $carRentalVehicle->vehicle->name ?? $carRentalVehicle->vehicle->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Ngày chạy <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="run_date" id="edit_run_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="start_time" id="edit_start_time" required inputmode="numeric" style="cursor:pointer;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="end_time" id="edit_end_time" required inputmode="numeric" style="cursor:pointer;">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Vị trí đi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="start_location" required id="edit_start_location">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vị trí đến <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="end_location" required id="edit_end_location">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Km bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="start_odometer" id="edit_start_odometer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Km kết thúc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="end_odometer" id="edit_end_odometer" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Đơn giá tăng ca</label>
                            <input type="text" class="form-control" value="50,000" readonly style="background-color: #f8f9fa;">
                            <small class="text-muted">Đơn giá cố định: 50,000 VNĐ/giờ</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phí đậu xe</label>
                            <input type="text" class="form-control" name="parking_fee" id="edit_parking_fee">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Phí cầu đường</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEditTollFeeRow()">
                                <i class="fas fa-plus me-1"></i>Thêm trạm
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm" id="editTollFeesTable">
                                <thead>
                                    <tr>
                                        <th>Tên trạm <span class="text-danger">*</span></th>
                                        <th>Mã giao dịch <span class="text-danger">*</span></th>
                                        <th>Số tiền <span class="text-danger">*</span></th>
                                        <th>Ghi chú</th>
                                        <th width="80"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Edit toll fee rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="notes" id="edit_notes" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Cập nhật nhật ký</button>
                </div>
            </form>
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
    // Add Vehicle Log Form AJAX Submission
    $('#addCarRentalVehicleLogForm').on('submit', function(e) {
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
        
        // Check start_time and end_time
        startTime = $('[name="start_time"]').val();
        endTime = $('[name="end_time"]').val();
        
        if (!startTime) {
            showFieldError('[name="start_time"]', 'Thời gian bắt đầu là bắt buộc');
            hasErrors = true;
        }
        
        if (!endTime) {
            showFieldError('[name="end_time"]', 'Thời gian kết thúc là bắt buộc');
            hasErrors = true;
        }
        
        // Check toll fees required fields
        $('#tollFeesTable tbody tr').each(function(index) {
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
        const startOdo = parseFloat($('[name="start_odometer"]').val().replace(/,/g, ''));
        const endOdo = parseFloat($('[name="end_odometer"]').val().replace(/,/g, ''));

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
            title: 'Đang xử lý...',
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
                    $('#addCarRentalVehicleLogModal').modal('hide');
                    
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
                        // Reload page to show new log
                        location.reload();
                    });
                }
            },
            error: (xhr) => {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        const input = $(`[name="${field}"]`);
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

    // Format number inputs on change (when user leaves the input)
    $('input[name="start_odometer"], input[name="end_odometer"], input[name="parking_fee"], .toll-fee-amount, input[name="monthly_rental_fee"]').on('change', function () {
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

    // Initialize formatting for existing values
    $('input[name="start_odometer"], input[name="end_odometer"], input[name="parking_fee"], .toll-fee-amount, input[name="monthly_rental_fee"]').each(function() {
        let initial = $(this).val().replace(/[^0-9.]/g, '');
        if (initial) {
            let parts = initial.split('.');
            let formatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (parts[1]) {
                formatted += '.' + parts[1].slice(0, 2);
            }
            $(this).val(formatted);
        }
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
});

// Function to edit vehicle log
window.editVehicleLog = function(logId) {
    // Show loading
    Swal.fire({
        title: 'Đang tải dữ liệu...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Fetch log data
    $.ajax({
        url: `/admin/car-rental/vehicle-log/${logId}/edit`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: (response) => {
            Swal.close();
            
            // Populate form fields
            $('#edit_log_id').val(response.log.id);
            $('#edit_vehicle_id').val(response.log.vehicle_id);
            
            // Convert date format for Flatpickr input
            let startTime = response.log.start_time;
            let endTime = response.log.end_time;
            if (startTime.includes('T')) startTime = startTime.replace('T', ' ');
            if (endTime.includes('T')) endTime = endTime.replace('T', ' ');
            
            $('#edit_start_time').val(startTime);
            $('#edit_end_time').val(endTime);
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
            $('#edit_run_date').val(response.log.run_date ? String(response.log.run_date).substring(0, 10) : '');
            
            // Load toll fees for edit
            loadEditTollFees(response.log.toll_fees || []);
            
            // Set form action
            $('#editCarRentalVehicleLogForm').attr('action', `/admin/car-rental/vehicle-log/${response.log.id}`);
            
            // Show modal
            $('#editCarRentalVehicleLogModal').modal('show');
        },
        error: (xhr) => {
            Swal.fire({
                title: "Lỗi!",
                text: "Không thể tải dữ liệu nhật ký xe",
                icon: "error"
            });
        }
    });
}

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

function deleteVehicleLog(logId) {
    Swal.fire({
        title: 'Bạn có chắc chắn muốn xóa?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Đang xóa...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/admin/car-rental/vehicle-log/${logId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire('Lỗi!', xhr.responseJSON?.message || 'Có lỗi xảy ra, vui lòng thử lại', 'error');
                }
            });
        }
    });
}

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
function addEditTollFeeRow() {
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
}

// Remove toll fee row from create form
function removeTollFeeRow(index) {
    $(`#tollFeesTable tbody tr[data-index="${index}"]`).remove();
}

// Remove toll fee row from edit form
function removeEditTollFeeRow(index) {
    $(`#editTollFeesTable tbody tr[data-index="${index}"]`).remove();
}

// Load toll fees for edit form
function loadEditTollFees(tollFees) {
    const tbody = $('#editTollFeesTable tbody');
    tbody.empty();
    editTollFeeRowIndex = 0;
    
    tollFees.forEach((tollFee, index) => {
        const row = `
            <tr data-index="${editTollFeeRowIndex}">
                <td>
                    <input type="text" class="form-control form-control-sm" name="toll_fees[${editTollFeeRowIndex}][station_name]" value="${tollFee.station_name}" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="toll_fees[${editTollFeeRowIndex}][transaction_code]" value="${tollFee.transaction_code || ''}" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm edit-toll-fee-amount" name="toll_fees[${editTollFeeRowIndex}][fee_amount]" value="${formatNumber(tollFee.fee_amount)}" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="toll_fees[${editTollFeeRowIndex}][notes]" value="${tollFee.notes || ''}">
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
    });
    
    // Initialize number formatting for loaded rows
    initializeEditTollFeeFormatting();
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

// Initialize number formatting for toll fee amounts in edit form
function initializeEditTollFeeFormatting() {
    $('.edit-toll-fee-amount').off('input').on('input', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');
        
        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';
        
        $(this).val(integerPart + decimalPart);
    });
}

// Initialize toll fee formatting when modals are shown
$('#addCarRentalVehicleLogModal').on('shown.bs.modal', function() {
    initializeTollFeeFormatting();
});

$('#editCarRentalVehicleLogModal').on('shown.bs.modal', function() {
    initializeEditTollFeeFormatting();
});

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
</script>
@endpush
