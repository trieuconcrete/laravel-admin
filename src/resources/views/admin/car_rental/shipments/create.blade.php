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
            <form action="{{ route('admin.shipments.store') }}" method="POST" enctype="multipart/form-data" id="shipmentForm">
                @csrf
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
                                                    <input type="text" class="form-control" value="50,000">
                                                    <small class="text-muted">Đơn giá cố định: 50,000 VNĐ/giờ</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Phí đậu xe</label>
                                                    <input type="text" class="form-control" name="parking_fee">
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
                                                        value="1" 
                                                        id="is_car_rental">
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
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverRow(this, {{ $i }})"><i class="ri-delete-bin-fill"></i></button>
                                                                            <input type="hidden" name="driver_rows[]" value="{{ $i }}">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
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
<script src="{{ asset('js/shipment-form.js') }}"></script>
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
        
        // Format deduction inputs and unit inputs on keyup
        $('.deduction-input, .unit-input').on('input', function () {
            formatPriceInput($(this));
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
        
        // Make the formatPriceInput function globally available
        window.formatPriceInput = function(input) {
            formatPriceInput($(input));
        };
    });
</script>
<script>
    // Khai báo các biến cần thiết
    const goodsTable = document.querySelector('#goodsTable tbody');
    let goodsCount = {{ count(old('goods', [])) ?: 1 }};
    const personTable = document.querySelector('#personTable tbody');
    const personPxTable = document.querySelector('#personPxTable tbody');
    
    // Lưu trữ dữ liệu cũ từ validation errors
    window.laravelOld = @json(session()->getOldInput());
    
    // Khai báo các loại khấu trừ cho tài xế
    const personDeductionTypes = [
        @foreach($personDeductionTypes as $type)
            { id: "{{ $type->id }}", name: "{{ $type->name }}" },
        @endforeach
    ];

    const personPxDeductionTypes = [
        @foreach($subPersonDeductionTypes as $type)
            { id: "{{ $type->id }}", name: "{{ $type->name }}" },
        @endforeach
    ];
    
    // Gán danh sách người dùng vào biến toàn cục
    // Đảm bảo users là một object với id làm key
    window.users = {};
    @if(!empty($users) && is_array($users))
        @foreach($users as $id => $name)
            window.users[{{ $id }}] = '{{ addslashes($name) }}';
        @endforeach
    @endif

    window.userPXs = {};
    @if(!empty($userPXs) && is_array($userPXs))
        @foreach($userPXs as $id => $name)
            window.userPXs[{{ $id }}] = '{{ addslashes($name) }}';
        @endforeach
    @endif
    
    console.log('Available users:', window.users);
    
    // Khởi tạo các sự kiện khi trang đã tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo form với số lượng driver ban đầu
        initShipmentForm({{ count(old('drivers', [])) ?: 1 }});
        
        // Cập nhật trạng thái nút thêm tài xế
        updateAddPersonButtonState();
        
        // Thêm event listener cho nút thêm hàng hóa
        document.getElementById('addGoodBtn').onclick = function() {
            goodsCount = addGoodRow(goodsTable, goodsCount);
        };
        
        // Thêm event listener cho nút thêm người
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

            if (currentRows > 3) {
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
        document.getElementById('addPersonPxBtn').onclick = function() {
            // Kiểm tra số lượng user trước khi thêm
            const selectedIds = getSelectedUserIds(personPxTable, 'driverPXs');
            const totalUserPXs = Object.keys(window.userPXs).length;
            const currentRows = personPxTable.querySelectorAll('tbody tr').length;
            
            console.log('Button click - Selected IDs:', selectedIds.length, 'Total Users:', totalUserPXs, 'Current Rows:', currentRows);
            
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

            if (currentRows > 3) {
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
        
        // Kiểm tra và cập nhật trạng thái nút thêm nhân sự dựa trên số lượng người dùng khả dụng
        updateAddPersonButtonState();
        
        // Định dạng tất cả các trường số khi trang được tải
        formatAllNumericInputs();
        
        // Kiểm tra và chuyển đến tab có lỗi nếu có
        handleFormErrors();
        
        // Xử lý checkbox "Xe HPL Thuê"
        const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
        const driverSection = document.getElementById('drivers');
        
        function toggleDriverSections() {
            if (!isCarRentalCheckbox || !driverSection) {
                return; // Exit if elements don't exist
            }
            
            const isChecked = isCarRentalCheckbox.checked;
            if (isChecked) {
                // Nếu là xe thuê, ẩn phần tài xế
                driverSection.style.display = 'none';
            } else {
                // Nếu không phải xe thuê, hiện phần tài xế
                driverSection.style.display = 'block';
            }
        }
        
        // Thêm event listener cho checkbox
        if (isCarRentalCheckbox) {
            isCarRentalCheckbox.addEventListener('change', toggleDriverSections);
            // Chạy lần đầu khi trang load
            toggleDriverSections();
        }
        
        // Xử lý submit form
        document.getElementById('shipmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateShipmentForm()) {
                prepareFormBeforeSubmit();
                this.submit();
            }
        });
    });

    document.getElementById('avatarInput')?.addEventListener('change', function(event) {
        const file = event.target.files[0];
    
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
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
