@extends('admin.layout')
@section('title', 'Tạo chuyến xe')
@section('content')

@push('styles')
    <style>
        #personTable th,
        #personTable td {
            white-space: nowrap !important;
        }

        #personTable th.notes-col,
        #personTable td.notes-col {
            width: 350px !important;
            min-width: 350px !important;
        }

        #personTable th.driver-col, #personTable td.driver-col {
            width: 250px !important;
            min-width: 250px !important;
        }

        .form-images .upload {
            display: none
        }

        .image-upload-wrapper {
            border: 1px dashed #405189;
            border-radius: 8px;
            padding: 10px;
            aspect-ratio: 2/1;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .image-upload-wrapper .preview-area {
            width: 100%;
            height: 100%;
            display: none;
        }

        .image-upload-wrapper.has-preview .preview-area {
            display: unset;
        }

        .image-upload-wrapper .upload-message {
            text-align: center;
            color: #405189;
        }

        .image-upload-wrapper .preview-area img {
            width: 100%;
            height: 100%;
            aspect-ratio: 1;
            border-radius: 8px;
            object-fit: cover;
        }
    </style>
@endpush

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
                                                        <input class="form-check-input" type="radio" name="shipment_type" value="1" id="shipment_type1" {{ old('shipment_type', '1') == '1' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="shipment_type1">
                                                            Khách chạy theo chuyến
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check form-radio-primary mb-3">
                                                        <input class="form-check-input" type="radio" name="shipment_type" value="3" id="shipment_type3" {{ old('shipment_type') == '3' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="shipment_type3">
                                                            Xe nâng
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check form-radio-primary mb-3">
                                                        <input class="form-check-input" type="radio" name="shipment_type" value="4" id="shipment_type4" {{ old('shipment_type') == '4' ? 'checked' : '' }}>
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
                                                    @if (request('customer_id'))
                                                        <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                                                    @endif
                                                    <select class="form-select js-example-basic-single" name="customer_id" required @disabled(request('customer_id'))>
                                                        <option value="">Chọn khách hàng</option>
                                                        @foreach($customers as $id => $name)
                                                            <option value="{{ $id }}" @selected($id == request('customer_id'))>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('customer_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
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
                                            </div>
                                            <div class="row mb-3">
                                                @php
                                                $defaultDeparture = date('Y-m-d');
                                                $defaultArrival = $defaultDeparture;

                                                // Nếu có giá trị old(), ưu tiên sử dụng nó
                                                $departureDateValue = old('departure_time', $defaultDeparture);
                                                $arrivalDateValue = old('estimated_arrival_time', $defaultArrival);
                                                @endphp
                                                <div class="col-md-3">
                                                    <label class="form-label">Ngày khởi hành<span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control date-input" name="departure_time" value="@formatDateForInput($departureDateValue)" required autocomplete="off">
                                                    @error('departure_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Giờ khởi hành</label>
                                                    <input type="time" class="form-control" name="start_time" id="start_time" value="{{ old('start_time') }}" inputmode="numeric" style="cursor:pointer;">
                                                    @error('start_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Ngày dự kiến đến</label>
                                                    <input type="date" class="form-control date-input" name="estimated_arrival_time" value="@formatDateForInput($arrivalDateValue)" required autocomplete="off">
                                                    @error('estimated_arrival_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Giờ đến</label>
                                                    <input type="time" class="form-control" name="end_time" value="{{ old('end_time') }}">
                                                    @error('end_time')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Giá chuyến <span class="text-danger">*</span></label>
                                                    <input type="text" id="total-amount" class="form-control unit-input" placeholder="Nhập giá chuyến" name="unit_price" value="{{ old('unit_price') }}">
                                                    @error('unit_price')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Số lượng chuyến</label>
                                                    <input type="text" class="form-control float-input" placeholder="Nhập số lượng chuyến" name="trip_count" value="{{ old('trip_count', 1) }}">
                                                    @error('trip_count')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Số KM</label>
                                                    <input type="number" class="form-control" placeholder="Nhập số KM" name="distance" value="{{ old('distance') }}">
                                                    @error('distance')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Khối lượng chuyến (tấn)</label>
                                                    <input type="text" class="form-control float-input" placeholder="Nhập khối lượng" name="cargo_weight" value="{{ old('cargo_weight') }}">
                                                    @error('cargo_weight')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
                                            </div>
                                                                                         <!-- Additional fields for origin and destination -->
                                            <div class="origin-destination bg-light p-3 mb-3">
                                                <label class="form-label fs-5">Thông tin lộ trình</label>
                                                <div class="row mb-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Điểm đi<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đi" name="origin" value="{{ old('origin') }}" required>
                                                        @error('origin')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Địa chỉ điểm đi</label>
                                                        <input type="text" class="form-control" placeholder="Nhập địa chỉ điểm đi" name="address_origin" value="{{ old('address_origin') }}">
                                                        @error('address_origin')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Điểm đến</label>
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đến 1" name="destination" value="{{ old('destination') }}">
                                                        @error('destination')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Địa chỉ điểm đến</label>
                                                        <input type="text" class="form-control" placeholder="Nhập địa chỉ điểm đến" name="address_destination" value="{{ old('address_destination') }}">
                                                        @error('address_destination')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Tên hàng hóa</label>
                                                        <input type="text" class="form-control" placeholder="Nhập tên hàng hóa" name="product_name" value="{{ old('product_name') }}">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-3">
                                                        {{--  <label class="form-label">Điểm đi 2</label>  --}}
                                                        <input hidden type="text" class="form-control" placeholder="Nhập điểm đi 2" name="origin2" value="{{ old('origin2') }}">
                                                        @error('origin2')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" placeholder="Nhập địa chỉ điểm đi" name="address_origin2" value="{{ old('address_origin2') }}">
                                                        @error('address_origin2')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đến 2" name="destination2" value="{{ old('destination2') }}">
                                                        @error('destination2')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" placeholder="Nhập địa chỉ điểm đến 2" name="address_destination2" value="{{ old('address_destination2') }}">
                                                        @error('address_destination2')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" placeholder="Nhập tên hàng hóa 2" name="product_name2" value="{{ old('product_name2') }}">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-3">
                                                        {{--  <label class="form-label">Điểm đi 3</label>  --}}
                                                        <input hidden type="text" class="form-control" placeholder="Nhập điểm đi 3" name="origin3" value="{{ old('origin3') }}">
                                                        @error('origin3')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" placeholder="Nhập địa chỉ điểm đi" name="address_origin3" value="{{ old('address_origin3') }}">
                                                        @error('address_origin3')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" placeholder="Nhập điểm đến 3" name="destination3" value="{{ old('destination3') }}">
                                                        @error('destination3')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" placeholder="Nhập địa chỉ điểm đến 3" name="address_destination3" value="{{ old('address_destination3') }}">
                                                        @error('address_destination3')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" placeholder="Nhập tên hàng hóa 3" name="product_name3" value="{{ old('product_name3') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End -->
                                            <div class="mb-3 bg-light p-3" id="goodsSection">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fs-5 mb-0">Danh sách hàng hóa <small class="text-muted text-sm-start">Cho xe nâng</small></label>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addGoodBtn">
                                                        <i class="fas fa-plus me-1"></i>Thêm hàng hóa
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-sm" id="goodsTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Tên hàng hóa</th>
                                                                <th>Nội dung công việc</th>
                                                                <th>Số lượng</th>
                                                                <th>Khối lượng (tấn)</th>
                                                                <th>Đơn giá(VNĐ)</th>
                                                                <th>Thành tiền</th>
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
                                                                            <input type="text" name="goods[{{ $i }}][name]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.name', $good['name'] ?? '') }}">
                                                                            @error('goods.'.$i.'.name')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][notes]" class="form-control form-control-sm" value="{{ old('goods.'.$i.'.Ghi chú', $good['Ghi chú'] ?? '') }}">
                                                                            @error('goods.'.$i.'.Ghi chú')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="goods[{{ $i }}][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.'.$i.'.quantity', $good['quantity'] ?? '') }}">
                                                                            @error('goods.'.$i.'.quantity')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][weight]" class="form-control form-control-sm float-input" min="0"  value="{{ old('goods.'.$i.'.weight', $good['weight'] ?? '') }}">
                                                                            @error('goods.'.$i.'.weight')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][unit]" class="form-control form-control-sm number" value="{{ old('goods.'.$i.'.unit', $good['unit'] ?? '') }}">
                                                                            @error('goods.'.$i.'.unit')<span class="text-danger">{{ $message }}</span>@enderror
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="goods[{{ $i }}][amount]" class="form-control form-control-sm number" value="{{ old('goods.'.$i.'.amount', $good['amount'] ?? '') }}">
                                                                            @error('goods.'.$i.'.amount')<span class="text-danger">{{ $message }}</span>@enderror
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
                                                                        <input type="text" name="goods[0][notes]" class="form-control form-control-sm" value="{{ old('goods.0.Ghi chú') }}">
                                                                        <div class="text-danger" id="error-goods-0-Ghi chú">@error('goods.0.Ghi chú'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="goods[0][quantity]" class="form-control form-control-sm" min="1" value="{{ old('goods.0.quantity') }}">
                                                                        <div class="text-danger" id="error-goods-0-quantity">@error('goods.0.quantity'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][weight]" class="form-control form-control-sm float-input" min="0"  value="{{ old('goods.0.weight') }}">
                                                                        <div class="text-danger" id="error-goods-0-weight">@error('goods.0.weight'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][unit]" class="form-control form-control-sm number" value="{{ old('goods.0.unit') }}">
                                                                        <div class="text-danger" id="error-goods-0-unit">@error('goods.0.unit'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="goods[0][amount]" class="form-control form-control-sm number" value="{{ old('goods.0.amount') }}">
                                                                        <div class="text-danger" id="error-goods-0-amount">@error('goods.0.amount'){{ $message }}@enderror</div>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGoodRow(this, 0)"><i class="ri-delete-bin-fill"></i></button>
                                                                        <input type="hidden" name="goods_rows[]" value="0">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="5" class="text-end font-weight-bold">Tổng thành tiền:</th>
                                                                <th id="total-amount-goods"></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="mb-3 bg-light p-3">
                                                <label class="form-label fs-5">Chi phí chuyến xe</label> <small class="text-muted">Chi phí khách hàng trả cho HPL</small>
                                                <div class="table-responsive">
                                                    <table class="table">
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
                                                                            <textarea class="form-control form-control-sm" name="deductions[{{ $type->id }}]" rows="3" placeholder="Nhập ghi chú...">{{ old('deductions.'.$type->id) }}</textarea>
                                                                        @else
                                                                            <input type="text" class="form-control form-control-sm deduction-input" name="deductions[{{ $type->id }}]" min="0" value="{{ old('deductions.'.$type->id) }}">
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
                                                <div class="col-md-12">
                                                    <label class="form-label">Ghi chú</label>
                                                    <textarea class="form-control" rows="2" placeholder="Nhập ghi chú" name="notes">{!! old('notes') !!}</textarea>
                                                    @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
                                                </div>
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
                                                <div class="col-md-10">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Phương tiện<span class="text-danger">*</span></label>
                                                            <select class="form-select js-example-basic-single" name="vehicle_id" id="vehicles">
                                                                <option value="">Chọn phương tiện</option>
                                                                @foreach($vehicles as $vehicle)
                                                                    <option value="{{ (int)$vehicle->vehicle_id }}" {{ old('vehicle_id') == (int)$vehicle->vehicle_id ? 'selected' : '' }}>{{ $vehicle->plate_number . '-' . $vehicle->vehicleType->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <!-- Loading spinner (hidden by default) -->
                                                            <div class="spinner-border spinner-border-sm text-primary mt-2" id="vehicle_loading" style="display: none">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                            @error('vehicle_id')<span class="text-danger">{{ $message }}</span>@enderror
                                                        </div>
                                                        <div class="col-md-6" id="unitPriceForDriverDiv" style="display: none;">
                                                            <label class="form-label">Giá chuyến</label>
                                                            <small class="text-muted">Chi phí HPL trả cho tài xế(12%)</small>
                                                            <input type="text" class="form-control number" placeholder="Nhập giá chuyến trả cho tài xế" name="unit_price_for_driver" value="{{ old('unit_price_for_driver') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="drivers">
                                                <hr>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="form-label mb-0 fs-5">TÀI XẾ</label>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addPersonBtn">
                                                            <i class="fas fa-plus me-1"></i>Thêm tài xế
                                                        </button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-striped align-middle table-nowrap mb-0" id="personTable">
                                                            <thead>
                                                                <tr>
                                                                    <th style="padding-right:100px;">Nhân sự <span class="text-danger">*</span></th>
                                                                    <th>Lái chính </th>
                                                                    @foreach($personDeductionTypes as $type)
                                                                        <th>{{ $type->name }}</th>
                                                                    @endforeach
                                                                    <th class="notes-col">Ghi chú</th>
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
                                                                            <select name="drivers[{{ $i }}][user_id]" class="form-select js-example-basic-single" style="min-width: 180px;" required>
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
                                                                        <td class="notes-col">
                                                                            <input type="text" name="drivers[{{ $i }}][deductions][notes]" class="form-control form-control-sm " value="{{ old('drivers.'.$i.'.deductions.Ghi chú', $driver['deductions'][$type->id]['Ghi chú'] ?? '') }}">
                                                                            @error('drivers.{{ $i }}.deductions.notes')<div class="text-danger">{{ $message }}</div>@enderror
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
                                                                        <select name="drivers[0][user_id]" class="form-select js-example-basic-single" required>
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
                                                                        <input type="text" name="drivers[0][deductions][notes]" class="form-control form-control-sm " value="{{ old('drivers.0.deductions.Ghi chú', $driver['deductions'][$type->id]['Ghi chú'] ?? '') }}">
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
                                                        <label class="form-label mb-0 fs-5">LƠ XE</label>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addPersonPxBtn">
                                                            <i class="fas fa-plus me-1"></i>Thêm lơ xe
                                                        </button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-striped align-middle table-nowrap mb-0" id="personPxTable">
                                                            <thead>
                                                                <tr>
                                                                    <th style="padding: 0px 60px;">Nhân sự <span class="text-danger">*</span></th>
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
                                                                            <select name="driverPXs[{{ $i }}][user_id]" class="form-select js-example-basic-single" style="min-width: 180px;" required>
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
                                                                            <input type="text" name="driverPXs[{{ $i }}][deductions][notes]" class="form-control form-control-sm " value="{{ old('driverPXs.'.$i.'.deductions.Ghi chú', $driver['deductions'][$type->id]['Ghi chú'] ?? '') }}">
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
                                            <div id="carRental" class="mt-3">
                                                <hr>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Giá chuyến <span class="text-danger">*</span></label>
                                                        <small class="text-muted">Chi phí HPL trả cho đối tác cho thuê xe</small>
                                                        <input type="text" class="form-control unit-input" placeholder="Nhập giá chuyến" name="unit_price_for_car_rental" value="{{ old('unit_price_for_car_rental') }}">
                                                        @error('unit_price_for_car_rental')<span class="text-danger">{{ $message }}</span>@enderror
                                                    </div>
                                                </div>
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
                                                                                <textarea class="form-control form-control-sm" name="deductions[{{ $type->id }}]" rows="3" placeholder="Nhập ghi chú...">{{ old('deductions.'.$type->id) }}</textarea>
                                                                            @else
                                                                                <input type="text" class="form-control form-control-sm deduction-input" name="deductions[{{ $type->id }}]" min="0" value="{{ old('deductions.'.$type->id) }}">
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
        $('.deduction-input, .unit-input, .number').on('input', function () {
            formatPriceInput($(this));
        });

        // Initial formatting for deduction inputs and unit inputs
        $('.deduction-input, .unit-input, .number').each(function() {
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
        const carRentalSection = document.getElementById('carRental');
        const unitPriceForDriverDiv = document.getElementById('unitPriceForDriverDiv');

        function toggleDriverSections() {
            if (!isCarRentalCheckbox || !driverSection) {
                return; // Exit if elements don't exist
            }

            const isChecked = isCarRentalCheckbox.checked;
            if (isChecked) {
                // Nếu là xe thuê, ẩn phần tài xế
                driverSection.style.display = 'none';
                carRentalSection.style.display = 'block';
                unitPriceForDriverDiv.style.display = 'none';
            } else {
                // Nếu không phải xe thuê, hiện phần tài xế
                driverSection.style.display = 'block';
                carRentalSection.style.display = 'none';
                unitPriceForDriverDiv.style.display = 'block';
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
<script src="{{ asset('js/shipment-goods.js') }}"></script>
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
