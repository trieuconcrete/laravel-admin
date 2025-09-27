@extends('admin.layout')

@section('content')

    <div class="container-fluid">
        <!-- Customer Info Header -->
        <div class="row mt-5">
            <!--end col-->
            <div class="col-xxl-12">
                <div class="card mt-xxl-n5">
                    <div class="customer-info-header p-3">
                        <div class="row">
                            <div class="col-md-5">
                                <h4>{{ $customer->name }}</h4>
                                <span class="text-muted">Mã khách hàng: </span><b>{{ $customer->customer_code }}</b><br>
                                <span class="text-muted">Loại khách hàng: </span><b>{{ $customer->getTypeLabelAttribute() }}</b>
                            </div>
                            <div class="col-md-7">
                                <h4 class="mb-0 text-center">
                                    <i class="fas fa-balance-scale me-2"></i>Tổng kết công nợ khách hàng
                                </h4>
                                <div class="card-body">
                                    <div class="row g-3" id="debtSummaryContainer">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="fs-4 fw-bold text-primary" id="totalReported">-</div>
                                                <div class="text-muted">Tổng công nợ</div>
                                                <small class="text-info d-none" id="refundNote">(*) Có điều chỉnh</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="fs-4 fw-bold text-success" id="totalPaid">-</div>
                                                <div class="text-muted">Đã thanh toán</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="fs-4 fw-bold text-danger" id="remainingDebt">-</div>
                                                <div class="text-muted" id="debtLabel">Còn nợ</div>
                                                <small class="text-info d-block" id="debtNote"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Tab -->
                    {{-- <div class="tab-pane fade show active" id="transactions"> --}}
                        <!-- Debt Summary Card -->
                        {{--
                    </div> --}}
                    <hr>
                    <!-- Nav Tabs -->
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link {{ ($activeTab ?? 'generalInfo') == 'generalInfo' ? 'active' : '' }}" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#generalInfo"
                            type="button" role="tab" aria-controls="generalInfo" aria-selected="{{ ($activeTab ?? 'generalInfo') == 'generalInfo' ? 'true' : 'false' }}">Thông tin</button>
                        <button class="nav-link {{ ($activeTab ?? 'generalInfo') == 'monthlyReport' ? 'active' : '' }}" id="nav-shipments-tab" data-bs-toggle="tab" data-bs-target="#monthlyReport"
                            type="button" role="tab" aria-controls="monthlyReport" aria-selected="{{ ($activeTab ?? 'generalInfo') == 'monthlyReport' ? 'true' : 'false' }}">Chuyến xe</button>
                        <button class="nav-link {{ ($activeTab ?? 'generalInfo') == 'carRental' ? 'active' : '' }}" id="nav-shipments-tab" data-bs-toggle="tab" data-bs-target="#carRental"
                            type="button" role="tab" aria-controls="carRental" aria-selected="{{ ($activeTab ?? 'generalInfo') == 'carRental' ? 'true' : 'false' }}">Thuê xe</button>
                        <button class="nav-link {{ ($activeTab ?? 'generalInfo') == 'shipmentReport' ? 'active' : '' }}" id="nav-shipments-tab" data-bs-toggle="tab" data-bs-target="#shipmentReport"
                            type="button" role="tab" aria-controls="shipmentReport" aria-selected="{{ ($activeTab ?? 'generalInfo') == 'shipmentReport' ? 'true' : 'false' }}">Công nợ</button>
                        <button class="nav-link {{ ($activeTab ?? 'generalInfo') == 'transactions' ? 'active' : '' }}" id="nav-transactions-tab" data-bs-toggle="tab"
                            data-bs-target="#transactions" type="button" role="tab" aria-controls="transactions"
                            aria-selected="{{ ($activeTab ?? 'generalInfo') == 'transactions' ? 'true' : 'false' }}">Thanh toán</button>
                    </div>
                    <!-- Tab Content -->
                    <div class="tab-content p-3 border border-top-0 rounded-bottom">
                        <!-- General Info Tab -->
                        <div class="tab-pane fade {{ ($activeTab ?? 'generalInfo') == 'generalInfo' ? 'show active' : '' }}" id="generalInfo">
                            <form action="{{ route('admin.customers.update', $customer) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Loại khách hàng: <span
                                                            class="text-danger">*</span></label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="individualType"
                                                        value="individual" {{ $customer->type == \App\Models\Customer::TYPE_INDIVIDUAL ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="individualType">Cá nhân</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="businessType"
                                                        value="business" {{ $customer->type == \App\Models\Customer::TYPE_BUSINESS ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="businessType">Doanh nghiệp</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Trạng thái</label>
                                            <select name="is_active" class="form-select">
                                                @foreach ($customerStatusActives as $key => $val)
                                                    <option value="{{ $key }}" {{ request('is_active') == $customer->is_active ? 'selected' : '' }}>{{ $val }}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger error" data-field="is_active"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Tên khách hàng <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" id="fullnameInput"
                                                placeholder="Enter your Full name"
                                                value="{{ old('name', $customer->name) }}">
                                            @error('name')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Mã khách hàng</label>
                                            <input disabled type="text" class="form-control" name="name" id="fullnameInput"
                                                placeholder="Enter your customer code"
                                                value="{{ old('customer_code', $customer->customer_code) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Mã số thuế <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="tax_code" id="fullnameInput"
                                                placeholder="Enter your tax_code"
                                                value="{{ old('tax_code', $customer->tax_code) }}">
                                            @error('tax_code')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Ngày thành lập</label>
                                            <input type="date" class="form-control" name="establishment_date"
                                                value="{{ $customer->establishment_date }}">
                                            @error('establishment_date')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Địa chỉ </label>
                                            <input type="text" class="form-control" name="address" id="fullnameInput"
                                                placeholder="Enter your address"
                                                value="{{ old('address', $customer->address) }}">
                                            @error('address')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Điện thoại <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="phone" id="fullnameInput"
                                                placeholder="Enter your address"
                                                value="{{ old('phone', $customer->phone) }}">
                                            @error('phone')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" id="fullnameInput"
                                                placeholder="Enter your email" value="{{ old('email', $customer->email) }}">
                                            @error('email')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Website </label>
                                            <input type="text" class="form-control" name="website" id="fullnameInput"
                                                placeholder="Enter your website"
                                                value="{{ old('website', $customer->website) }}">
                                            @error('website')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">File bảng kê</label>
                                            <input type="file" name="document_file" id="documentFileInput"
                                                class="form-control mt-1 border p-2 rounded">
                                            @if(session()->has('_documentFile_temp'))
                                                <input type="hidden" name="_documentFile_temp"
                                                    value="{{ session('_documentFile_temp') ?? null }}">
                                            @endif
                                            @if ($customer->document_file)
                                                <div class="mt-2">
                                                    <a href="{{ asset('storage/' . $customer->document_file) }}"
                                                        target="_blank">
                                                        📎 Xem tệp đã tải lên (đã được lưu)
                                                    </a>
                                                </div>
                                            @endif
                                            @error('document_file')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-start">
                                            <button type="submit" class="btn btn-secondary">Lưu</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                            </form>
                        </div>

                        <!-- monthly report -->
                        <div class="tab-pane fade {{ ($activeTab ?? 'generalInfo') == 'monthlyReport' ? 'show active' : '' }}" id="monthlyReport">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="">Bảng kê</label>
                                    <select class="form-select" name="month" id="month">
                                        <option value="">Chọn bảng kê</option>
                                        @if($shipmentMonthlyReports->count() > 0)
                                            @foreach($shipmentMonthlyReports as $index => $val)
                                                <option value="{{ $val->monthly }}">{{ $val->monthly }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="">Ngày bắt đầu <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control date-input" name="statement_start_date" id="statement_start_date" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="">Ngày kết thúc <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control date-input" name="statement_end_date" id="statement_end_date" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="">
                                        <label class="">Loại chuyến xe</label>
                                        <select class="form-control" name="shipment_type" id="shipment_type">
                                            <option value="">Tất cả loại chuyến xe</option>
                                            <option value="1">Khách chạy theo chuyến</option>
                                            <option value="3">Xe nâng</option>
                                            <option value="4">Xe đường dài bắc-nam</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4 mt-4">
                                <div class="col-md-12 text-center">
                                    <button type="button" id="searchShipments" class="btn btn-info me-2">
                                        <i class="ri-search-line me-1"></i>Tìm chuyến xe
                                    </button>
                                    <button type="button" id="exportInvoice" class="btn btn-outline-primary">
                                        <i class="las la-file-invoice align-middle me-1"></i> Xuất bảng kê
                                    </button>
                                    <button type="button" id="summarizeReport"
                                        class="btn btn-secondary me-2">
                                        <i class="las la-calculator align-middle me-1"></i>
                                        Tổng kết công nợ
                                    </button>
                                    <a href="{{ route('admin.shipments.create') . '?customer_id=' . $customer->id }}" class="btn btn-primary" target="_blank">
                                        <i class="ri-add-circle-line align-middle me-1"></i>Thêm chuyến xe
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle table-nowrap mb-0" id="monthlyReportTable">
                                    <thead class="table-light text-uppercase">
                                        <tr>
                                            <th>Mã chuyến</th>
                                            <th>Ngày</th>
                                            <th>Điểm đi</th>
                                            <th>Điểm đến</th>
                                            <th>Chuyến</th>
                                            <th>Số tấn</th>
                                            <th>Đơn giá</th>
                                            <th>Phụ phí</th>
                                            <th>Thành tiền</th>
                                            <th>Công nợ</th>
                                            <th>Ghi chú</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @if(isset($monthlyShipments) && count($monthlyShipments) > 0)
                                        @foreach($monthlyShipments as $shipment)
                                        <tr>
                                            <td>{{ $shipment['shipment_code'] }}</td>
                                            <td>{{ $shipment['departure_time'] }}</td>
                                            <td>{{ $shipment['origin'] }}</td>
                                            <td>{{ $shipment['destination'] }}</td>
                                            <td>{{ $shipment['trip_count'] }}</td>
                                            <td>{{ $shipment['cargo_weight'] }}</td>
                                            <td>{{ number_format($shipment['unit_price']) }}</td>
                                            <td>{{ $shipment['combined_fees'] > 0 ?
                                                number_format($shipment['combined_fees']) : '' }}</td>
                                            <td>{{ number_format($shipment['total_amount']) }}</td>
                                            <td>{{ $shipment['notes'] }}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="12" class="text-center">Không có dữ liệu chuyến xe trong tháng
                                                này</td>
                                        </tr>
                                        @endif --}}
                                    </tbody>
                                    <tfoot>
                                        @php
                                            $totalTrips = isset($monthlyShipments) ? $monthlyShipments->sum('trip_count') : 0;
                                            $totalWeight = isset($monthlyShipments) ? ($monthlyShipments->sum('cargo_weight')) : 0;
                                            $totalCombinedFees = isset($monthlyShipments) ? ($monthlyShipments->sum('combined_fees')) : 0;
                                            $grandTotal = isset($monthlyShipments) ? ($monthlyShipments->sum('total_amount')) : 0;
                                            $amountWithTax = isset($monthlyShipments) ? ($grandTotal * 0.08) : 0;
                                            $totalAmountWithTax = isset($monthlyShipments) ? ($grandTotal + $amountWithTax) : 0;
                                        @endphp
                                        <tr class="table-primary fw-bold">
                                            <td colspan="4">Tổng cộng</td>
                                            <td id="totalTrips">
                                                {{ number_format($totalTrips) }}
                                            </td>
                                            <td id="totalWeight">
                                                {{ number_format($totalWeight) }}
                                            </td>
                                            <td></td>
                                            <td id="totalCombinedFees">
                                                {{ number_format($totalCombinedFees) }}
                                            </td>
                                            <td id="grandTotal">
                                                {{ number_format($grandTotal) }}
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-primary fw-bold">
                                            <td colspan="4">Thuế GTGT 8%</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td id="amountWithTax">
                                                {{ number_format($amountWithTax) }}
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-primary fw-bold">
                                            <td colspan="4">Tổng thanh toán</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td id="totalAmountWithTax">
                                                {{ number_format($totalAmountWithTax) }}
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ ($activeTab ?? 'generalInfo') == 'carRental' ? 'show active' : '' }}" id="carRental">
                            {{-- <div class="d-flex justify-content-between mb-3">
                                <h6>Danh sách thuê xe</h6>
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target=""><i class="ri-add-circle-line align-middle me-1"></i>Thêm thuê xe</button>
                            </div> --}}
                            @include('admin.customers.partials.car-rental', ['carRentals' => $carRentals])
                        </div>
                        <div class="tab-pane fade {{ ($activeTab ?? 'generalInfo') == 'shipmentReport' ? 'show active' : '' }}" id="shipmentReport">
                            @include('admin.customers.partials.shipment-reports', ['shipmentReports' => $shipmentReports])
                        </div>

                        <!-- Transactions Tab -->
                        <div class="tab-pane fade {{ ($activeTab ?? 'generalInfo') == 'transactions' ? 'show active' : '' }}" id="transactions">

                            <div class="d-flex justify-content-between mb-3">
                                <h6>Lịch sử thanh toán</h6>
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#transactionModal"><i class="ri-add-circle-line align-middle me-1"></i>Thêm thanh toán</button>
                            </div>

                            <!-- Search Form -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <form id="transactionSearchForm" method="GET"
                                        action="{{ route('admin.customers.transactions', $customer) }}">
                                        <input type="hidden" name="active_tab" value="{{ $activeTab ?? 'transactions' }}">
                                        <div class="row g-3">
                                            <div class="col-md-6 col-lg-3">
                                                <label class="form-label">Từ ngày</label>
                                                <input type="date" name="start_date" class="form-control"
                                                    value="{{ request('start_date') ?? '' }}">
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 col-lg-3">
                                                <label class="form-label">Đến ngày</label>
                                                <input type="date" name="end_date" class="form-control"
                                                    value="{{ request('end_date') ?? '' }}">
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 col-lg-3">
                                                <label class="form-label">Phương thức thanh toán</label>
                                                <select name="payment_method" class="form-select">
                                                    <option value="">Tất cả</option>
                                                    @foreach(\App\Models\Payment::getPaymentMethods() as $key => $method)
                                                        <option value="{{ $key }}" {{ request('payment_method') == $key ? 'selected' : '' }}>{{ $method }}</option>
                                                    @endforeach
                                                </select>
                                                @error('payment_method')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 col-lg-3">
                                                <label class="form-label">Trạng thái</label>
                                                <select name="payment_status" class="form-select">
                                                    <option value="">Tất cả</option>
                                                    @foreach(\App\Models\Payment::getStatuses() as $key => $status)
                                                        <option value="{{ $key }}" {{ request('payment_status') == $key ? 'selected' : '' }}>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                                @error('payment_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12 d-flex justify-content-center">
                                                <div class="d-flex gap-3">
                                                    <button type="submit" class="btn btn-info me-2">
                                                        <i class="ri-search-line me-1"></i>Tìm kiếm
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Transaction Results -->
                            <div id="transaction-results">
                                @include('admin.customers.partials.transaction-table', ['transactions' => $transactions ?? collect()])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container-fluid -->

    <!-- Add Transaction Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thanh toán công nợ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr>
                <form id="transactionForm" enctype="multipart/form-data" method="POST"
                    action="{{ route('admin.customers.store-transaction', $customer) }}">
                    @csrf
                    <input type="hidden" name="shipment_report_id" id="shipment_report_id" value="">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                                <input class="form-control number-format" type="text" placeholder="Số tiền" name="amount" id="amount" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày thanh toán <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="payment_date"
                                    value="@formatDateForInput(date('Y-m-d'))" required />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-control">
                                    @foreach(\App\Models\Payment::getPaymentMethods() as $key => $method)
                                        <option value="{{ $key }}">{{ $method }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chú thích</label>
                            <textarea class="form-control" rows="3" placeholder="Nhập chú thích" name="notes" id="notes"></textarea>
                        </div>
                        <div id="transactionFormErrors" class="alert alert-danger mt-2" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Transaction Modal -->
    <div class="modal fade" id="editTransactionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa giao dịch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr>
                <form id="editTransactionForm" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="transaction_id" id="edit_transaction_id">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                                <input class="form-control number-format" type="text" placeholder="Số tiền" name="amount"
                                    id="edit_amount" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày thanh toán <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" name="payment_date" id="edit_payment_date"
                                    required />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                <select name="payment_method" id="edit_payment_method" class="form-control">
                                    @foreach(\App\Models\Payment::getPaymentMethods() as $key => $method)
                                        <option value="{{ $key }}">{{ $method }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chú thích</label>
                            <textarea class="form-control" rows="3" placeholder="Nhập chú thích" name="notes"
                                id="edit_notes"></textarea>
                        </div>
                        <div id="editTransactionFormErrors" class="alert alert-danger mt-2" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const carRentalEditRoute = "{{ route('admin.car-rental.edit-vehicle-log', ':id') }}";
            // Handle active tab from URL parameter or controller variable
            const urlParams = new URLSearchParams(window.location.search);
            const activeTabParam = urlParams.get('active_tab');
            const activeTab = activeTabParam || '{{ $activeTab ?? "generalInfo" }}';

            // Activate the correct tab
            if (activeTab) {
                // Remove active class from all tabs and panes
                document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Add active class to the correct tab and pane
                const targetTab = document.querySelector(`[data-bs-target="#${activeTab}"]`);
                const targetPane = document.getElementById(activeTab);

                if (targetTab) targetTab.classList.add('active');
                if (targetPane) targetPane.classList.add('show', 'active');
            }

            // Handle tab activation to ensure data is loaded when monthly report tab is clicked
            const tabLinks = document.querySelectorAll('.nav-link');
            if (tabLinks) {
                tabLinks.forEach(tab => {
                    tab.addEventListener('click', function (e) {
                        // Check if this is the monthly report tab
                        if (this.getAttribute('href') === '#monthlyReport' && monthSelector) {
                            // Ensure data is loaded when tab is activated
                            setTimeout(() => {
                                loadMonthlyReport({{ $customer->id }}, monthSelector.value);
                            }, 100);
                        }
                    });
                });
            }
            // Handle month selection change
            const monthSelector = document.getElementById('monthSelector');
            const customerId = {{ $customer->id }};

            if (monthSelector) {
                // Set default value to current month if not set
                if (!monthSelector.value) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = (now.getMonth() + 1).toString().padStart(2, '0');
                    monthSelector.value = `${year}-${month}`;
                }

                // Initial load of data
                loadMonthlyReport(customerId, monthSelector.value);

                // Handle change events
                monthSelector.addEventListener('change', function () {
                    loadMonthlyReport(customerId, this.value);
                });
            }

            // Function to load monthly report data
            function loadMonthlyReport(customerId, month) {
                // Show loading indicator
                const tableBody = document.querySelector('#monthlyReportTable tbody');
                const loadingRow = '<tr><td colspan="12" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Đang tải dữ liệu...</td></tr>';
                tableBody.innerHTML = loadingRow;

                // Disable month selector and export button during loading
                if (monthSelector) monthSelector.disabled = true;
                const exportBtn = document.getElementById('exportMonthlyReport');
                if (exportBtn) exportBtn.disabled = true;

                // Make AJAX request to get data
                fetch(`{{ route('admin.customers.show', $customer->id) }}?month=${month}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            // Clear table and add new data
                            tableBody.innerHTML = '';

                            // Calculate totals
                            let totalTrips = 0;
                            let totalWeight = 0;
                            let totalCombinedFees = 0;
                            let grandTotal = 0;

                            // Add rows for each shipment
                            data.data.forEach(shipment => {
                                const row = document.createElement('tr');
                                console.log('Shipment type:', shipment.shipment_type);

                                // Update totals
                                totalTrips += parseInt(shipment.trip_count) || 0;
                                totalWeight += parseFloat(shipment.cargo_weight) || 0;
                                totalCombinedFees += parseFloat(shipment.combined_fees) || 0;
                                grandTotal += parseFloat(shipment.total_amount) || 0;
                                amountWithTax = parseFloat(grandTotal * 0.08) || 0;
                                totalAmountWithTax = parseFloat(grandTotal + amountWithTax) || 0;

                                let shipmentLink;
                                if (shipment.shipment_type == 2) {
                                    // Giả sử bạn đã định nghĩa carRentalEditRoute trong blade
                                    shipmentLink = `<a href="${carRentalEditRoute.replace(':id', shipment.id)}">${shipment.shipment_code}</a>`;
                                } else {
                                    shipmentLink = `<a href="/admin/shipments/${shipment.id}/edit" target="_blank" class="text-primary">${shipment.shipment_code}</a>`;
                                }
                                let shipmentReport;
                                if (shipment.shipment_report_id) {
                                    shipmentReport = `<span class="badge bg-success">Đã tổng kết</span>`;
                                } else {
                                    shipmentReport = `<span class="badge bg-danger">Chưa tổng kết</span>`;
                                }

                                // Format the row HTML
                                row.innerHTML = `
                                <td>${shipmentLink}</td>
                                <td>${shipment.departure_time}</td>
                                <td>${shipment.origin}</td>
                                <td>${shipment.destination}</td>
                                <td>${shipment.trip_count}</td>
                                <td>${numberFormat(shipment.cargo_weight)}</td>
                                <td>${numberFormat(shipment.unit_price)}</td>
                                <td>${shipment.combined_fees > 0 ? numberFormat(shipment.combined_fees) : ''}</td>
                                <td>${numberFormat(shipment.total_amount)}</td>
                                <td>${shipmentReport}</td>
                                <td>${shipment.notes || ''}</td>
                                <td><span class="badge bg-${getStatusBadgeClass(shipment.status)}">${getStatusLabel(shipment.status)}</span></td>
                            `;

                                tableBody.appendChild(row);
                            });

                            // Update footer totals
                            document.getElementById('totalTrips').textContent = totalTrips;
                            document.getElementById('totalWeight').textContent = numberFormat(totalWeight.toFixed(2));
                            document.getElementById('totalCombinedFees').textContent = numberFormat(totalCombinedFees);
                            document.getElementById('grandTotal').textContent = numberFormat(grandTotal);
                            document.getElementById('amountWithTax').textContent = numberFormat(amountWithTax);
                            document.getElementById('totalAmountWithTax').textContent = numberFormat(totalAmountWithTax);
                        } else {
                            // No data found
                            document.getElementById('grandTotal').textContent = '0';
                            document.getElementById('amountWithTax').textContent = '0';
                            document.getElementById('totalAmountWithTax').textContent = '0';
                        }

                        // Button luôn hiển thị và sẵn sàng để tổng kết
                        const summarizeButton = document.getElementById('summarizeReport');
                        if (summarizeButton) {
                            summarizeButton.disabled = false;
                            summarizeButton.innerHTML = '<i class="las la-calculator align-middle me-1"></i> Tổng kết bảng kê';
                            summarizeButton.className = 'btn btn-secondary me-2';
                        }

                        // Re-enable controls
                        if (monthSelector) monthSelector.disabled = false;
                        const exportBtn = document.getElementById('exportMonthlyReport');
                        if (exportBtn) exportBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error fetching monthly report:', error);
                        tableBody.innerHTML = '<tr><td colspan="12" class="text-center text-danger">Lỗi khi tải dữ liệu. Vui lòng thử lại.</td></tr>';

                        // Re-enable controls in case of error
                        if (monthSelector) monthSelector.disabled = false;
                        const exportBtn = document.getElementById('exportMonthlyReport');
                        if (exportBtn) exportBtn.disabled = false;
                    });
            }

            // Helper function to format numbers with commas
            function numberFormat(number) {
                return new Intl.NumberFormat('vi-VN').format(number);
            }

            // Helper function to get status badge class
            function getStatusBadgeClass(status) {
                const statusClasses = {
                    'pending': 'warning',
                    'confirmed': 'info',
                    'in_transit': 'primary',
                    'delivered': 'success',
                    'cancelled': 'danger',
                    'delayed': 'danger',
                    'completed': 'success'
                };
                return statusClasses[status] || 'secondary';
            }

            // Helper function to get status label
            function getStatusLabel(status) {
                const statusLabels = {
                    'pending': 'Chờ xác nhận',
                    'confirmed': 'Đã xác nhận',
                    'in_transit': 'Đang vận chuyển',
                    'delivered': 'Đã giao hàng',
                    'cancelled': 'Đã hủy',
                    'delayed': 'Bị trễ',
                    'completed': 'Hoàn thành'
                };
                return statusLabels[status] || status;
            }

            // Helper function to get shipment type label
            function getShipmentTypeLabel(shipmentType) {
                const typeLabels = {
                    '1': 'Khách chạy theo chuyến',
                    '2': 'Khách thuê xe tháng',
                    '3': 'Xe nâng',
                    '4': 'Xe đường dài bắc-nam'
                };
                return typeLabels[shipmentType] || 'Loại chuyến xe';
            }

                        // Function to perform search with given parameters
            function performSearch(startDate, endDate, shipmentType = '') {
                console.log('=== performSearch called ===');
                console.log('Parameters:', { startDate, endDate, shipmentType });
                console.log('Start date type:', typeof startDate);
                console.log('End date type:', typeof endDate);

                // Build query parameters - if shipmentType is empty, don't include it in query
                const queryParams = new URLSearchParams({
                    statement_start_date: startDate,
                    statement_end_date: endDate
                });

                if (shipmentType && shipmentType !== '') {
                    queryParams.append('shipment_type', shipmentType);
                }

                console.log('Query params:', queryParams.toString());

                // Show loading
                const searchButton = document.getElementById('searchShipments');
                if (searchButton) {
                    searchButton.disabled = true;
                    const originalText = searchButton.innerHTML;
                    searchButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tìm kiếm...';

                    const apiUrl = `{{ route('admin.shipment-reports.data', $customer) }}?${queryParams.toString()}`;
                    console.log('Making fetch request to:', apiUrl);
                    console.log('Full URL:', window.location.origin + apiUrl);

                    // Load data
                    fetch(apiUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        console.log('=== Response received ===');
                        console.log('Response status:', response.status);
                        console.log('Response ok:', response.ok);
                        console.log('Response headers:', response.headers);
                        return response.json();
                    })
                    .then(data => {
                        console.log('=== Response data parsed ===');
                        console.log('Response data:', data);
                        console.log('Data success:', data.success);
                        console.log('Data data:', data.data);
                        console.log('Data total_count:', data.total_count);
                        if (data.success) {
                            console.log('=== API call successful ===');
                            console.log('Calling updateTableWithData with:', data.data);

                            // Update table with data
                            updateTableWithData(data.data);

                            // Load debt summary after updating table (tổng công nợ từ trước đến nay)
                            loadDebtSummary();

                            // Show success message
                            const shipmentType = document.querySelector('select[name="shipment_type"]').value;
                            const typeLabel = shipmentType && shipmentType !== '' ? getShipmentTypeLabel(shipmentType) : 'Tất cả các loại';

                            // Swal.fire({
                            //     icon: 'success',
                            //     title: 'Thành công',
                            //     text: `Tìm thấy ${data.total_count} chuyến xe (${typeLabel}) với tổng tiền ${numberFormat(data.total_amount)} VND`,
                            //     timer: 2000,
                            //     showConfirmButton: false
                            // });
                        } else {
                            console.log('=== API returned error ===');
                            console.log('API error data:', data);
                            console.log('API error message:', data.message);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message || 'Có lỗi xảy ra khi tìm kiếm.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('=== Error occurred ===');
                        console.error('Error type:', error.constructor.name);
                        console.error('Error message:', error.message);
                        console.error('Error stack:', error.stack);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra khi tìm kiếm.'
                        });
                    })
                    .finally(() => {
                        console.log('=== Request completed ===');
                        console.log('Re-enabling search button');
                        searchButton.disabled = false;
                        searchButton.innerHTML = originalText;
                    });
                }
            }

            // Handle search shipments button click
            $('#searchShipments').on('click', function () {
                const startDate = $('#statement_start_date').attr('data-backend-value') || '';
                const endDate = $('#statement_end_date').attr('data-backend-value') || '';
                const shipmentType = $('#shipment_type').val();

                if (new Date(startDate) > new Date(endDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.'
                    });
                    return;
                }

                performSearch(startDate, endDate, shipmentType);

                loadDebtSummary();
            });

            // Function to load debt summary from API (tổng công nợ từ trước đến nay)
            function loadDebtSummary() {
                const customerId = {{ $customer->id }};

                // Call API to get debt summary (không cần tham số thời gian)
                // Luôn lấy tổng công nợ từ trước đến nay, không phụ thuộc vào thời gian search
                fetch(`{{ route('admin.customers.debt-summary', $customer) }}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateDebtSummary(data.debt_summary);
                    } else {
                        console.error('Error loading debt summary:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading debt summary:', error);
                });
            }

            // Function to update debt summary UI
            function updateDebtSummary(debtSummary) {
                const totalReportedEl = document.getElementById('totalReported');
                const totalPaidEl = document.getElementById('totalPaid');
                const remainingDebtEl = document.getElementById('remainingDebt');
                const debtLabelEl = document.getElementById('debtLabel');
                const debtNoteEl = document.getElementById('debtNote');
                const refundNoteEl = document.getElementById('refundNote');

                if (totalReportedEl) totalReportedEl.textContent = numberFormat(debtSummary.total_reported);
                if (totalPaidEl) totalPaidEl.textContent = numberFormat(debtSummary.total_paid);
                if (remainingDebtEl) remainingDebtEl.textContent = numberFormat(Math.abs(debtSummary.remaining_debt));

                // Update debt label and note based on debt type
                if (debtSummary.is_refund_case) {
                    if (refundNoteEl) refundNoteEl.classList.remove('d-none');

                    if (debtSummary.debt_type === 'customer_owes') {
                        if (debtLabelEl) debtLabelEl.textContent = 'Còn nợ';
                        if (debtNoteEl) debtNoteEl.textContent = '(*) Có điều chỉnh';
                        if (remainingDebtEl) remainingDebtEl.className = 'fs-4 fw-bold text-danger';
                    } else {
                        if (debtLabelEl) debtLabelEl.textContent = 'Công ty nợ';
                        if (debtNoteEl) debtNoteEl.textContent = '(*) Có điều chỉnh';
                        if (remainingDebtEl) remainingDebtEl.className = 'fs-4 fw-bold text-success';
                    }
                } else {
                    if (refundNoteEl) refundNoteEl.classList.add('d-none');

                    if (debtSummary.debt_type === 'customer_owes') {
                        if (debtLabelEl) debtLabelEl.textContent = 'Còn nợ';
                        if (debtNoteEl) debtNoteEl.textContent = '';
                        if (remainingDebtEl) remainingDebtEl.className = 'fs-4 fw-bold text-danger';
                    } else if (debtSummary.debt_type === 'company_owes') {
                        if (debtLabelEl) debtLabelEl.textContent = 'Công ty nợ';
                        if (debtNoteEl) debtNoteEl.textContent = '';
                        if (remainingDebtEl) remainingDebtEl.className = 'fs-4 fw-bold text-success';
                    } else {
                        if (debtLabelEl) debtLabelEl.textContent = 'Đã cân bằng';
                        if (debtNoteEl) debtNoteEl.textContent = '';
                        if (remainingDebtEl) remainingDebtEl.className = 'fs-4 fw-bold text-info';
                    }
                }
            }

            // Function to update table with data
            function updateTableWithData(shipments) {
                console.log('=== updateTableWithData called ===');
                console.log('Shipments data:', shipments);
                console.log('Shipments length:', shipments ? shipments.length : 'null');
                const tableBody = document.querySelector('#monthlyReportTable tbody');
                const tableFoot = document.querySelector('#monthlyReportTable tfoot');
                console.log('Table body element:', tableBody);
                console.log('Table foot element:', tableFoot);

                tableBody.innerHTML = '';

                if (shipments.length === 0) {
                    console.log('No shipments found, showing empty message');
                    tableBody.innerHTML = '<tr><td colspan="11" class="text-center">Không có dữ liệu chuyến xe trong thời gian này</td></tr>';
                    // Ẩn tfoot khi không có dữ liệu
                    if (tableFoot) {
                        tableFoot.style.display = 'none';
                    }
                    return;
                }

                // Hiển thị tfoot khi có dữ liệu
                if (tableFoot) {
                    tableFoot.style.display = 'table-footer-group';
                }

                let totalTrips = 0;
                let totalWeight = 0;
                let totalCombinedFees = 0;
                let grandTotal = 0;
                let amountWithTax = 0;
                let totalAmountWithTax = 0;

                shipments.forEach(shipment => {
                    const row = document.createElement('tr');

                    totalTrips += parseInt(shipment.trip_count) || 0;
                    totalWeight += parseFloat(shipment.cargo_weight) || 0;
                    totalCombinedFees += parseFloat(shipment.combined_fees) || 0;
                    grandTotal += parseFloat(shipment.total_amount) || 0;
                    amountWithTax = parseFloat(grandTotal * 0.08) || 0;
                    totalAmountWithTax = parseFloat(grandTotal + amountWithTax) || 0;
                    let shipmentLink;
                    if (shipment.shipment_type == 2) {
                        // Giả sử bạn đã định nghĩa carRentalEditRoute trong blade
                        shipmentLink = `<a href="${carRentalEditRoute.replace(':id', shipment.id)}">${shipment.shipment_code}</a>`;
                    } else {
                        shipmentLink = `<a href="/admin/shipments/${shipment.id}/edit" target="_blank" class="text-primary">${shipment.shipment_code}</a>`;
                    }
                    let shipmentReport;
                    if (shipment.shipment_report_id) {
                        shipmentReport = `<span class="badge bg-success">Đã tổng kết</span>`;
                    } else {
                        shipmentReport = `<span class="badge bg-danger">Chưa tổng kết</span>`;
                    }

                    row.innerHTML = `
                        <td>${shipmentLink}</td>
                        <td>${shipment.departure_time}</td>
                        <td>${shipment.origin}</td>
                        <td>${shipment.destination}</td>
                        <td>${shipment.trip_count}</td>
                        <td>${numberFormat(shipment.cargo_weight)}</td>
                        <td>${numberFormat(shipment.unit_price)}</td>
                        <td>${shipment.combined_fees > 0 ? numberFormat(shipment.combined_fees) : ''}</td>
                        <td>${numberFormat(shipment.total_amount)}</td>
                        <td>${shipmentReport}</td>
                        <td>${shipment.notes || ''}</td>
                        <td><span class="badge bg-${getStatusBadgeClass(shipment.status)}">${getStatusLabel(shipment.status)}</span></td>
                    `;

                    tableBody.appendChild(row);
                });

                // Update footer totals
                document.getElementById('totalTrips').textContent = totalTrips;
                document.getElementById('totalWeight').textContent = numberFormat(totalWeight.toFixed(2));
                document.getElementById('totalCombinedFees').textContent = numberFormat(totalCombinedFees);
                document.getElementById('grandTotal').textContent = numberFormat(grandTotal);
                document.getElementById('amountWithTax').textContent = numberFormat(amountWithTax);
                document.getElementById('totalAmountWithTax').textContent = numberFormat(totalAmountWithTax);
            }

            // Handle summarize report button click
            $('#summarizeReport').on('click', function () {
                const startDate = $('#statement_start_date').data('backend-value');
                const endDate = $('#statement_end_date').data('backend-value');
                const shipmentType = $('#shipment_type').val();
                const monthSelect = $('#month')[0];
                const customerId = {{ $customer->id }};

                // Validate
                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng chọn ngày bắt đầu và ngày kết thúc để tổng kết.'
                    });
                    return;
                }

                if (!shipmentType || shipmentType === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng chọn loại chuyến xe để tổng kết.'
                    });
                    return;
                }

                if (new Date(startDate) > new Date(endDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.'
                    });
                    return;
                }

                const shipmentTypeLabel = getShipmentTypeLabel(shipmentType);

                Swal.fire({
                    title: 'Xác nhận tổng kết bảng kê?',
                    html: `
                        <div class="text-start">
                            <p><strong>Thời gian:</strong> ${startDate} - ${endDate}</p>
                            <p><strong>Loại chuyến xe:</strong> ${shipmentTypeLabel}</p>
                            <p class="text-warning"><i class="las la-exclamation-triangle"></i> Chỉ tổng kết cho loại chuyến xe này</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Tổng kết',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        confirmButton: 'btn btn-secondary',
                        cancelButton: 'btn btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const originalText = $('#summarizeReport').html();
                        $('#summarizeReport')
                            .prop('disabled', true)
                            .html('<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...');

                        $.ajax({
                            url: `{{ route('admin.shipment-reports.summarize', $customer) }}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            contentType: 'application/json',
                            data: JSON.stringify({
                                statement_start_date: startDate,
                                statement_end_date: endDate,
                                shipment_type: parseInt(shipmentType),
                                customer_id: customerId,
                                month: monthSelect ? monthSelect.value : ''
                            }),
                            success: function (data) {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công',
                                        html: `
                                            <div class="text-start">
                                                <p><strong>Thời gian:</strong> ${startDate} - ${endDate}</p>
                                                <p><strong>Loại chuyến xe:</strong> ${getShipmentTypeLabel(shipmentType)}</p>
                                                <p><strong>Số chuyến:</strong> ${data.data.shipment_count}</p>
                                                <p><strong>Tổng tiền:</strong> ${numberFormat(data.data.total_amount)} VND</p>
                                            </div>
                                        `,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Đóng'
                                    }).then((res) => {
                                        if (res.isConfirmed) {
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: data.message || 'Đã xảy ra lỗi khi tổng kết bảng kê.'
                                    });
                                }
                            },
                            error: function (err) {
                                console.error('Error:', err);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: 'Đã xảy ra lỗi khi tổng kết bảng kê.'
                                });
                            },
                            complete: function () {
                                $('#summarizeReport')
                                    .prop('disabled', false)
                                    .html(originalText);
                                setTimeout(() => {
                                    // window.location.reload();
                                }, 3000);
                            }
                        });
                    }
                });
            });


            $('#exportInvoice').on('click', function () {
                const startDate = $('#statement_start_date').data('backend-value');
                const endDate = $('#statement_end_date').data('backend-value');
                const shipmentType = $('#shipment_type').val();
                const monthSelect = $('#month')[0];

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng chọn ngày bắt đầu và ngày kết thúc để xuất.'
                    });
                    return;
                }

                if (!shipmentType) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng chọn loại chuyến xe để xuất.'
                    });
                    return;
                }

                // Lấy label của shipment type
                const shipmentTypeLabel = getShipmentTypeLabel(shipmentType);

                // Cấu hình Swal
                const swalOptions = {
                    title: 'Xác nhận xuất bảng kê?',
                    html: `
                        <div class="text-start">
                            <p><strong>Thời gian:</strong> ${startDate} - ${endDate}</p>
                            <p><strong>Loại chuyến xe:</strong> ${shipmentTypeLabel}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Có, xuất ngay',
                    cancelButtonText: 'Hủy bỏ',
                    customClass: {
                        confirmButton: 'btn btn-secondary',
                        cancelButton: 'btn btn-light'
                    }
                };

                // Nếu shipmentType = 1, thêm select template
                if (shipmentType == 1) {
                    swalOptions.input = "select";
                    swalOptions.inputOptions = {
                        '1': 'TOPBAND',
                        '2': 'WOOJIN',
                        '3': 'Khác',
                    };
                    swalOptions.inputPlaceholder = "Chọn mẫu bảng kê";
                    swalOptions.inputValidator = (value) => {
                        return new Promise((resolve) => {
                            if (value !== "") {
                                resolve();
                            } else {
                                resolve("Vui lòng chọn mẫu bảng kê.");
                            }
                        });
                    };
                }

                // Hiển thị Swal
                Swal.fire(swalOptions).then((result) => {
                    console.log('Export confirmation result:', result);
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();

                                const params = new URLSearchParams({
                                    statement_start_date: startDate,
                                    statement_end_date: endDate,
                                    shipment_type: shipmentType,
                                    month: monthSelect ? monthSelect.value : '',
                                    excel_template: result.value || ''
                                });

                                const downloadUrl = `{{ route('admin.shipment-reports.export', $customer) }}?${params.toString()}`;

                                // Tạo link tạm và trigger download
                                const link = document.createElement('a');
                                link.href = downloadUrl;
                                link.style.display = 'none';
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);

                                setTimeout(() => {
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Xuất bảng kê thành công',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                }, 1000);
                            }
                        });
                    }
                });
            });

            // Helper function to get shipment type label
            function getShipmentTypeLabel(type) {
                const labels = {
                    1: 'Khách chạy theo chuyến',
                    2: 'Khách chạy theo tháng',
                    3: 'Xe nâng',
                    4: 'Xe đường dài bắc-nam'
                };
                return labels[type] || 'Không xác định';
            }

            // Transaction form submission
            $('#transactionForm').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const errorContainer = $('#transactionFormErrors');

                // Disable submit button and show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
                errorContainer.hide();

                // Get form data
                const formData = new FormData(form[0]);

                // Send AJAX request
                $.ajax({
                    url: '{{ route("admin.customers.store-transaction", $customer) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        // Close modal
                        $('#transactionModal').modal('hide');

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Giao dịch đã được tạo thành công.'
                        }).then(() => {
                            // Reload the page to show the new transaction
                            window.location.reload();
                        });
                    },
                    error: function (xhr) {
                        // Show error message
                        let errorMessage = 'Đã xảy ra lỗi khi tạo giao dịch.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            // Handle validation errors
                            if (xhr.responseJSON.errors) {
                                errorMessage = '<ul>';
                                $.each(xhr.responseJSON.errors, function (key, value) {
                                    errorMessage += '<li>' + value + '</li>';
                                });
                                errorMessage += '</ul>';
                            }
                        }

                        errorContainer.html(errorMessage).show();
                    },
                    complete: function () {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html('Lưu');
                    }
                });
            });

            // Transaction search form AJAX submission
            $('#transactionSearchForm').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const resultsContainer = $('#transaction-results');

                // Clear previous validation errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').hide();

                // Show loading indicator
                resultsContainer.html('<div class="text-center my-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

                // Get form data
                const formData = new FormData(form[0]);
                const searchUrl = form.attr('action') + '?' + new URLSearchParams(formData).toString();

                // Update browser URL without reloading the page
                window.history.pushState({}, '', searchUrl);

                // Send AJAX request
                $.ajax({
                    url: searchUrl,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (response) {
                        if (response.success) {
                            resultsContainer.html(response.html);
                        } else {
                            resultsContainer.html('<div class="alert alert-danger">' + (response.message || 'Đã xảy ra lỗi khi tìm kiếm giao dịch.') + '</div>');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;

                            // Display validation errors for each field
                            $.each(errors, function (field, messages) {
                                const inputField = form.find('[name="' + field + '"]');
                                inputField.addClass('is-invalid');

                                // Find or create error message container
                                let errorContainer = inputField.siblings('.invalid-feedback');
                                if (errorContainer.length === 0) {
                                    errorContainer = $('<div class="invalid-feedback"></div>');
                                    inputField.after(errorContainer);
                                }

                                // Display the first error message
                                errorContainer.text(messages[0]).show();
                            });

                            // Show a general error message at the top
                            resultsContainer.html('<div class="alert alert-danger">Vui lòng kiểm tra lại thông tin tìm kiếm.</div>');
                        } else {
                            // Handle other errors
                            let errorMessage = 'Đã xảy ra lỗi khi tìm kiếm giao dịch.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            resultsContainer.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                        }
                    }
                });
            });

            // Handle pagination links
            $(document).on('click', '#transaction-pagination .pagination a', function (e) {
                e.preventDefault();

                const url = $(this).attr('href');
                const resultsContainer = $('#transaction-results');

                // Show loading indicator
                resultsContainer.html('<div class="text-center my-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

                // Update browser URL without reloading the page
                window.history.pushState({}, '', url);

                // Send AJAX request
                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (response) {
                        if (response.success) {
                            resultsContainer.html(response.html);
                        } else {
                            resultsContainer.html('<div class="alert alert-danger">' + (response.message || 'Đã xảy ra lỗi khi tải dữ liệu.') + '</div>');
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Đã xảy ra lỗi khi tải dữ liệu.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        resultsContainer.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    }
                });
            });
            // Format amount inputs with thousand separators
            $('.number-format').on('input', function () {
                let value = $(this).val();

                // Remove all non-digit characters except decimal point
                value = value.replace(/[^0-9.]/g, '');

                // Limit to 9 digits before decimal point
                let parts = value.split('.');
                parts[0] = parts[0].slice(0, 10);

                // Format with thousand separators
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';

                $(this).val(integerPart + decimalPart);
            });

            // Format initial values if they exist
            $('.number-format').each(function () {
                if ($(this).val()) {
                    let value = $(this).val();
                    // Remove all non-digit characters except decimal point
                    value = value.replace(/[^0-9.]/g, '');

                    // Format with thousand separators
                    let parts = value.split('.');
                    let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';

                    $(this).val(integerPart + decimalPart);
                }
            });

            // Handle edit transaction button click
            $(document).on('click', '.edit-transaction', function () {
                const transactionId = $(this).data('id');
                const amount = $(this).data('amount');
                const paymentDate = $(this).data('payment-date');
                const paymentMethod = $(this).data('payment-method');
                const notes = $(this).data('notes');
                const customerId = {{ $customer->id }};

                // Set form action
                const updateUrl = '{{ url("admin/customers/{$customer->id}/transactions") }}/' + transactionId;
                $('#editTransactionForm').attr('action', updateUrl);

                // Fill form fields
                $('#edit_transaction_id').val(transactionId);
                $('#edit_amount').val(amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
                $('#edit_payment_date').val(paymentDate);
                console.log(paymentMethod);
                $('#edit_payment_method').val(paymentMethod);
                $('#edit_notes').val(notes);
            });

            // Edit transaction form submission
            $('#editTransactionForm').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const errorContainer = $('#editTransactionFormErrors');

                // Disable submit button and show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
                errorContainer.hide();

                // Get form data
                const formData = new FormData(form[0]);

                // Send AJAX request
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        // Close modal
                        $('#editTransactionModal').modal('hide');

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Giao dịch đã được cập nhật thành công.'
                        }).then(() => {
                            // Reload the page to show the updated transaction
                            window.location.reload();
                        });
                    },
                    error: function (xhr) {
                        // Show error message
                        let errorMessage = 'Đã xảy ra lỗi khi cập nhật giao dịch.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            // Handle validation errors
                            if (xhr.responseJSON.errors) {
                                errorMessage = '<ul>';
                                $.each(xhr.responseJSON.errors, function (key, value) {
                                    errorMessage += '<li>' + value + '</li>';
                                });
                                errorMessage += '</ul>';
                            }
                        }

                        errorContainer.html(errorMessage).show();
                    },
                    complete: function () {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html('Cập nhật');
                    }
                });
            });

            // Handle delete transaction button click
            $(document).on('click', '.delete-transaction', function () {
                const transactionId = $(this).data('id');
                const customerId = $(this).data('customer-id');
                const token = '{{ csrf_token() }}';

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: 'Bạn có chắc chắn muốn xóa giao dịch này không? Hành động này không thể hoàn tác.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send delete request
                        $.ajax({
                            url: `{{ url('admin/customers') }}/${customerId}/transactions/${transactionId}`,
                            type: 'DELETE',
                            data: {
                                _token: token
                            },
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: response.message || 'Giao dịch đã được xóa thành công.'
                                }).then(() => {
                                    // Reload the page to update the transaction list
                                    window.location.reload();
                                });
                            },
                            error: function (xhr) {
                                let errorMessage = 'Đã xảy ra lỗi khi xóa giao dịch.';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });



            // Main event: Use window.onload to ensure everything is ready
            window.addEventListener('load', function () {
                console.log('=== Window loaded - Main event ===');

                const startDate = $('#statement_start_date').data('backend-value');
                const endDate = $('#statement_end_date').data('backend-value');

                performSearch(startDate, endDate, '');

                loadDebtSummary();

                window.initialLoadHandled = true;
            });

            // Additional backup: Use setTimeout to ensure everything is ready (only if initial load not handled)
            setTimeout(function() {
                console.log('=== Timeout check for auto-load ===');

                // Check if initial load has already been handled
                if (window.initialLoadHandled === true) {
                    console.log('Timeout - initial load already handled, skipping');
                    return;
                }

                const monthSelect = document.getElementById('month');

                // Check if month select has a meaningful value and not loaded yet
                if (monthSelect && monthSelect.value && monthSelect.value !== '' && !monthSelect.dataset.loaded) {
                    console.log('Timeout - triggering change event for month:', monthSelect.value);
                    monthSelect.dataset.loaded = 'true';
                    monthSelect.dispatchEvent(new Event('change'));
                } else if (!monthSelect || !monthSelect.value || monthSelect.value === '') {
                    // If no month selected or empty value, check if we need to set default dates
                    const startDateInput = document.getElementById('statement_start_date');
                    const endDateInput = document.getElementById('statement_end_date');

                    if (!startDateInput.value || !endDateInput.value) {
                        console.log('Timeout - setting default dates but not searching');
                        const currentDate = new Date();
                        const year = currentDate.getFullYear();
                        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                        const startDate = `${year}-${month}-01`;
                        const lastDay = new Date(year, month, 0).getDate();
                        const endDate = `${year}-${month}-${lastDay}`;

                        startDateInput.value = startDate;
                        endDateInput.value = endDate;

                        console.log('Dates set, waiting for user to select shipment_type and click search');
                    } else {
                        console.log('Timeout - dates already set, auto-searching with all types');
                        performSearch(startDateInput.value, endDateInput.value, '');
                    }
                }

                // Mark that we've handled the load (even if it's from timeout)
                window.initialLoadHandled = true;
            }, 1000);


            // Handle payment button clicks
            const transactionModal = document.getElementById('transactionModal');
            const shipmentReportIdInput = document.getElementById('shipment_report_id');
            const amountInput = document.getElementById('amount');
            const notesInput = document.getElementById('notes');

            // Listen for modal show event to populate data
            transactionModal.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget;

                // Get shipment_report_id and amount from the parent row's data attributes
                const row = button.closest('tr');
                const shipmentId = row.getAttribute('data-debt');
                const amount = row.getAttribute('data-amount');
                const notes = row.getAttribute('data-notes');

                // Set values in modal inputs
                if (shipmentReportIdInput) {
                    shipmentReportIdInput.value = shipmentId;
                }
                // Set values in modal inputs
                if (notesInput) {
                    notesInput.value = 'Công nợ #' + shipmentId + '\nLoại: ' + notes;
                }

                if (amountInput && amount) {
                    // Format amount with commas for display
                    const formattedAmount = parseInt(amount).toLocaleString('en-US');
                    amountInput.value = formattedAmount;
                }
            });
        });
    </script>
@endpush
