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
            <form action="{{ route('admin.shipments.update', $shipment) }}" method="POST" enctype="multipart/form-data" id="shipmentForm">
                @method('PUT')
                @csrf
                <div class="row mb-3 pb-1">
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                <div class="flex-grow-1">
                                    <h4 class="fs-16 mb-1">Chi tiết chuyến hàng</h4>
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
                                                            <option value="{{ $id }}" {{ old('customer_id', $shipment->customer_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="status" required>
                                                        @foreach($shipmentStatus as $key => $value)
                                                            <option value="{{ $key }}" {{ old('status', $shipment->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                @php
                                                $departure_time = old('departure_time', optional($shipment->departure_time)->format('Y-m-d'));
                                                $estimated_arrival_time = old('estimated_arrival_time', optional($shipment->estimated_arrival_time)->format('Y-m-d'));
                                                @endphp
                                                <div class="col-md-6">
                                                    <label class="form-label">Thời gian khởi hành<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="departure_time"
                                                           value="{{ $departure_time }}">
                                                    @error('departure_time')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Thời gian dự kiến đến<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="estimated_arrival_time"
                                                    value="{{ $estimated_arrival_time }}">
                                                    @error('estimated_arrival_time')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Điểm khởi hành <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="Nhập điểm khởi hành" name="origin" value="{{ old('origin', $shipment->origin) }}" required>
                                                    @error('origin')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Điểm đến<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="Nhập điểm đến" name="destination" value="{{ old('destination', $shipment->destination) }}" required>
                                                    @error('destination')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Số KM</label>
                                                    <input type="text" class="form-control" placeholder="Nhập số KM" name="distance" value="{{ old('distance', $shipment->distance) }}">
                                                    @error('distance')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ghi chú</label>
                                                <textarea class="form-control" rows="2" placeholder="Nhập ghi chú" name="notes">{!! old('notes', $shipment->notes) !!}</textarea>
                                                @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
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
                                                                        <input type="number" class="form-control form-control-sm" name="deductions[{{ $type->id }}]" min="0" value="{{ old('deductions.'.$type->id, isset($shipmentDeductions[$type->id]) ? $shipmentDeductions[$type->id]->amount : '') }}">
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
                                                                <th>Giá trị (VNĐ) <span class="text-danger">*</span></th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(count($shipment->goods) > 0)
                                                                @foreach($shipment->goods as $i => $good)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="hidden" name="goods_index[]" value="{{ $i }}">
                                                                            <input type="text" name="goods[{{ $i }}][name]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.name', $good->name) }}" required>
                                                                            @error('goods.'.$i.'.name')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][notes]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.notes', $good->notes) }}">
                                                                            @error('goods.'.$i.'.notes')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="goods[{{ $i }}][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.'.$i.'.quantity', $good->quantity) }}" required>
                                                                            @error('goods.'.$i.'.quantity')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="goods[{{ $i }}][weight]" class="form-control form-control-sm" min="0" value="{{ old('goods.'.$i.'.weight', $good->weight) }}">
                                                                            @error('goods.'.$i.'.weight')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][unit]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.unit', $good->unit) }}">
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
                                                                        <input type="hidden" name="goods_index[]" value="0">
                                                                        <input type="text" name="goods[0][name]" class="form-control form-control-sm" value="{{ old('goods.0.name') }}" required>
                                                                        @error('goods.0.name')<span class="text-danger">{{ $message }}</span>@enderror
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][notes]" class="form-control form-control-sm" value="{{ old('goods.0.notes') }}">
                                                                        @error('goods.0.notes')<span class="text-danger">{{ $message }}</span>@enderror
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="goods[0][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.0.quantity') }}" required>
                                                                        @error('goods.0.quantity')<span class="text-danger">{{ $message }}</span>@enderror
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="goods[0][weight]" class="form-control form-control-sm" min="0" value="{{ old('goods.0.weight') }}">
                                                                        @error('goods.0.weight')<span class="text-danger">{{ $message }}</span>@enderror
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="goods[0][unit]" class="form-control form-control-sm" min="0" value="{{ old('goods.0.unit') }}">
                                                                        @error('goods.0.unit')<span class="text-danger">{{ $message }}</span>@enderror
                                                                    </td>
                                                                    <td></td>
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
                                                            <option value="{{ $id }}" {{ old('vehicle_id', $shipment->vehicle_id) == $id ? 'selected' : '' }}>{{ $plate }}</option>
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
                                                            $driversArray = [];
                                                            foreach($driverDeductions as $userId => $deductions) {
                                                                $driversArray[] = [
                                                                    'user_id' => $userId,
                                                                    'deductions' => $deductions->keyBy('shipment_deduction_type_id')
                                                                ];
                                                            }
                                                        @endphp
                                                        
                                                        @if(count($driversArray) > 0)
                                                            @foreach($driversArray as $i => $driver)
                                                                <tr>
                                                                    <td>
                                                                        <select name="drivers[{{ $i }}][user_id]" class="form-select form-select-sm" required>
                                                                            <option value="">Chọn nhân sự</option>
                                                                            @foreach($users as $id => $name)
                                                                                <option value="{{ $id }}" {{ old('drivers.'.$i.'.user_id', $driver['user_id']) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('drivers.'.$i.'.user_id')<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                    @foreach($personDeductionTypes as $type)
                                                                        <td>
                                                                            <input type="number" name="drivers[{{ $i }}][deductions][{{ $type->id }}]" class="form-control form-control-sm" min="0" 
                                                                                value="{{ old('drivers.'.$i.'.deductions.'.$type->id, isset($driver['deductions'][$type->id]) ? $driver['deductions'][$type->id]->amount : '') }}">
                                                                            @error('drivers.'.$i.'.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                    @endforeach
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ri-delete-bin-fill"></i></button>
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
                                                                        <input type="number" name="drivers[0][deductions][{{ $type->id }}]" class="form-control form-control-sm" min="0">
                                                                        @error('drivers.0.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                @endforeach
                                                                <td></td>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const goodsTable = document.querySelector('#goodsTable tbody');
        let goodsCount = {{ count(old('goods', [])) ?: 1 }};
        
        // Định dạng tất cả các trường số khi trang được tải
        formatAllNumericInputs();
        
        document.getElementById('addGoodBtn').onclick = function() {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="hidden" name="goods_index[]" value="${goodsCount}">
                    <input type="text" name="goods[${goodsCount}][name]" class="form-control form-control-sm" required>
                    <div class="text-danger goods-error" data-field="goods.${goodsCount}.name"></div>
                </td>
                <td>
                    <input type="text" name="goods[${goodsCount}][notes]" class="form-control form-control-sm">
                    <div class="text-danger goods-error" data-field="goods.${goodsCount}.notes"></div>
                </td>
                <td>
                    <input type="number" name="goods[${goodsCount}][quantity]" class="form-control form-control-sm" min="1" required>
                    <div class="text-danger goods-error" data-field="goods.${goodsCount}.quantity"></div>
                </td>
                <td>
                    <input type="number" name="goods[${goodsCount}][weight]" class="form-control form-control-sm numeric-input" min="0" required>
                    <div class="text-danger goods-error" data-field="goods.${goodsCount}.weight"></div>
                </td>
                <td>
                    <input type="number" name="goods[${goodsCount}][unit]" class="form-control form-control-sm numeric-input" min="0" required>
                    <div class="text-danger goods-error" data-field="goods.${goodsCount}.unit"></div>
                </td>
                <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ri-delete-bin-fill"></i></button></td>
            `;
            goodsTable.appendChild(row);
            
            // Thêm event listener cho các trường số mới thêm vào
            const newRow = goodsTable.lastElementChild;
            addNumericInputListeners(newRow.querySelectorAll('input[type="number"]'));
            
            goodsCount++;
        };

        const personTable = document.querySelector('#personTable tbody');
        document.getElementById('addPersonBtn').onclick = function() {
            let deductionInputs = '';
            @foreach($personDeductionTypes as $type)
                deductionInputs += `<td>
                    <input type=\"hidden\" name=\"drivers[][deduction_type_ids][]\" value=\"{{ $type->id }}\">
                    <input type=\"number\" name=\"drivers[][deductions][{{ $type->id }}]\" class=\"form-control form-control-sm numeric-input\" min=\"0\">
                </td>`;
            @endforeach
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="drivers[][user_id]" class="form-select form-select-sm" required>
                        <option value="">Chọn nhân sự</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </td>
                ${deductionInputs}
                <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="ri-delete-bin-fill"></i></button></td>
            `;
            personTable.appendChild(row);
            
            // Thêm event listener cho các trường số mới thêm vào
            const newRow = personTable.lastElementChild;
            addNumericInputListeners(newRow.querySelectorAll('input[type="number"]'));
        };
        
        // Sử dụng nút submit để kiểm tra và hiển thị thông báo lỗi
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // 1. Ưu tiên kiểm tra các trường ở tab thông tin vận chuyển trước
            const customerId = document.querySelector('select[name="customer_id"]')?.value;
            const origin = document.querySelector('input[name="origin"]')?.value;
            const destination = document.querySelector('input[name="destination"]')?.value;
            const departureTime = document.querySelector('input[name="departure_time"]')?.value;
            const estimatedArrivalTime = document.querySelector('input[name="estimated_arrival_time"]')?.value;
            
            // Kiểm tra xem có ít nhất một hàng hóa hay không
            const goodsNameInputs = document.querySelectorAll('input[name^="goods["][name$="][name]"]');
            const hasGoods = goodsNameInputs.length > 0 && Array.from(goodsNameInputs).some(input => input.value.trim() !== '');
            
            // Kiểm tra các trường ở tab thông tin vận chuyển
            if (!customerId || !origin || !destination || !departureTime || !estimatedArrivalTime || !hasGoods) {
                let errorMessage = '';
                let errorField = null;
                let tabId = 'driverAllowance'; // ID của tab thông tin vận chuyển
                
                if (!customerId) {
                    errorMessage = 'Vui lòng chọn khách hàng!';
                    errorField = 'select[name="customer_id"]';
                } else if (!origin) {
                    errorMessage = 'Vui lòng nhập điểm khởi hành!';
                    errorField = 'input[name="origin"]';
                } else if (!destination) {
                    errorMessage = 'Vui lòng nhập điểm đến!';
                    errorField = 'input[name="destination"]';
                } else if (!departureTime) {
                    errorMessage = 'Vui lòng chọn thời gian khởi hành!';
                    errorField = 'input[name="departure_time"]';
                } else if (!estimatedArrivalTime) {
                    errorMessage = 'Vui lòng chọn thời gian dự kiến đến!';
                    errorField = 'input[name="estimated_arrival_time"]';
                } else if (!hasGoods) {
                    errorMessage = 'Vui lòng thêm ít nhất một hàng hóa!';
                    errorField = 'input[name^="goods["][name$="][name]"]';
                }
                
                // Hiển thị thông báo lỗi
                Swal.fire({
                    title: 'Lỗi!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    // Chuyển đến tab thông tin vận chuyển
                    const tabLink = document.querySelector(`a[href="#${tabId}"]`);
                    if (tabLink) {
                        const tab = new bootstrap.Tab(tabLink);
                        tab.show();
                        
                        // Focus vào trường có lỗi
                        setTimeout(() => {
                            const errorElement = document.querySelector(errorField);
                            if (errorElement) {
                                errorElement.focus();
                                errorElement.classList.add('highlight-error');
                                setTimeout(() => {
                                    errorElement.classList.remove('highlight-error');
                                }, 2000);
                            }
                        }, 300);
                    }
                });
                
                return;
            }
            
            // 2. Sau đó mới kiểm tra các trường ở tab phương tiện & tài xế
            const vehicleId = document.querySelector('select[name="vehicle_id"]')?.value;
            const userIdField = document.querySelector('select[name="drivers[0][user_id]"]');
            const userId = userIdField ? userIdField.value : '';
            
            // Nếu vehicle_id hoặc user_id trống, hiển thị thông báo lỗi
            if (!vehicleId || !userId) {
                let errorMessage = '';
                let errorField = null;
                
                if (!vehicleId && !userId) {
                    errorMessage = 'Vui lòng chọn phương tiện và nhân sự!';
                    errorField = 'select[name="vehicle_id"]';
                } else if (!vehicleId) {
                    errorMessage = 'Vui lòng chọn phương tiện!';
                    errorField = 'select[name="vehicle_id"]';
                } else {
                    errorMessage = 'Vui lòng chọn nhân sự!';
                    errorField = 'select[name="drivers[0][user_id]"]';
                }
                
                // Sử dụng SweetAlert2 để hiển thị thông báo
                Swal.fire({
                    title: 'Lỗi!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    // Chuyển đến tab phương tiện & tài xế
                    const vehicleTab = document.querySelector('a[href="#shipmentDetail"]');
                    if (vehicleTab) {
                        const tab = new bootstrap.Tab(vehicleTab);
                        tab.show();
                        
                        // Focus vào trường có lỗi
                        setTimeout(() => {
                            const errorElement = document.querySelector(errorField);
                            if (errorElement) {
                                errorElement.focus();
                                errorElement.classList.add('highlight-error');
                                setTimeout(() => {
                                    errorElement.classList.remove('highlight-error');
                                }, 2000);
                            }
                        }, 300);
                    }
                });
            } else {
                // Định dạng tất cả các trường số trước khi submit
                document.querySelectorAll('input[type="number"]').forEach(input => {
                    if (input.value !== '') {
                        const value = parseFloat(input.value);
                        if (Number.isInteger(value)) {
                            input.value = parseInt(value);
                        }
                    }
                });
                
                // Nếu tất cả đều hợp lệ, submit form
                document.getElementById('shipmentForm').submit();
            }
        });
        
        // Xử lý form trước khi submit để định dạng số
        document.getElementById('shipmentForm').addEventListener('submit', function(e) {
            // Định dạng tất cả các trường số trước khi submit
            document.querySelectorAll('input[type="number"]').forEach(input => {
                if (input.value !== '') {
                    const value = parseFloat(input.value);
                    if (Number.isInteger(value)) {
                        input.value = parseInt(value);
                    }
                }
            });
        });
    });
    
    // Hàm để định dạng tất cả các trường số khi trang được tải
    function formatAllNumericInputs() {
        // Thêm listener cho tất cả các trường số
        const numericInputs = document.querySelectorAll('input[type="number"]');
        addNumericInputListeners(numericInputs);
        
        // Định dạng giá trị ban đầu
        numericInputs.forEach(input => {
            if (input.value !== '') {
                const value = parseFloat(input.value);
                if (Number.isInteger(value)) {
                    input.value = parseInt(value);
                }
            }
        });
    }
    
    // Hàm thêm event listener cho các trường số
    function addNumericInputListeners(inputs) {
        inputs.forEach(input => {
            // Thêm class để dễ quản lý
            input.classList.add('numeric-input');
            
            // Xử lý khi blur (rời khỏi trường nhập)
            input.addEventListener('blur', function() {
                if (this.value !== '') {
                    const value = parseFloat(this.value);
                    if (Number.isInteger(value)) {
                        this.value = parseInt(value);
                    }
                }
            });
        });
    }

    document.getElementById('avatarInput').addEventListener('change', function(event) {
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
