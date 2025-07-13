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
                                        Nhật ký xe
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
                                </div>

                                <div>
                                    <button type="submit" class="btn rounded-pill btn-secondary waves-effect">Save</button>
                                </div>
                            </form>
                                </div>

                                <!-- Vehicle Logs Tab -->
                                <div class="tab-pane" id="vehicle-logs" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title mb-0">Danh sách nhật ký xe</h5>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarRentalVehicleLogModal">
                                            <i class="ri-add-line align-bottom me-1"></i> Thêm nhật ký
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian bắt đầu</th>
                                                    <th>Thời gian kết thúc</th>
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
                                                    <td>{{ $log->start_time->format('Y-m-d H:i') }}</td>
                                                    <td>{{ $log->end_time->format('Y-m-d H:i') }}</td>
                                                    <td>{{ number_format($log->overtime_hours) }}</td>
                                                    <td>{{ number_format($log->start_odometer) }}</td>
                                                    <td>{{ number_format($log->end_odometer) }}</td>
                                                    <td>{{ number_format($log->total_distance) }}</td>
                                                    <td>{{ number_format($log->total_overtime_cost) }}</td>
                                                    <td>{{ number_format($log->toll_fee) }}</td>
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
                        <div class="col-md-6">
                            <label class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="end_time" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Km bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="start_odometer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Km kết thúc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="end_odometer" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Đơn giá tăng ca (VNĐ/giờ)</label>
                            <input type="text" class="form-control" name="overtime_rate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phí cầu đường</label>
                            <input type="text" class="form-control" name="toll_fee">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Phí đậu xe</label>
                            <input type="text" class="form-control" name="parking_fee">
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
                        <div class="col-md-6">
                            <label class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="start_time" id="edit_start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="end_time" id="edit_end_time" required>
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
                            <input type="text" class="form-control" name="overtime_rate" id="edit_overtime_rate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phí cầu đường</label>
                            <input type="text" class="form-control" name="toll_fee" id="edit_toll_fee">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Phí đậu xe</label>
                            <input type="text" class="form-control" name="parking_fee" id="edit_parking_fee">
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
        
        // Clear previous error messages
        $('.text-danger').remove();
        
        // Get form data
        const formData = new FormData(this);
        
        // Basic validation
        const startTime = $('[name="start_time"]').val();
        const endTime = $('[name="end_time"]').val();
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
    $('input[name="start_odometer"], input[name="end_odometer"], input[name="overtime_rate"], input[name="toll_fee"], input[name="parking_fee"]').on('input', function () {
        let value = $(this).val();

        // Remove all non-numeric characters except dots
        value = value.replace(/[^0-9.]/g, '');

        // Handle decimal formatting
        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';

        $(this).val(integerPart + decimalPart);
    });

    // Initialize formatting for existing values
    $('input[name="start_odometer"], input[name="end_odometer"], input[name="overtime_rate"], input[name="toll_fee"], input[name="parking_fee"]').each(function() {
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
    $('input[id="edit_start_odometer"], input[id="edit_end_odometer"], input[id="edit_overtime_rate"], input[id="edit_toll_fee"], input[id="edit_parking_fee"]').on('input', function () {
        let value = $(this).val();

        // Remove all non-numeric characters except dots
        value = value.replace(/[^0-9.]/g, '');

        // Handle decimal formatting
        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';

        $(this).val(integerPart + decimalPart);
    });

    // Initialize formatting for edit form inputs when modal is shown
    $('#editCarRentalVehicleLogModal').on('shown.bs.modal', function() {
        // Format only numeric inputs in edit form
        $('input[id="edit_start_odometer"], input[id="edit_end_odometer"], input[id="edit_overtime_rate"], input[id="edit_toll_fee"], input[id="edit_parking_fee"]').each(function() {
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

    flatpickr("#edit_start_time", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("#edit_end_time", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("input[name='start_time']:not(#edit_start_time)", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("input[name='end_time']:not(#edit_end_time)", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
});

// Function to edit vehicle log
function editVehicleLog(logId) {
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
            $('#edit_overtime_rate').val(response.log.overtime_rate);
            $('#edit_toll_fee').val(response.log.toll_fee);
            $('#edit_parking_fee').val(response.log.parking_fee);
            $('#edit_notes').val(response.log.notes);
            
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
    
    // Clear previous error messages
    $('.text-danger').remove();
    
    // Get form data
    const formData = new FormData(this);
    
    // Basic validation
    const startTime = $('[name="start_time"]', this).val();
    const endTime = $('[name="end_time"]', this).val();
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
</script>
@endpush
