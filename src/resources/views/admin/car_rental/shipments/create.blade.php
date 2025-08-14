@extends('admin.layout')
@section('title', 'Tạo nhật ký hành trình thuê xe')
@section('content')

<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col">
            <form action="{{ isset($carRental) ? route('admin.car-rental.store-vehicle-log') : route('admin.shipments.store') }}" method="POST" enctype="multipart/form-data" id="shipmentForm">
                @csrf
                <!-- Hidden field for customer_id when coming from car-rental -->
                @if(isset($carRental))
                    <input type="hidden" name="customer_id" value="{{ $carRental->customer_id }}">
                    <input type="hidden" name="car_rental_id" value="{{ $carRental->id }}">
                @endif
                <!-- Hidden field for is_car_rental value -->
                <input type="hidden" id="is_car_rental_value" name="is_car_rental_value" value="0">
                <div class="row mb-3 pb-1">
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                <div class="flex-grow-1">
                                </div>
                                <div class="mt-3 mt-lg-0">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                <i class="ri-save-3-line align-middle me-1"></i>Lưu 
                                            </button>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </div>
                            </div><!-- end card header -->
                        </div>
                        <!--end col-->
                    </div>
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#driverAllowance" role="tab">
                                                <i class="far fa-user"></i> Thông tin chuyến
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#shipmentDetail" role="tab">
                                                <i class="fas fa-home"></i> Phương tiện & tài xế
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="driverAllowance" role="tabpanel">
                                            <h5 class="mb-3 fs-5">Thông tin vận chuyển</h5>
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
                                                    <input type="text" class="form-control odometer-input" name="start_odometer" id="start_odometer" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Km kết thúc <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control odometer-input" name="end_odometer" id="end_odometer" required>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="pending">Tạo mới</option>
                                                        <option value="in_transit">Đang vận chuyển</option>
                                                        <option value="cancelled">Đã hủy</option>
                                                        <option value="delayed">Bị trễ</option>
                                                        <option value="completed">Hoàn thành</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for=""></label>
                                                    <div class="form-check form-check-secondary mb-3">
                                                        <input class="form-check-input" 
                                                        name="is_overtime_at_noon" 
                                                        type="checkbox" 
                                                        value="1" 
                                                        id="is_overtime_at_noon">
                                                        <label class="form-check-label" for="is_overtime_at_noon">
                                                            Có tăng ca trưa
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Đơn giá tăng ca (VNĐ/giờ) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="overtime_rate" id="overtime_rate" value="50,000" required>
                                                    <small class="text-muted">Đơn giá tăng ca cho tài xế</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Phí đậu xe</label>
                                                    <input type="text" class="form-control parking-fee-input" name="parking_fee">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Kết quả tính OT</label>
                                                    <div class="bg-light p-3 rounded">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <small class="text-muted">Số giờ OT:</small><br>
                                                                <strong id="overtime_hours_display">0.00 giờ</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">Tổng chi phí OT:</small><br>
                                                                <strong id="total_overtime_cost_display">0 VNĐ</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    OT được tính từ 17:30, +1h nếu có tăng ca trưa
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Chi tiết tính OT</label>
                                                    <div class="bg-light p-3 rounded">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <small class="text-muted">Giờ làm việc:</small><br>
                                                                <strong id="working_hours_display">0.00 giờ</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">Tăng ca trưa:</small><br>
                                                                <strong id="noon_overtime_display">0.00 giờ</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calculator me-1"></i>
                                                                    Tổng OT = Giờ làm việc + Tăng ca trưa
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <input type="hidden" name="calculated_overtime_hours" value="0">
                                                    <input type="hidden" name="calculated_total_overtime_cost" value="0">
                                                    <input type="hidden" name="working_hours" value="0">
                                                    <input type="hidden" name="noon_overtime_hours" value="0">
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
                                        <div class="tab-pane" id="shipmentDetail" role="tabpanel">
                                            <div class="row mb-3">
                                                <div class="col-md-2">
                                                    <div class="form-check form-check-secondary mb-3">
                                                        <input class="form-check-input" 
                                                        name="is_car_rental" 
                                                        type="checkbox" 
                                                        value="0" 
                                                        id="is_car_rental"
                                                        {{--  checked  --}}
                                                        >
                                                        <label class="form-check-label" for="is_car_rental">
                                                            Xe HPL Thuê
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Phương tiện<span class="text-danger">*</span></label>
                                                    <select class="form-select" name="vehicle_id" id="vehicles">
                                                        <option value="">Chọn phương tiện</option>
                                                        @foreach($vehicles as $vehicle)
                                                            <option value="{{ (int)$vehicle->vehicle_id }}" {{ old('vehicle_id') == (int)$vehicle->vehicle_id ? 'selected' : '' }}>{{ $vehicle->plate_number . '-' . $vehicle->vehicleType->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('vehicle_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                    
                                                    <!-- Loading spinner (hidden by default) -->
                                                    <div class="spinner-border spinner-border-sm text-primary mt-2" id="vehicle_loading" style="display: none;">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="drivers">
                                                <hr>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-label mb-0">Tài xế</label>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addPersonBtn">
                                                            <i class="fas fa-plus me-1"></i>Thêm tài xế
                                                        </button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm" id="personTable">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nhân sự <span class="text-danger">*</span></th>
                                                                    <th>Lái chính </th>
                                                                    @foreach($personDeductionTypes as $type)
                                                                        <th>{{ $type->name }}</th>
                                                                    @endforeach
                                                                    <th>Ghi chú</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php
                                                                $drivers = old('drivers', []);
                                                                $driversCount = count($drivers);
                                                            @endphp
                                                            @if($driversCount > 0)
                                                                @foreach($drivers as $i => $driver)
                                                                    <tr>
                                                                        <td>
                                                                            <select name="drivers[{{ $i }}][user_id]" class="form-select form-select-sm" style="min-width: 180px;" required>
                                                                                <option value="">Chọn nhân sự</option>
                                                                                @foreach($users as $id => $name)
                                                                                    <option value="{{ $id }}" {{ old('drivers.'.$i.'.user_id', $driver['user_id'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('drivers.'.$i.'.user_id')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <div class="form-check form-switch d-inline-block">
                                                                                <input type="checkbox" name="drivers[{{ $i }}][deductions][is_main_driver]" class="form-check-input deduction-input" value="1" 
                                                                                    {{ old('drivers.'.$i.'.deductions.is_main_driver', $driver['deductions']['is_main_driver'] ?? false) ? 'checked' : '' }}>
                                                                            </div>
                                                                            @error('drivers.{{ $i }}.deductions.is_main_driver')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        @foreach($personDeductionTypes as $type)
                                                                            <td>
                                                                                <input type="text" name="drivers[{{ $i }}][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0" value="{{ old('drivers.'.$i.'.deductions.'.$type->id, $driver['deductions'][$type->id] ?? '') }}">
                                                                                @error('drivers.'.$i.'.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                            </td>
                                                                        @endforeach
                                                                        <td>
                                                                            <input type="text" name="drivers[{{ $i }}][deductions][Ghi chú]" class="form-control form-control-sm " value="{{ old('drivers.'.$i.'.deductions.Ghi chú', $driver['deductions'][$type->id]['Ghi chú'] ?? '') }}">
                                                                            @error('drivers.{{ $i }}.deductions.Ghi chú')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverRow(this, {{ $i }})"><i class="ri-delete-bin-fill"></i></button>
                                                                            <input type="hidden" name="driver_rows[]" value="{{ $i }}">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>
                                                                        <select name="drivers[0][user_id]" class="form-select form-select-sm" required>
                                                                            <option value="">Chọn nhân sự</option>
                                                                            @foreach($users as $id => $name)
                                                                                <option value="{{ $id }}">{{ $name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('drivers.0.user_id')<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="form-check form-switch d-inline-block">
                                                                            <input type="checkbox" name="drivers[0][deductions][is_main_driver]" class="form-check-input deduction-input" value="1" 
                                                                                {{ old('drivers.0.deductions.is_main_driver', $driver['deductions']['is_main_driver'] ?? false) ? 'checked' : '' }}>
                                                                        </div>
                                                                        @error('drivers.0.deductions.is_main_driver')<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                    @foreach($personDeductionTypes as $type)
                                                                        <td>
                                                                            <input type="text" name="drivers[0][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0">
                                                                            @error('drivers.0.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                    @endforeach
                                                                    <td>
                                                                        <input type="text" name="drivers[0][deductions][Ghi chú]" class="form-control form-control-sm " value="{{ old('drivers.0.deductions.Ghi chú', $driver['deductions'][$type->id]['Ghi chú'] ?? '') }}">
                                                                        @error('drivers.0.deductions.Ghi chú')<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverRow(this, 0)"><i class="ri-delete-bin-fill"></i></button>
                                                                        <input type="hidden" name="driver_rows[]" value="0">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-label mb-0">Lơ xe</label>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addPersonPxBtn">
                                                            <i class="fas fa-plus me-1"></i>Thêm lơ xe
                                                        </button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm" id="personPxTable">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nhân sự <span class="text-danger">*</span></th>
                                                                    @foreach($subPersonDeductionTypes as $type)
                                                                        <th>{{ $type->name }}</th>
                                                                    @endforeach
                                                                    <th>Ghi chú</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php
                                                                $drivers = old('drivers', []);
                                                                $driversCount = count($drivers);
                                                            @endphp
                                                            @if($driversCount > 0)
                                                                @foreach($drivers as $i => $driver)
                                                                    <tr>
                                                                        <td>
                                                                            <select name="driverPXs[{{ $i }}][user_id]" class="form-select form-select-sm" style="min-width: 180px;" required>
                                                                                <option value="">Chọn nhân sự</option>
                                                                                @foreach($userPXs as $id => $name)
                                                                                    <option value="{{ $id }}" {{ old('driverPXs.'.$i.'.user_id', $driver['user_id'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('driverPXs.'.$i.'.user_id')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        @foreach($subPersonDeductionTypes as $type)
                                                                            <td>
                                                                                <input type="text" name="driverPXs[{{ $i }}][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0" value="{{ old('driverPXs.'.$i.'.deductions.'.$type->id, $driver['deductions'][$type->id] ?? '') }}">
                                                                                @error('driverPXs.'.$i.'.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                            </td>
                                                                        @endforeach
                                                                        <td>
                                                                            <input type="text" name="driverPXs[{{ $i }}][deductions][Ghi chú]" class="form-control form-control-sm " value="{{ old('driverPXs.'.$i.'.deductions.Ghi chú', $driver['deductions'][$type->id]['Ghi chú'] ?? '') }}">
                                                                            @error('driverPXs.'.$i.'.deductions.Ghi chú')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverPXRow(this, {{ $i }})"><i class="ri-delete-bin-fill"></i></button>
                                                                            <input type="hidden" name="driverPX_rows[]" value="{{ $i }}">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>
                                                                        <select name="driverPXs[0][user_id]" class="form-select form-select-sm" style="min-width: 180px;">
                                                                            <option value="">Chọn nhân sự</option>
                                                                            @foreach($userPXs as $id => $name)
                                                                                <option value="{{ $id }}">{{ $name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('driverPXs.0.user_id')<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                    @foreach($subPersonDeductionTypes as $type)
                                                                        <td>
                                                                            <input type="text" name="driverPXs[0][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0">
                                                                            @error('driverPXs.0.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                    @endforeach
                                                                    <td>
                                                                        <input type="text" name="driverPXs[0][deductions][Ghi chú]" class="form-control form-control-sm">
                                                                        @error('driverPXs.0.deductions.Ghi chú')<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden" name="driverPX_rows[]" value="0">
                                                                    </td>
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/car-rental-form.js') }}"></script>
<script>
    // Set dữ liệu từ Blade template vào JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Set personDeductionTypes
        window.personDeductionTypes = [
            @foreach($personDeductionTypes as $type)
                { id: "{{ $type->id }}", name: "{{ $type->name }}" },
            @endforeach
        ];

        // Set personPxDeductionTypes
        window.personPxDeductionTypes = [
            @foreach($subPersonDeductionTypes as $type)
                { id: "{{ $type->id }}", name: "{{ $type->name }}" },
            @endforeach
        ];
        
        // Set users
        window.users = {};
        @if(!empty($users))
            @foreach($users as $id => $name)
                window.users[{{ $id }}] = '{{ addslashes($name) }}';
            @endforeach
        @endif

        // Set userPXs
        window.userPXs = {};
        @if(!empty($userPXs))
            @foreach($userPXs as $id => $name)
                window.userPXs[{{ $id }}] = '{{ addslashes($name) }}';
            @endforeach
        @endif
        
        // Set goodsCount
        window.goodsCount = {{ count(old('goods', [])) ?: 1 }};
        
        // Set laravelOld
        window.laravelOld = @json(session()->getOldInput());
        
        console.log('Car rental form data initialized:', {
            personDeductionTypes: window.personDeductionTypes,
            personPxDeductionTypes: window.personPxDeductionTypes,
            users: window.users,
            userPXs: window.userPXs,
            goodsCount: window.goodsCount
        });
        
        // Xử lý checkbox is_car_rental khi trang load
        const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
        if (isCarRentalCheckbox && isCarRentalCheckbox.checked) {
            // Nếu checkbox được checked, ẩn phần tài xế
            const driverSection = document.getElementById('drivers');
            if (driverSection) {
                driverSection.style.display = 'none';
                
                // Bỏ required và disable tất cả các trường tài xế
                const driverFields = driverSection.querySelectorAll('select[name*="[user_id]"], input[name*="[allowance]"], input[name*="[deduction]"]');
                driverFields.forEach(field => {
                    field.removeAttribute('required');
                    field.disabled = true;
                });
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        // Function to format price inputs with VND formatting and 9-digit limit
        function formatPriceInput(input) {
            let value = input.val();
            
            // Remove non-numeric characters and handle decimal part
            value = value.replace(/[^0-9.]/g, '');
            
            // If there's a decimal part, handle it
            if (value.includes('.')) {
                // Split into integer and decimal parts
                let parts = value.split('.');
                // If decimal part is .00, remove it completely
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0];
                } else {
                    // Otherwise keep only integer part
                    value = parts[0];
                }
            }
            
            // Limit to 9 digits
            if (value.length > 9) {
                value = value.substring(0, 9);
            }
            
            // Format with commas
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            input.val(value);
        }
        
        // Function to format odometer inputs with comma formatting
        function formatOdometerInput(input) {
            let value = input.val();
            
            // Remove non-numeric characters and decimal part
            value = value.replace(/[^0-9.]/g, '');
            
            // Handle decimal part - if it's .00 or .0, remove it completely
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0]; // Remove decimal part completely
                } else {
                    value = parts[0]; // Keep only integer part
                }
            }
            
            // Format with commas
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            input.val(value);
        }
        
        // Function to format parking fee input with VND formatting
        function formatParkingFeeInput(input) {
            let value = input.val();
            
            // Remove non-numeric characters and decimal part
            value = value.replace(/[^0-9.]/g, '');
            
            // Handle decimal part - if it's .00 or .0, remove it completely
            if (value.includes('.')) {
                let parts = value.split('.');
                if (parts[1] === '00' || parts[1] === '0') {
                    value = parts[0]; // Remove decimal part completely
                } else {
                    value = parts[0]; // Keep only integer part
                }
            }
            
            // Format with commas
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            input.val(value);
        }
        
        // Format deduction inputs and unit inputs on keyup
        $('.deduction-input, .unit-input').on('input', function () {
            formatPriceInput($(this));
        });
        
        // Format odometer inputs on keyup
        $('.odometer-input').on('input', function () {
            formatOdometerInput($(this));
        });
        
        // Format parking fee input on keyup
        $('.parking-fee-input').on('input', function () {
            formatParkingFeeInput($(this));
        });
        
        // Initial formatting for deduction inputs and unit inputs
        $('.deduction-input, .unit-input').each(function() {
            let value = $(this).val();
            if (value) {
                // Remove existing formatting
                value = value.replace(/,/g, '');
                
                // Handle decimal part if exists
                if (value.includes('.')) {
                    let parts = value.split('.');
                    // If decimal part is .00, remove it completely
                    if (parts[1] === '00' || parts[1] === '0') {
                        value = parts[0];
                    } else {
                        // Otherwise keep only integer part
                        value = parts[0];
                    }
                }
                
                // Limit to 9 digits
                if (value.length > 9) {
                    value = value.substring(0, 9);
                }
                
                // Apply formatting
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                $(this).val(value);
            }
        });
        
        // Initial formatting for odometer inputs
        $('.odometer-input').each(function() {
            let value = $(this).val();
            if (value) {
                // Remove existing formatting
                value = value.replace(/,/g, '');
                
                // Handle decimal part - if it's .00 or .0, remove it completely
                if (value.includes('.')) {
                    let parts = value.split('.');
                    if (parts[1] === '00' || parts[1] === '0') {
                        value = parts[0]; // Remove decimal part completely
                    } else {
                        value = parts[0]; // Keep only integer part
                    }
                }
                
                // Apply formatting
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                $(this).val(value);
            }
        });
        
        // Initial formatting for parking fee input
        $('.parking-fee-input').each(function() {
            let value = $(this).val();
            if (value) {
                // Remove existing formatting
                value = value.replace(/,/g, '');
                
                // Handle decimal part - if it's .00 or .0, remove it completely
                if (value.includes('.')) {
                    let parts = value.split('.');
                    if (parts[1] === '00' || parts[1] === '0') {
                        value = parts[0]; // Remove decimal part completely
                    } else {
                        value = parts[0]; // Keep only integer part
                    }
                }
                
                // Apply formatting
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                $(this).val(value);
            }
        });
        
        // Make the format functions globally available
        window.formatPriceInput = function(input) {
            formatPriceInput($(input));
        };
        
        window.formatOdometerInput = function(input) {
            formatOdometerInput($(input));
        };
        
        window.formatParkingFeeInput = function(input) {
            formatParkingFeeInput($(input));
        };
        
        // Thêm event listener cho nút thêm tài xế
        const personTable = document.querySelector('#personTable tbody');
        const personDeductionTypes = window.personDeductionTypes || [];
        
        document.getElementById('addPersonBtn').onclick = function() {
            // Kiểm tra số lượng user trước khi thêm
            const selectedIds = getSelectedUserIds(personTable, 'driver');
            const totalUsers = Object.keys(window.users).length;
            const currentRows = personTable.querySelectorAll('tr').length;
            
            console.log('Button click - Selected IDs:', selectedIds.length, 'Total Users:', totalUsers, 'Current Rows:', currentRows);
            
            // Kiểm tra số lượng hàng hiện tại với tổng số users
            if (currentRows >= totalUsers) {
                Swal.fire({
                    title: 'Không thể thêm',
                    text: 'Đã sử dụng hết tất cả nhân sự có sẵn. Số lượng nhân sự: ' + totalUsers,
                    icon: 'warning',
                    confirmButtonText: 'Đóng'
                });
                return false;
            }

            if (currentRows >= 3) {
                Swal.fire({
                    title: 'Không thể thêm',
                    text: 'Chỉ thêm được tối đa 3 tài xế',
                    icon: 'warning',
                    confirmButtonText: 'Đóng'
                });
                return false;
            }
            
            // Kiểm tra nếu đã sử dụng hết tất cả người dùng
            if (selectedIds.length >= totalUsers) {
                Swal.fire({
                    title: 'Không thể thêm',
                    text: 'Đã sử dụng hết tất cả nhân sự có sẵn',
                    icon: 'warning',
                    confirmButtonText: 'Đóng'
                });
                return false;
            }
            
            // Log users object for debugging
            console.log('Users object:', window.users);
            // Nếu còn người dùng khả dụng, thêm hàng mới
            addDriverRow(personTable, personDeductionTypes, window.users);
        };
        
        // Thêm event listener cho nút thêm lơ xe
        const personPxTable = document.querySelector('#personPxTable tbody');
        const personPxDeductionTypes = window.personPxDeductionTypes || [];
        
        document.getElementById('addPersonPxBtn').onclick = function() {
            // Kiểm tra số lượng user PX trước khi thêm
            const selectedIds = getSelectedUserIds(personPxTable, 'driverPX');
            const totalUserPXs = Object.keys(window.userPXs).length;
            const currentRows = personPxTable.querySelectorAll('tr').length;
            
            console.log('Button click - Selected IDs:', selectedIds.length, 'Total User PXs:', totalUserPXs, 'Current Rows:', currentRows);
            
            // Kiểm tra số lượng hàng hiện tại với tổng số users
            if (currentRows >= totalUserPXs) {
                Swal.fire({
                    title: 'Không thể thêm',
                    text: 'Đã sử dụng hết tất cả nhân sự có sẵn. Số lượng nhân sự: ' + totalUserPXs,
                    icon: 'warning',
                    confirmButtonText: 'Đóng'
                });
                return false;
            }

            if (currentRows >= 3) {
                Swal.fire({
                    title: 'Không thể thêm',
                    text: 'Chỉ thêm được tối đa 3 lơ xe',
                    icon: 'warning',
                    confirmButtonText: 'Đóng'
                });
                return false;
            }
            
            // Kiểm tra nếu đã sử dụng hết tất cả người dùng
            if (selectedIds.length >= totalUserPXs) {
                Swal.fire({
                    title: 'Không thể thêm',
                    text: 'Đã sử dụng hết tất cả nhân sự có sẵn',
                    icon: 'warning',
                    confirmButtonText: 'Đóng'
                });
                return false;
            }
            
            // Log users object for debugging
            console.log('Users object:', window.userPXs);
            // Nếu còn người dùng khả dụng, thêm hàng mới
            addDriverPXRow(personPxTable, personPxDeductionTypes, window.userPXs);
        };
    });
</script>
@endpush

@push('styles')
<style>
    /* Hiệu ứng highlight cho input có lỗi */
    .highlight-error {
        animation: highlight-error-animation 1.5s ease;
    }
    
    @keyframes highlight-error-animation {
        0% { background-color: rgba(255, 0, 0, 0.1); }
        50% { background-color: rgba(255, 0, 0, 0.2); }
        100% { background-color: transparent; }
    }
</style>
@endpush
