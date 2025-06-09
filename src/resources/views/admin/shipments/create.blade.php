@extends('admin.layout')

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
                                    <h4 class="fs-16 mb-1">Tạo chuyến hàng</h4>
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
                                                <i class="far fa-user"></i> Thông tin vận chuyển 
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
                                            <h5 class="mb-3">Thông tin vận chuyển</h5>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Chọn khách hàng<span class="text-danger">*</span></label>
                                                    <select class="form-select" name="customer_id" required>
                                                        <option value="">Chọn khách hàng</option>
                                                        @foreach($customers as $id => $name)
                                                            <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="pending">Chờ xác nhận</option>
                                                        <option value="in_transit">Đang vận chuyển</option>
                                                        <option value="delivered">Đã giao hàng</option>
                                                        <option value="cancelled">Đã hủy</option>
                                                        <option value="delayed">Bị trễ</option>
                                                        <option value="completed">Hoàn thành</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                @php
                                                // Xử lý giá trị mặc định và giá trị old() đúng định dạng
                                                $defaultDeparture = date('Y-m-d');
                                                $defaultArrival = date('Y-m-d', strtotime('+1 week'));
                                                
                                                // Nếu có giá trị old(), ưu tiên sử dụng nó
                                                $departureDateValue = old('departure_time', $defaultDeparture);
                                                $arrivalDateValue = old('estimated_arrival_time', $defaultArrival);
                                                @endphp
                                                <div class="col-md-6">
                                                    <label class="form-label">Thời gian khởi hành<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control date-input" name="departure_time" value="@formatDateForInput($departureDateValue)" required autocomplete="off">
                                                    @error('departure_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Thời gian dự kiến đến<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control date-input" name="estimated_arrival_time" value="@formatDateForInput($arrivalDateValue)" required autocomplete="off">
                                                    @error('estimated_arrival_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Điểm khởi hành <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="Nhập điểm khởi hành" name="origin" value="{{ old('origin') }}" required>
                                                    @error('origin')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Điểm đến</label>
                                                    <input type="text" class="form-control" placeholder="Nhập điểm đến" name="destination" value="{{ old('destination') }}">
                                                    @error('destination')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Số KM</label>
                                                    <input type="number" class="form-control" placeholder="Nhập số KM" name="distance" value="{{ old('distance') }}">
                                                    @error('distance')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Khối lượng (kg)</label>
                                                    <input type="text" class="form-control" placeholder="Nhập khối lượng" name="cargo_weight" value="{{ old('cargo_weight') }}">
                                                    @error('cargo_weight')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Số lượng chuyến</label>
                                                    <input type="number" class="form-control" placeholder="Nhập số lượng chuyến" name="trip_count" value="{{ old('trip_count') }}">
                                                    @error('trip_count')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Giá chuyến <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control unit-input" placeholder="Nhập giá chuyến" name="unit_price" value="{{ old('unit_price') }}">
                                                    @error('unit_price')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ghi chú</label>
                                                <textarea class="form-control" rows="2" placeholder="Nhập ghi chú" name="notes" value="{{ old('notes') }}"></textarea>
                                                @error('note')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <label class="form-label">Chi phí chuyến hàng</label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                @foreach($deductionTypes as $type)
                                                                    <th>{{ $type->name }}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                @foreach($deductionTypes as $type)
                                                                    <td>
                                                                        <input type="hidden" name="deduction_type_ids[]" value="{{ $type->id }}">
                                                                        <input type="text" class="form-control form-control-sm deduction-input" name="deductions[{{ $type->id }}]" min="0" value="{{ old('deductions.'.$type->id) }}">
                                                                        @error('deductions.'.$type->id)<span class="text-danger">{{ $message }}</span>@enderror
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label mb-0">Danh sách hàng hóa</label>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addGoodBtn">
                                                        <i class="fas fa-plus me-1"></i>Thêm hàng hóa
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-sm" id="goodsTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Tên hàng hóa <span class="text-danger">*</span></th>
                                                                <th>Mô tả</th>
                                                                <th>Số lượng</th>
                                                                <th>Trọng lượng (kg)</th>
                                                                <th>Giá trị (VNĐ)</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $goods = old('goods', []);
                                                                $goodsCount = count($goods);
                                                            @endphp
                                                            @if($goodsCount > 0)
                                                                @foreach($goods as $i => $good)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][name]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.name', $good['name'] ?? '') }}" required>
                                                                            @error('goods.'.$i.'.name')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][notes]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.notes', $good['notes'] ?? '') }}">
                                                                            @error('goods.'.$i.'.notes')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="goods[{{ $i }}][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.'.$i.'.quantity', $good['quantity'] ?? '') }}">
                                                                            @error('goods.'.$i.'.quantity')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="goods[{{ $i }}][weight]" class="form-control form-control-sm" min="0"  value="{{ old('goods.'.$i.'.weight', $good['weight'] ?? '') }}">
                                                                            @error('goods.'.$i.'.weight')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="goods[{{ $i }}][unit]" class="form-control form-control-sm unit-input" value="{{ old('goods.'.$i.'.unit', $good['unit'] ?? '') }}">
                                                                            @error('goods.'.$i.'.unit')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ri-delete-bin-fill"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" name="goods[0][name]" class="form-control form-control-sm" value="{{ old('goods.0.name') }}" required>
                                                                        <div class="text-danger" id="error-goods-0-name">@error('goods.0.name'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][notes]" class="form-control form-control-sm" value="{{ old('goods.0.notes') }}">
                                                                        <div class="text-danger" id="error-goods-0-notes">@error('goods.0.notes'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="goods[0][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.0.quantity') }}">
                                                                        <div class="text-danger" id="error-goods-0-quantity">@error('goods.0.quantity'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="goods[0][weight]" class="form-control form-control-sm" min="0"  value="{{ old('goods.0.weight') }}">
                                                                        <div class="text-danger" id="error-goods-0-weight">@error('goods.0.weight'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="goods[0][unit]" class="form-control form-control-sm unit-input" value="{{ old('goods.0.unit') }}">
                                                                        <div class="text-danger" id="error-goods-0-unit">@error('goods.0.unit'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGoodRow(this, 0)"><i class="ri-delete-bin-fill"></i></button>
                                                                        <input type="hidden" name="goods_rows[]" value="0">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="shipmentDetail" role="tabpanel">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Phương tiện<span class="text-danger">*</span></label>
                                                    <select class="form-select" name="vehicle_id" required>
                                                        <option value="">Chọn phương tiện</option>
                                                        @foreach($vehicles as $id => $plate)
                                                            <option value="{{ $id }}" {{ old('vehicle_id') == $id ? 'selected' : '' }}>{{ $plate }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('vehicle_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label mb-0">Tài xế/Lơ xe</label>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addPersonBtn">
                                                        <i class="fas fa-plus me-1"></i>Thêm tài xế/lơ xe
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-sm" id="personTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Nhân sự <span class="text-danger">*</span></th>
                                                                @foreach($personDeductionTypes as $type)
                                                                    <th>{{ $type->name }}</th>
                                                                @endforeach
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
                                                                    @foreach($personDeductionTypes as $type)
                                                                        <td>
                                                                            <input type="text" name="drivers[{{ $i }}][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0" value="{{ old('drivers.'.$i.'.deductions.'.$type->id, $driver['deductions'][$type->id] ?? '') }}">
                                                                            @error('drivers.'.$i.'.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                    @endforeach
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
                                                                @foreach($personDeductionTypes as $type)
                                                                    <td>
                                                                        <input type="text" name="drivers[0][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0">
                                                                        @error('drivers.0.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                @endforeach
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
                                            <script>
                                                
                                            </script>
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
    
    // Khai báo các loại khấu trừ cho tài xế
    const personDeductionTypes = [
        @foreach($personDeductionTypes as $type)
            { id: "{{ $type->id }}", name: "{{ $type->name }}" },
        @endforeach
    ];
    
    // Gán danh sách người dùng vào biến toàn cục
    users = {
        @foreach($users as $id => $name)
            "{{ $id }}": "{{ $name }}",
        @endforeach
    };
    
    // Khởi tạo các sự kiện khi trang đã tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo form với số lượng driver ban đầu
        initShipmentForm({{ count(old('drivers', [])) ?: 1 }});
        
        // Thêm event listener cho nút thêm hàng hóa
        document.getElementById('addGoodBtn').onclick = function() {
            goodsCount = addGoodRow(goodsTable, goodsCount);
        };
        
        // Thêm event listener cho nút thêm người
        document.getElementById('addPersonBtn').onclick = function() {
            addDriverRow(personTable, personDeductionTypes, users);
        };
        
        // Kiểm tra và cập nhật trạng thái nút thêm nhân sự dựa trên số lượng người dùng khả dụng
        updateAddPersonButtonState();
        
        // Định dạng tất cả các trường số khi trang được tải
        formatAllNumericInputs();
        
        // Kiểm tra và chuyển đến tab có lỗi nếu có
        handleFormErrors();
        
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
