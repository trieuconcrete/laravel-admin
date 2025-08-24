@extends('admin.layout')
@section('title', 'Chỉnh sửa nhật ký hành trình thuê xe')
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
            <form action="{{ route('admin.car-rental.update-vehicle-log', $shipment->id) }}" method="POST" enctype="multipart/form-data" id="shipmentForm">
                @csrf
                @method('PUT')
                <!-- Hidden fields -->
                <input type="hidden" name="customer_id" value="{{ $shipment->customer_id }}">
                <input type="hidden" name="car_rental_id" value="{{ $shipment->car_rental_id }}">
                
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
                                                <i class="ri-save-3-line align-middle me-1"></i>Cập nhật 
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
                                                    <input type="date" class="form-control" name="run_date" id="run_date" value="{{ old('run_date', $shipment->run_date ? $shipment->run_date->format('d/m/Y') : ($shipment->departure_time ? $shipment->departure_time->format('d/m/Y') : date('Y-m-d'))) }}" required>
                                                    @error('run_date')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control" name="start_time" id="start_time" value="{{ old('start_time', $shipment->start_time ?? ($shipment->departure_time ? $shipment->departure_time->format('H:i') : '08:00')) }}" required inputmode="numeric" style="cursor:pointer;">
                                                    @error('start_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control" name="end_time" id="end_time" value="{{ old('end_time', $shipment->end_time ?? ($shipment->estimated_arrival_time ? $shipment->estimated_arrival_time->format('H:i') : '17:30')) }}" required inputmode="numeric" style="cursor:pointer;">
                                                    @error('end_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Vị trí đi <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="start_location" required id="start_location" value="{{ old('start_location', $shipment->start_location ?? $shipment->origin ?? 'Điểm bắt đầu') }}">
                                                    @error('start_location')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Vị trí đến <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="end_location" required id="end_location" value="{{ old('end_location', $shipment->end_location ?? $shipment->destination ?? 'Điểm kết thúc') }}">
                                                    @error('end_location')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Km bắt đầu <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control odometer-input" name="start_odometer" id="start_odometer" value="{{ old('start_odometer', $shipment->start_odometer ?? $shipment->distance ?? '0') }}" required>
                                                    @error('start_odometer')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Km kết thúc <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control odometer-input" name="end_odometer" id="end_odometer" value="{{ old('end_odometer', $shipment->end_odometer ?? ($shipment->start_odometer + $shipment->distance ?? 0)) }}" required>
                                                    @error('end_odometer')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="status" required>
                                                        @foreach($shipmentStatus as $key => $value)
                                                            <option value="{{ $key }}" {{ old('status', $shipment->status ?? 'completed') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for=""></label>
                                                    <div class="form-check form-check-secondary mb-3">
                                                        <input class="form-check-input" 
                                                        name="is_overtime_at_noon" 
                                                        type="checkbox" 
                                                        value="1" 
                                                        id="is_overtime_at_noon"
                                                        {{ old('is_overtime_at_noon', $shipment->is_overtime_at_noon ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_overtime_at_noon">
                                                            Có tăng ca trưa
                                                        </label>
                                                    </div>
                                                    @error('is_overtime_at_noon')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Đơn giá tăng ca (VNĐ/giờ) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="overtime_rate" id="overtime_rate" value="{{ old('overtime_rate', number_format($shipment->overtime_rate ?? 50000)) }}" required>
                                                    <small class="text-muted">Đơn giá tăng ca cho tài xế</small>
                                                    @error('overtime_rate')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Phí đậu xe</label>
                                                    <input type="text" class="form-control number" name="parking_fee" value="{{ old('parking_fee', $shipment->parking_fee ?? '0') }}">
                                                    @error('parking_fee')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Kết quả tính OT</label>
                                                    <div class="bg-light p-3 rounded">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <small class="text-muted">Số giờ OT:</small><br>
                                                                <strong id="overtime_hours_display">{{ number_format($shipment->overtime_hours ?? 0, 2) }} giờ</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">Tổng chi phí OT:</small><br>
                                                                <strong id="total_overtime_cost_display">{{ number_format($shipment->total_overtime_cost ?? 0) }} VNĐ</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    OT được tính từ giờ làm việc: <span id="start_working_hour_display">{{ $carRental->start_working_hour ?? '07:00' }}</span> - <span id="end_working_hour_display">{{ $carRental->end_working_hour ?? '17:30' }}</span>
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
                                                                <small class="text-muted">OT buổi sáng:</small><br>
                                                                <strong id="morning_overtime_display">0.00 giờ</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">OT buổi chiều:</small><br>
                                                                <strong id="afternoon_overtime_display">0.00 giờ</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-6">
                                                                <small class="text-muted">Tăng ca trưa:</small><br>
                                                                <strong id="noon_overtime_display">{{ $shipment->is_overtime_at_noon ? '1.00' : '0.00' }} giờ</strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">Tổng OT:</small><br>
                                                                <strong id="total_overtime_display">{{ number_format($shipment->overtime_hours ?? 0, 2) }} giờ</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calculator me-1"></i>
                                                                    Tổng OT = OT buổi sáng + OT buổi chiều + Tăng ca trưa
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <input type="hidden" name="calculated_overtime_hours" value="{{ $shipment->overtime_hours ?? 0 }}">
                                                    <input type="hidden" name="calculated_total_overtime_cost" value="{{ $shipment->total_overtime_cost ?? 0 }}">
                                                    <input type="hidden" name="morning_overtime_hours" value="0">
                                                    <input type="hidden" name="afternoon_overtime_hours" value="0">
                                                    <input type="hidden" name="noon_overtime_hours" value="{{ $shipment->is_overtime_at_noon ? 1 : 0 }}">
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
                                                            @if(count($shipment->tollFees) > 0)
                                                                @foreach($shipment->tollFees as $i => $tollFee)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" name="toll_fees[{{ $i }}][station_name]" class="form-control form-control-sm" value="{{ old('toll_fees.'.$i.'.station_name', $tollFee->station_name) }}" placeholder="Nhập tên trạm">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="toll_fees[{{ $i }}][transaction_code]" class="form-control form-control-sm" value="{{ old('toll_fees.'.$i.'.transaction_code', $tollFee->transaction_code) }}" placeholder="Nhập mã giao dịch">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="toll_fees[{{ $i }}][fee_amount]" class="form-control form-control-sm toll-fee-amount" value="{{ old('toll_fees.'.$i.'.fee_amount', $tollFee->fee_amount) }}" placeholder="Nhập số tiền">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="toll_fees[{{ $i }}][notes]" class="form-control form-control-sm" value="{{ old('toll_fees.'.$i.'.notes', $tollFee->notes) }}" placeholder="Nhập ghi chú">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTollFeeRow(this, {{ $i }})"><i class="ri-delete-bin-fill"></i></button>
                                                                            <input type="hidden" name="toll_fee_rows[]" value="{{ $i }}">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" name="toll_fees[0][station_name]" class="form-control form-control-sm" placeholder="Nhập tên trạm">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="toll_fees[0][transaction_code]" class="form-control form-control-sm" placeholder="Nhập mã giao dịch">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="toll_fees[0][fee_amount]" class="form-control form-control-sm toll-fee-amount" placeholder="Nhập số tiền">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="toll_fees[0][notes]" class="form-control form-control-sm" placeholder="Nhập ghi chú">
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden" name="toll_fee_rows[]" value="0">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Phí cân xe</label>
                                                    <input type="text" class="form-control number" name="weighing_fee" value="{{ old('weighing_fee',$shipment->weighing_fee) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Phụ phí test</label>
                                                    <input type="text" class="form-control number" name="testing_surcharge" value="{{ old('testing_surcharge',$shipment->testing_surcharge) }}">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Ghi chú</label>
                                                <textarea class="form-control" name="notes" rows="3" placeholder="Nhập ghi chú">{!! old('notes', $shipment->notes ?? 'Thuê xe - ' . ($shipment->cargo_description ?? 'Dịch vụ thuê xe')) !!}</textarea>
                                                @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
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
                                                        id="is_car_rental"
                                                        {{ old('is_car_rental', $shipment->is_car_rental ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_car_rental">
                                                            Xe HPL Thuê
                                                        </label>
                                                    </div>
                                                    <!-- Hidden input để luôn gửi giá trị is_car_rental -->
                                                    <input type="hidden" name="is_car_rental_value" id="is_car_rental_value" value="{{ old('is_car_rental', $shipment->is_car_rental ?? true) ? '1' : '0' }}">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">Phương tiện<span class="text-danger">*</span></label>
                                                    <select class="form-select" name="vehicle_id" id="vehicles">
                                                        <option value="">Chọn phương tiện</option>
                                                        @foreach($vehicles as $vehicle)
                                                            <option value="{{ (int)$vehicle->vehicle_id }}" {{ old('vehicle_id', $shipment->vehicle_id ?? $shipment->vehicle->vehicle_id ?? '') == (int)$vehicle->vehicle_id ? 'selected' : '' }}>{{ $vehicle->plate_number . '-' . $vehicle->vehicleType->name }}{{ $vehicle->is_car_rental ? ' (Thuê)' : '' }}</option>
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
                                                                            <input type="text" name="drivers.0.deductions.{{ $type->id }}" class="form-control form-control-sm deduction-input" min="0">
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
                                                                    <th>Nhân sự</th>
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
                                                                            <select name="driverPXs[{{ $i }}][user_id]" class="form-select form-select-sm" style="min-width: 180px;">
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
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriverPXRow(this, {{ $i }})"><i class="ri-delete-bin-fill"></i></button>
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

                                            <!-- Chi phí chuyến xe - chỉ hiển thị khi is_car_rental = true -->
                                            <div id="carRentalCosts" class="mb-3" style="display: {{ $shipment->is_car_rental ? 'block' : 'none' }};">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Giá chuyến <span class="text-danger">*</span></label>
                                                        <small class="text-muted">Chi phí HPL trả cho đối tác cho thuê xe</small>
                                                        <input type="text" class="form-control unit-input number" placeholder="Nhập giá chuyến" name="unit_price_for_car_rental" value="{{ old('unit_price_for_car_rental', $shipment->unit_price_for_car_rental ?? '') }}">
                                                        @error('unit_price_for_car_rental')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <label class="form-label fs-5">Chi phí chuyến xe</label> 
                                                    <small class="text-muted">Chi phí HPL trả cho đối tác cho thuê xe</small>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    @foreach($carRentalDeductionTypes ?? [] as $type)
                                                                        <th>{{ $type->name }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    @foreach($carRentalDeductionTypes ?? [] as $type)
                                                                        <td>
                                                                            <input type="hidden" name="deduction_type_ids[]" value="{{ $type->id }}">
                                                                            @if($type->name === 'Ghi chú')
                                                                                <textarea class="form-control form-control-sm" name="deductions[{{ $type->id }}]" rows="3" placeholder="Nhập ghi chú...">{{ old('deductions.'.$type->id, $carRentalDeductions->where('shipment_deduction_type_id', $type->id)->first()->amount ?? '') }}</textarea>
                                                                            @else
                                                                                <input type="text" class="form-control form-control-sm deduction-input number" name="deductions[{{ $type->id }}]" min="0" value="{{ old('deductions.'.$type->id, $carRentalDeductions->where('shipment_deduction_type_id', $type->id)->first()->amount ?? '') }}">
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
        
        // Set vehicles
        window.vehicles = [
            @foreach($vehicles as $vehicle)
                {
                    vehicle_id: {{ $vehicle->vehicle_id }},
                    plate_number: '{{ addslashes($vehicle->plate_number) }}',
                    is_car_rental: {{ $vehicle->is_car_rental ? 'true' : 'false' }},
                    vehicleType: {
                        name: '{{ addslashes($vehicle->vehicleType->name) }}'
                    }
                },
            @endforeach
        ];
        
        // Set goodsCount
        window.goodsCount = {{ count(old('goods', [])) ?: 1 }};
        
        // Set laravelOld
        window.laravelOld = @json(session()->getOldInput());
        
        console.log('Car rental form data initialized:', {
            personDeductionTypes: window.personDeductionTypes,
            personPxDeductionTypes: window.personPxDeductionTypes,
            users: window.users,
            userPXs: window.userPXs,
            vehicles: window.vehicles,
            goodsCount: window.goodsCount
        });
        
        // Khởi tạo tollFeeRowIndex từ số toll fees hiện có để tránh ghi đè
        const existingTollFees = {{ count($shipment->tollFees) }};
        if (typeof window.tollFeeRowIndex !== 'undefined') {
            window.tollFeeRowIndex = Math.max(window.tollFeeRowIndex, existingTollFees);
        } else {
            window.tollFeeRowIndex = existingTollFees;
        }
        
        console.log('Initialized tollFeeRowIndex:', window.tollFeeRowIndex, 'Existing toll fees:', existingTollFees);
        
        // Xử lý checkbox is_car_rental khi trang load
        const isCarRentalCheckbox = document.querySelector('input[name="is_car_rental"]');
        console.log('Checkbox is_car_rental found:', isCarRentalCheckbox);
        console.log('Checkbox checked state:', isCarRentalCheckbox ? isCarRentalCheckbox.checked : 'not found');
        
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
            
            // Hiển thị phần chi phí chuyến xe
            const carRentalCosts = document.getElementById('carRentalCosts');
            console.log('carRentalCosts element found:', carRentalCosts);
            if (carRentalCosts) {
                carRentalCosts.style.display = 'block';
                console.log('carRentalCosts display set to block');
            }
            
            // Cập nhật danh sách xe chỉ hiển thị xe thuê
            if (window.vehicles && Array.isArray(window.vehicles)) {
                // Gọi function từ car-rental-form.js
                if (typeof updateVehicleList === 'function') {
                    updateVehicleList(true);
                }
            }
        }
        
        // Thêm event listener cho checkbox is_car_rental
        if (isCarRentalCheckbox) {
            isCarRentalCheckbox.addEventListener('change', function() {
                const driverSection = document.getElementById('drivers');
                const carRentalCosts = document.getElementById('carRentalCosts');
                const isCarRentalValue = document.getElementById('is_car_rental_value');
                
                if (this.checked) {
                    // Xe HPL thuê - ẩn phần tài xế, hiển thị chi phí chuyến xe
                    if (driverSection) {
                        driverSection.style.display = 'none';
                        const driverFields = driverSection.querySelectorAll('select[name*="[user_id]"], input[name*="[allowance]"], input[name*="[deduction]"]');
                        driverFields.forEach(field => {
                            field.removeAttribute('required');
                            field.disabled = true;
                        });
                    }
                    
                    if (carRentalCosts) {
                        carRentalCosts.style.display = 'block';
                    }
                    
                    if (isCarRentalValue) {
                        isCarRentalValue.value = '1';
                    }
                } else {
                    // Xe thường - hiển thị phần tài xế, ẩn chi phí chuyến xe
                    if (driverSection) {
                        driverSection.style.display = 'block';
                        const driverFields = driverSection.querySelectorAll('select[name*="[user_id]"]');
                        driverFields.forEach(field => {
                            field.setAttribute('required', 'required');
                            field.disabled = false;
                        });
                    }
                    
                    if (carRentalCosts) {
                        carRentalCosts.style.display = 'none';
                    }
                    
                    if (isCarRentalValue) {
                        isCarRentalValue.value = '0';
                    }
                }
            });
        }
        
        // Function để tính toán và hiển thị OT
        function calculateAndDisplayOT() {
            const startTime = document.getElementById('start_time')?.value;
            const endTime = document.getElementById('end_time')?.value;
            const overtimeRateElement = document.getElementById('overtime_rate');
            const overtimeRate = overtimeRateElement ? overtimeRateElement.value.replace(/,/g, '') || 50000 : 50000;
            const isOvertimeAtNoonElement = document.getElementById('is_overtime_at_noon');
            const isOvertimeAtNoon = isOvertimeAtNoonElement ? isOvertimeAtNoonElement.checked : false;
            
            console.log('calculateAndDisplayOT called with:', { startTime, endTime, overtimeRate, isOvertimeAtNoon });
            
            // Lấy start_working_hour và end_working_hour từ car rental
            const startWorkingHour = '{{ $carRental->start_working_hour ? \Carbon\Carbon::parse($carRental->start_working_hour)->format('H:i') : "07:00" }}';
            const endWorkingHour = '{{ $carRental->end_working_hour ? \Carbon\Carbon::parse($carRental->end_working_hour)->format('H:i') : "17:30" }}';
            
            console.log('Working hours:', { startWorkingHour, endWorkingHour });
            
            // Cập nhật hiển thị working hours (format H:i)
            const startWorkingHourDisplay = document.getElementById('start_working_hour_display');
            const endWorkingHourDisplay = document.getElementById('end_working_hour_display');
            
            if (startWorkingHourDisplay) {
                const formattedStartTime = startWorkingHour.split(':').slice(0, 2).join(':');
                startWorkingHourDisplay.textContent = formattedStartTime;
            }
            
            if (endWorkingHourDisplay) {
                const formattedEndTime = endWorkingHour.split(':').slice(0, 2).join(':');
                endWorkingHourDisplay.textContent = formattedEndTime;
            }
            
            let overtimeHours = 0;
            let morningOvertime = 0;
            let afternoonOvertime = 0;
            
            // Tính OT buổi sáng (chỉ cần start_time)
            if (startTime) {
                // So sánh trực tiếp thời gian (HH:MM) không cần ngày
                if (startTime < startWorkingHour) {
                    // Chuyển đổi thời gian thành phút để tính toán chính xác
                    const startMinutes = parseInt(startTime.split(':')[0]) * 60 + parseInt(startTime.split(':')[1]);
                    const startWorkingMinutes = parseInt(startWorkingHour.split(':')[0]) * 60 + parseInt(startWorkingHour.split(':')[1]);
                    
                    morningOvertime = (startWorkingMinutes - startMinutes) / 60; // Convert to hours
                    overtimeHours += morningOvertime;
                    console.log('Morning OT calculated:', morningOvertime);
                }
            }
            
            // Tính OT buổi chiều (chỉ cần end_time)
            if (endTime) {
                // So sánh trực tiếp thời gian (HH:MM) không cần ngày
                if (endTime > endWorkingHour) {
                    // Chuyển đổi thời gian thành phút để tính toán chính xác
                    const endMinutes = parseInt(endTime.split(':')[0]) * 60 + parseInt(endTime.split(':')[1]);
                    const endWorkingMinutes = parseInt(endWorkingHour.split(':')[0]) * 60 + parseInt(endWorkingHour.split(':')[1]);
                    
                    afternoonOvertime = (endMinutes - endWorkingMinutes) / 60; // Convert to hours
                    overtimeHours += afternoonOvertime;
                    console.log('Afternoon OT calculated:', afternoonOvertime);
                }
            }
            
            // Thêm tăng ca trưa (không phụ thuộc vào thời gian)
            if (isOvertimeAtNoon) {
                overtimeHours += 1;
                console.log('Noon OT added: 1 hour');
            }
            
            const totalOvertimeCost = overtimeHours * overtimeRate;
            
            console.log('OT calculation results:', {
                morningOvertime,
                afternoonOvertime,
                isOvertimeAtNoon,
                overtimeHours,
                totalOvertimeCost
            });
            
            // Cập nhật hiển thị
            const overtimeHoursDisplay = document.getElementById('overtime_hours_display');
            const totalOvertimeCostDisplay = document.getElementById('total_overtime_cost_display');
            const morningOvertimeDisplay = document.getElementById('morning_overtime_display');
            const afternoonOvertimeDisplay = document.getElementById('afternoon_overtime_display');
            const noonOvertimeDisplay = document.getElementById('noon_overtime_display');
            const totalOvertimeDisplay = document.getElementById('total_overtime_display');
            
            if (overtimeHoursDisplay) overtimeHoursDisplay.textContent = overtimeHours.toFixed(2) + ' giờ';
            if (totalOvertimeCostDisplay) totalOvertimeCostDisplay.textContent = totalOvertimeCost.toLocaleString() + ' VNĐ';
            if (morningOvertimeDisplay) morningOvertimeDisplay.textContent = morningOvertime.toFixed(2) + ' giờ';
            if (afternoonOvertimeDisplay) afternoonOvertimeDisplay.textContent = afternoonOvertime.toFixed(2) + ' giờ';
            if (noonOvertimeDisplay) noonOvertimeDisplay.textContent = (isOvertimeAtNoon ? 1 : 0).toFixed(2) + ' giờ';
            if (totalOvertimeDisplay) totalOvertimeDisplay.textContent = overtimeHours.toFixed(2) + ' giờ';
            
            // Cập nhật hidden fields
            const calculatedOvertimeHours = document.querySelector('input[name="calculated_overtime_hours"]');
            const calculatedTotalOvertimeCost = document.querySelector('input[name="calculated_total_overtime_cost"]');
            const morningOvertimeHours = document.querySelector('input[name="morning_overtime_hours"]');
            const afternoonOvertimeHours = document.querySelector('input[name="afternoon_overtime_hours"]');
            const noonOvertimeHours = document.querySelector('input[name="noon_overtime_hours"]');
            
            if (calculatedOvertimeHours) calculatedOvertimeHours.value = overtimeHours;
            if (calculatedTotalOvertimeCost) calculatedTotalOvertimeCost.value = totalOvertimeCost;
            if (morningOvertimeHours) morningOvertimeHours.value = morningOvertime;
            if (afternoonOvertimeHours) afternoonOvertimeHours.value = afternoonOvertime;
            if (noonOvertimeHours) noonOvertimeHours.value = isOvertimeAtNoon ? 1 : 0;
            
            console.log('Display updated successfully');
        }
        
        // Thêm event listeners cho các trường thời gian
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const overtimeRateInput = document.getElementById('overtime_rate');
        const isOvertimeAtNoonInput = document.getElementById('is_overtime_at_noon');
        
        if (startTimeInput) {
            startTimeInput.addEventListener('change', calculateAndDisplayOT);
            startTimeInput.addEventListener('input', calculateAndDisplayOT);
            console.log('Event listener added to start_time');
        }
        
        if (endTimeInput) {
            endTimeInput.addEventListener('change', calculateAndDisplayOT);
            endTimeInput.addEventListener('input', calculateAndDisplayOT);
            console.log('Event listener added to end_time');
        }
        
        if (overtimeRateInput) {
            overtimeRateInput.addEventListener('input', calculateAndDisplayOT);
            console.log('Event listener added to overtime_rate');
        }
        
        if (isOvertimeAtNoonInput) {
            isOvertimeAtNoonInput.addEventListener('change', calculateAndDisplayOT);
            console.log('Event listener added to is_overtime_at_noon');
        }
        
        // Tính toán ban đầu
        calculateAndDisplayOT();
        
        // Format thời gian về H:i trước khi submit form
        document.getElementById('shipmentForm').addEventListener('submit', function(e) {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            
            // Format start_time về H:i nếu có giây
            if (startTimeInput && startTimeInput.value) {
                const startTime = startTimeInput.value;
                if (startTime.length > 5) {
                    startTimeInput.value = startTime.substring(0, 5);
                }
            }
            
            // Format end_time về H:i nếu có giây
            if (endTimeInput && endTimeInput.value) {
                const endTime = endTimeInput.value;
                if (endTime.length > 5) {
                    endTimeInput.value = endTime.substring(0, 5);
                }
            }
        });
        
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
        
        // Format deduction inputs and unit inputs on keyup
        $('.deduction-input, .unit-input').on('input', function () {
            formatPriceInput($(this));
        });
        
        // Format odometer inputs on keyup
        $('.odometer-input').on('input', function () {
            formatOdometerInput($(this));
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
