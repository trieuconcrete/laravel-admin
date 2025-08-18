@extends('admin.layout')
@section('title', 'Sửa chuyến')
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
            <form action="{{ route('admin.shipments.update', $shipment) }}" method="POST" enctype="multipart/form-data" id="shipmentForm" data-current-vehicle-id="{{ $shipment->vehicle_id }}">
                @method('PUT')
                @csrf
                <div class="row mb-3 pb-1">
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                <div class="flex-grow-1">
                                    <h4 class="fs-16 mb-1">Chi tiết chuyến xe</h4>
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
                                            <div class="row mb-3">
                                                <label class="form-label fs-5">Loại chuyến xe <span class="text-danger">*</span></label>
                                                <div class="col-md-3">
                                                    <div class="form-check form-radio-primary mb-3">
                                                        <input class="form-check-input" type="radio" name="shipment_type" value="1" id="shipment_type1" @checked(old('shipment_type', $shipment->shipment_type) == 1)>
                                                        <label class="form-check-label" for="shipment_type1">
                                                            Khách chạy theo chuyến
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check form-radio-primary mb-3">
                                                        <input class="form-check-input" type="radio" name="shipment_type" value="3" id="shipment_type3" @checked(old('shipment_type', $shipment->shipment_type) == 3)>
                                                        <label class="form-check-label" for="shipment_type3">
                                                            Xe nâng
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check form-radio-primary mb-3">
                                                        <input class="form-check-input" type="radio" name="shipment_type" value="4" id="shipment_type4" @checked(old('shipment_type', $shipment->shipment_type) == 4)>
                                                        <label class="form-check-label" for="shipment_type4">
                                                            Xe đường dài bắc-nam
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="mb-3 fs-5">Thông tin vận chuyển</h5>
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
                                                $departure_time = old('departure_time', $shipment?->departure_time);
                                                $estimated_arrival_time = old('estimated_arrival_time', $shipment?->estimated_arrival_time);
                                                @endphp
                                                <div class="col-md-3">
                                                    <label class="form-label">Thời gian khởi hành<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="departure_time"
                                                           value="@formatDateForInput($departure_time)">
                                                    @error('departure_time')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Giờ khởi hành</label>
                                                    <input type="time" class="form-control" name="start_time" id="start_time" value="{{ old('start_time', $shipment->start_time) }}" inputmode="numeric" style="cursor:pointer;">
                                                    @error('start_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Thời gian dự kiến đến</label>
                                                    <input type="date" class="form-control" name="estimated_arrival_time"
                                                    value="@formatDateForInput($estimated_arrival_time)">
                                                    @error('estimated_arrival_time')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Giờ đến</label>
                                                    <input type="time" class="form-control" name="end_time" value="{{ old('end_time', $shipment->end_time) }}">
                                                    @error('end_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>



                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Giá chuyến <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control unit-input" placeholder="Nhập giá chuyến" name="unit_price" value="{{ old('unit_price', $shipment->unit_price) }}">
                                                    @error('unit_price')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Số lượng chuyến</label>
                                                    <input type="number" class="form-control" placeholder="Nhập số lượng chuyến" name="trip_count" value="{{ old('trip_count', $shipment->trip_count ?? 1) }}">
                                                    @error('trip_count')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Số KM</label>
                                                    <input type="text" class="form-control" placeholder="Nhập số KM" name="distance" value="{{ old('distance', $shipment->distance) }}">
                                                    @error('distance')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Khối lượng (kg)</label>
                                                    <input type="text" class="form-control" placeholder="Nhập khối lượng" name="cargo_weight" value="{{ old('cargo_weight', $shipment->cargo_weight) }}">
                                                    @error('cargo_weight')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <!-- Additional fields for origin and destination -->
                                            <div class="origin-destination bg-light p-3 mb-2">
                                            <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Điểm đi<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đi" name="origin" value="{{ old('origin', $shipment->origin) }}" required>
                                                        @error('origin')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Công ty</label>
                                                        <input type="text" class="form-control" placeholder="Nhập công ty" name="company" value="{{ old('company', $shipment->company) }}">
                                                        @error('company')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Điểm đến</label>
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đến 1" name="destination" value="{{ old('destination', $shipment->destination) }}">
                                                        @error('destination')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        {{--  <label class="form-label">Điểm đi 2</label>  --}}
                                                        <input hidden type="text" class="form-control" placeholder="Nhập điểm đi 2" name="origin2" value="{{ old('origin2', $shipment->origin2) }}">
                                                        @error('origin2')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" placeholder="Nhập công ty" name="company2" value="{{ old('company2', $shipment->company2) }}">
                                                        @error('company2')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đến 2" name="destination2" value="{{ old('destination2', $shipment->destination2) }}">
                                                        @error('destination2')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        {{--  <label class="form-label">Điểm đi 3</label>  --}}
                                                        <input hidden type="text" class="form-control" placeholder="Nhập điểm đi 3" name="origin3" value="{{ old('origin3', $shipment->origin3) }}">
                                                        @error('origin3')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" placeholder="Nhập công ty" name="company3" value="{{ old('company3', $shipment->company3) }}">
                                                        @error('company3')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đến 3" name="destination3" value="{{ old('destination3', $shipment->destination3) }}">
                                                        @error('destination3')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                                </div>
                                            </div>
                                            <!-- End -->
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                <label class="form-label">Ghi chú</label>
                                                    <textarea class="form-control" rows="2" placeholder="Nhập ghi chú" name="notes">{!! old('notes', $shipment->notes) !!}</textarea>
                                                    @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <label class="form-label">Chi phí chuyến xe</label>
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
                                                                        @if($type->name === 'Ghi chú')
                                                                            <textarea class="form-control form-control-sm" name="deductions[{{ $type->id }}]" rows="3" placeholder="Nhập ghi chú...">{{ old('deductions.'.$type->id, isset($shipmentDeductions[$type->id]) ? $shipmentDeductions[$type->id]->notes : '') }}</textarea>
                                                                        @else
                                                                            <input type="text" class="form-control form-control-sm deduction-input" name="deductions[{{ $type->id }}]" min="0" value="{{ old('deductions.'.$type->id, isset($shipmentDeductions[$type->id]) ? $shipmentDeductions[$type->id]->amount : '') }}">
                                                                        @endif
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
                                                                <th>Tên hàng hóa</th>
                                                                <th>Mô tả</th>
                                                                <th>Số lượng</th>
                                                                <th>Trọng lượng (kg)</th>
                                                                <th>Giá trị (VNĐ)</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(count($shipment->goods) > 0)
                                                                @foreach($shipment->goods as $i => $good)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][name]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.name', $good->name) }}">
                                                                            <div class="text-danger" id="error-goods-{{ $i }}-name">@error('goods.'.$i.'.name'){{ $message }}@enderror</div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][Ghi chú]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.Ghi chú', $good->notes) }}">
                                                                            <div class="text-danger" id="error-goods-{{ $i }}-Ghi chú">@error('goods.'.$i.'.Ghi chú'){{ $message }}@enderror</div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.'.$i.'.quantity', $good->quantity) }}">
                                                                            <div class="text-danger" id="error-goods-{{ $i }}-quantity">@error('goods.'.$i.'.quantity'){{ $message }}@enderror</div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][weight]" class="form-control form-control-sm" min="0" value="{{ old('goods.'.$i.'.weight', $good->weight) }}">
                                                                            <div class="text-danger" id="error-goods-{{ $i }}-weight">@error('goods.'.$i.'.weight'){{ $message }}@enderror</div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][unit]" class="form-control form-control-sm unit-input" value="{{ old('goods.'.$i.'.unit', $good->unit) }}">
                                                                            <div class="text-danger" id="error-goods-{{ $i }}-unit">@error('goods.'.$i.'.unit'){{ $message }}@enderror</div>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGoodRow(this, {{ $i }})"><i class="ri-delete-bin-fill"></i></button>
                                                                            <input type="hidden" name="goods_rows[]" value="{{ $i }}">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" name="goods[0][name]" class="form-control form-control-sm" value="{{ old('goods.0.name') }}">
                                                                        <div class="text-danger" id="error-goods-0-name">@error('goods.0.name'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][Ghi chú]" class="form-control form-control-sm" value="{{ old('goods.0.Ghi chú') }}">
                                                                        <div class="text-danger" id="error-goods-0-Ghi chú">@error('goods.0.Ghi chú'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.0.quantity') }}">
                                                                        <div class="text-danger" id="error-goods-0-quantity">@error('goods.0.quantity'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][weight]" class="form-control form-control-sm" min="0" value="{{ old('goods.0.weight') }}">
                                                                        <div class="text-danger" id="error-goods-0-weight">@error('goods.0.weight'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][unit]" class="form-control form-control-sm unit-input" value="{{ old('goods.0.unit') }}">
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
                                                <div class="col-md-2">
                                                    <div class="form-check form-check-secondary mb-3">
                                                        <input class="form-check-input" name="is_car_rental" @checked($shipment->is_car_rental) type="checkbox" value="1" id="is_car_rental">
                                                        <label class="form-check-label" for="is_car_rental">
                                                            Xe HPL Thuê
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="form-label">Phương tiện<span class="text-danger">*</span></label>
                                                        </div>
                                                    <div class="col-md-7">
                                                        <select class="form-select" name="vehicle_id" id="vehicles">
                                                            <option value="">Chọn phương tiện</option>
                                                            @foreach($vehicles as $vehicle)
                                                                <option value="{{ (int)$vehicle->vehicle_id }}" @selected(old('vehicle_id', $shipment->vehicle_id) == (int)$vehicle->vehicle_id)>{{ $vehicle->plate_number . '-' . $vehicle->vehicleType->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('vehicle_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <!-- Loading spinner (hidden by default) -->
                                                        <div class="spinner-border spinner-border-sm text-primary mt-2" id="vehicle_loading" style="display: none;">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
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
                                                                $driversArray = [];
                                                                foreach($driverDeductions as $userId => $deductions) {
                                                                    // Lấy is_main_driver từ bất kỳ deduction record nào của driver này
                                                                    $isMainDriver = $deductions->first() ? $deductions->first()->is_main_driver : false;
                                                                    $driversArray[] = [
                                                                        'user_id' => $userId,
                                                                        'deductions' => $deductions->keyBy('shipment_deduction_type_id'),
                                                                        'is_main_driver' => $isMainDriver
                                                                    ];
                                                                }
                                                            @endphp
                                                            
                                                            @if(count($driversArray) > 0)
                                                                @foreach($driversArray as $i => $driver)
                                                                    
                                                                    <tr>
                                                                        <td>
                                                                            <select name="drivers[{{ $i }}][user_id]" class="form-select form-select-sm" style="min-width: 180px;" required>
                                                                                <option value="">Chọn nhân sự</option>
                                                                                @foreach($users as $id => $name)
                                                                                    <option value="{{ $id }}" {{ old('drivers.'.$i.'.user_id', $driver['user_id']) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('drivers.'.$i.'.user_id')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <div class="form-check form-switch d-inline-block">
                                                                                <input type="checkbox" name="drivers[{{ $i }}][deductions][is_main_driver]" class="form-check-input deduction-input" 
                                                                                    value="1" 
                                                                                    {{ old('drivers.'.$i.'.deductions.is_main_driver', $driver['is_main_driver']) ? 'checked' : '' }}>
                                                                            </div>
                                                                            @error('drivers.{{ $i }}.deductions.is_main_driver')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        @foreach($personDeductionTypes as $type)
                                                                            <td>
                                                                                <input type="text" name="drivers[{{ $i }}][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0" value="{{ old('drivers.'.$i.'.deductions.'.$type->id, isset($driver['deductions'][$type->id]) ? $driver['deductions'][$type->id]->amount : '') }}">
                                                                                @error('drivers.'.$i.'.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                            </td>
                                                                        @endforeach
                                                                        <td>
                                                                            <input type="text" name="drivers[{{ $i }}][deductions][Ghi chú]" class="form-control form-control-sm " value="{{ old('drivers.'.$i.'.deductions.Ghi chú', $driver['deductions']->first() ? $driver['deductions']->first()->notes : '') }}">
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
                                                                            <input type="checkbox" name="drivers[0][deductions][is_main_driver]" class="form-check-input deduction-input" 
                                                                                value="1" 
                                                                                {{ old('drivers.0.deductions.is_main_driver', false) ? 'checked' : '' }}>
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
                                                                        <input type="text" name="drivers[0][deductions][Ghi chú]" class="form-control form-control-sm " value="{{ old('drivers.0.deductions.Ghi chú', '') }}">
                                                                        @error('drivers.0.deductions.Ghi chú')<div class="text-danger">{{ $message }}</div>@enderror
                                                                    </td>
                                                                    <td>
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
                                                                $driverPXsArray = [];
                                                                foreach($driverPXDeductions as $userId => $deductions) {
                                                                    $driverPXsArray[] = [
                                                                        'user_id' => $userId,
                                                                        'deductions' => $deductions->keyBy('shipment_deduction_type_id')
                                                                    ];
                                                                }
                                                            @endphp
                                                            
                                                            @if(count($driverPXsArray) > 0)
                                                                @foreach($driverPXsArray as $i => $driver)
                                                                    <tr id="driver-row-{{ $i }}">
                                                                        <td>
                                                                            <select name="driverPXs[{{ $i }}][user_id]" class="form-select form-select-sm" style="min-width: 180px;" required>
                                                                                <option value="">Chọn nhân sự</option>
                                                                                @foreach($userPXs as $id => $name)
                                                                                    <option value="{{ $id }}" {{ old('driverPXs.'.$i.'.user_id', $driver['user_id']) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('driverPXs.'.$i.'.user_id')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        @foreach($subPersonDeductionTypes as $type)
                                                                            <td>
                                                                                <input type="text" name="driverPXs[{{ $i }}][deductions][{{ $type->id }}]" class="form-control form-control-sm deduction-input" min="0" value="{{ old('driverPXs.'.$i.'.deductions.'.$type->id, isset($driver['deductions'][$type->id]) ? $driver['deductions'][$type->id]->amount : '') }}">
                                                                                @error('driverPXs.'.$i.'.deductions.'.$type->id)<div class="text-danger">{{ $message }}</div>@enderror
                                                                            </td>
                                                                        @endforeach
                                                                        <td>
                                                                            <input type="text" name="driverPXs[{{ $i }}][deductions][Ghi chú]" class="form-control form-control-sm " value="{{ old('driverPXs.'.$i.'.deductions.Ghi chú', isset($driver['deductions'][$type->id]['Ghi chú']) ? $driver['deductions'][$type->id]['Ghi chú'] : '') }}">
                                                                            @error('driverPXs.{{ $i }}.deductions.Ghi chú')<div class="text-danger">{{ $message }}</div>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverRow(this, {{ $i }})"><i class="ri-delete-bin-fill"></i></button>
                                                                            <input type="hidden" name="driverPX_rows[]" value="{{ $i }}">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="carRental" class="mt-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Giá chuyến <span class="text-danger">*</span></label>
                                                        <small class="text-muted">Chi phí HPL trả cho đối tác cho thuê xe</small>
                                                        <input type="text" class="form-control unit-input" placeholder="Nhập giá chuyến" name="unit_price_for_car_rental" value="{{ old('unit_price_for_car_rental', $shipment->unit_price_for_car_rental) }}">
                                                        @error('unit_price_for_car_rental')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <label class="form-label fs-5">Chi phí chuyến xe</label> <small class="text-muted">Chi phí HPL trả cho đối tác cho thuê xe</small>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    @foreach($carRentalDeductionTypes as $type)
                                                                        <th>{{ $type->name }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    @foreach($carRentalDeductionTypes as $type)
                                                                        <td>
                                                                            <input type="hidden" name="deduction_type_ids[]" value="{{ $type->id }}">
                                                                            @if($type->name === 'Ghi chú')
                                                                                <textarea class="form-control form-control-sm" name="deductions[{{ $type->id }}]" rows="3" placeholder="Nhập ghi chú...">{{ old('deductions.'.$type->id, isset($shipmentDeductions[$type->id]) ? $shipmentDeductions[$type->id]->notes : '') }}</textarea>
                                                                            @else
                                                                                <input type="text" class="form-control form-control-sm deduction-input" name="deductions[{{ $type->id }}]" min="0" value="{{ old('deductions.'.$type->id, isset($shipmentDeductions[$type->id]) ? $shipmentDeductions[$type->id]->amount : '') }}">
                                                                            @endif
                                                                            @error('deductions.'.$type->id)<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
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
            
            // Remove non-numeric characters except commas
            value = value.replace(/[^0-9,]/g, '');
            
            // Remove existing commas to work with clean number
            value = value.replace(/,/g, '');
            
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
                    value = parts[0]; // Keep only integer part
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
    let goodsCount = {{ count(old('goods', $shipment->goods ?? [])) ?: 1 }};
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
    window.users = {};
    @if(!empty($users) && (is_array($users) || $users instanceof \Illuminate\Support\Collection))
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
    // Debug users data
    console.log('Users type:', '{{ gettype($users) }}');
    console.log('Users count:', {{ count($users) }});
    console.log('Users is_array:', {{ is_array($users) ? 'true' : 'false' }});
    console.log('Users is Collection:', {{ $users instanceof \Illuminate\Support\Collection ? 'true' : 'false' }});
    console.log('Available users:', window.users);
    @if(!empty($users))
        console.log('Users data from PHP:', @json($users));
    @endif
    
    // Khởi tạo driverRowCount từ số driver hiện có
    const existingDriverRows = document.querySelectorAll('input[name="driver_rows[]"]');
    if (existingDriverRows.length > 0) {
        // Lấy index lớn nhất từ các driver hiện có
        let maxIndex = 0;
        existingDriverRows.forEach(input => {
            const index = parseInt(input.value);
            if (index > maxIndex) maxIndex = index;
        });
        window.driverRowCount = maxIndex;
        console.log('Set driverRowCount to:', window.driverRowCount);
    } else {
        window.driverRowCount = 0;
    }

    // Form submission debug
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('Form data being submitted:');
        
        // Debug drivers data
        const drivers = document.querySelectorAll('select[name*="drivers"][name*="user_id"]');
        const driverRowInputs = document.querySelectorAll('input[name="driver_rows[]"]');
        
        console.log('Driver rows found:', driverRowInputs.length);
        
        drivers.forEach((select, index) => {
            const userId = select.value;
            const name = select.getAttribute('name');
            const match = name.match(/drivers\[(\d+)\]/);
            const rowIndex = match ? match[1] : index;
            
            const isMainDriverCheckbox = document.querySelector(`input[name="drivers[${rowIndex}][deductions][is_main_driver]"]`);
            const isMainDriver = isMainDriverCheckbox ? isMainDriverCheckbox.checked : false;
            
            console.log(`Driver ${rowIndex}:`, {
                user_id: userId,
                is_main_driver: isMainDriver,
                checkbox_element: isMainDriverCheckbox,
                select_name: name
            });
        });
        
        // Debug driver_rows inputs
        driverRowInputs.forEach((input, index) => {
            console.log(`Driver row input ${index}:`, input.value);
        });
        
        // Debug FormData được gửi
        const formData = new FormData(this);
        console.log('=== FormData Contents ===');
        for (let [key, value] of formData.entries()) {
            if (key.includes('driver')) {
                console.log(`${key}: ${value}`);
            }
        }
        
        // Debug driver_row_indexes
        const driverRowIndexes = Array.from(driverRowInputs).map(input => input.value).join(',');
        console.log('driver_row_indexes:', driverRowIndexes);
        
        const hiddenDriverRowIndexes = document.querySelector('input[name="driver_row_indexes"]');
        console.log('hidden driver_row_indexes:', hiddenDriverRowIndexes ? hiddenDriverRowIndexes.value : 'Not found');
    });
    
    // Khởi tạo các sự kiện khi trang đã tải xong
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo form với số lượng driver ban đầu
        initShipmentForm({{ count(old('drivers', $shipment->drivers ?? [])) }});
        
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
            const currentRows = Array.from(personPxTable.querySelectorAll('tbody tr')).length;
            
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
        const carRentalSection = document.getElementById('carRental');

        function toggleDriverSections() {
            if (!isCarRentalCheckbox || !driverSection) {
                return; // Exit if elements don't exist
            }
            
            const isChecked = isCarRentalCheckbox.checked;
            if (isChecked) {
                // Nếu là xe thuê, ẩn phần tài xế
                driverSection.style.display = 'none';
                carRentalSection.style.display = 'block';
            } else {
                // Nếu không phải xe thuê, hiện phần tài xế
                driverSection.style.display = 'block';
                carRentalSection.style.display = 'none';
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
            if (validateShipmentForm(this)) {
                prepareFormBeforeSubmit(this);
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
