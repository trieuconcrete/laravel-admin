@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <!-- Customer Info Header -->
    <div class="row mt-5">
        <!--end col-->
        <div class="col-xxl-12">
            <div class="card mt-xxl-n5">
                <div class="customer-info-header p-3 mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $customer->name }}</h4>
                            <p class="text-muted">Mã khách hàng: {{ $customer->customer_code }}</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><i class="fas fa-building me-2 text-primary"></i> {{ $customer->getTypeLabelAttribute() }}</p>
                                    <p><i class="fas fa-map-marker-alt me-2 text-{{ $customer->getStatusBadgeClassAttribute() }}"></i>{{ $customer->address }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-{{ $customer->getStatusBadgeClassAttribute() }} mb-2">{{ $customer->getStatusLabelAttribute() }}</span>
                            <p><i class="fas fa-calendar-alt me-2 text-primary"></i> Ngày đăng ký: @formatDate($customer->establishment_date)</p>
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Nav Tabs -->
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#generalInfo" type="button" role="tab" aria-controls="generalInfo" aria-selected="false">Tổng quan</button>
                    <button class="nav-link" id="nav-shipments-tab" data-bs-toggle="tab" data-bs-target="#monthlyReport" type="button" role="tab" aria-controls="monthlyReport" aria-selected="false">Bảng kê theo tháng</button>
                    <button class="nav-link active" id="nav-transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab" aria-controls="transactions" aria-selected="true">Giao dịch</button>
                </div>
                <!-- Tab Content -->
                <div class="tab-content p-3 border border-top-0 rounded-bottom">
                    <!-- General Info Tab -->
                    <div class="tab-pane fade" id="generalInfo">
                        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="fullnameInput" placeholder="Enter your Full name" value="{{ old('name', $customer->name) }}">
                                        @error('name')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Mã khách hàng</label>
                                        <input disabled type="text" class="form-control" name="name" id="fullnameInput" placeholder="Enter your customer code" value="{{ old('customer_code', $customer->customer_code) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Mã số thuế <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="tax_code" id="fullnameInput" placeholder="Enter your tax_code" value="{{ old('tax_code', $customer->tax_code) }}">
                                        @error('tax_code')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Ngày thành lập</label>
                                        <input type="date" class="form-control" name="establishment_date" value="@formatDateForInput($customer?->establishment_date)">
                                        @error('establishment_date')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Loại khách hàng: <span class="text-danger">*</span></label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="individualType" value="individual" {{ $customer->type == \App\Models\Customer::TYPE_INDIVIDUAL ? 'checked' : '' }}>
                                            <label class="form-check-label" for="individualType">Cá nhân</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="businessType" value="business" {{ $customer->type == \App\Models\Customer::TYPE_BUSINESS ? 'checked' : '' }}>
                                            <label class="form-check-label" for="businessType">Doanh nghiệp</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Địa chỉ </label>
                                        <input type="text" class="form-control" name="address" id="fullnameInput" placeholder="Enter your address" value="{{ old('address', $customer->address) }}">
                                        @error('address')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="phone" id="fullnameInput" placeholder="Enter your address" value="{{ old('phone', $customer->phone) }}">
                                        @error('phone')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="fullnameInput" placeholder="Enter your email" value="{{ old('email', $customer->email) }}">
                                        @error('email')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Website </label>
                                        <input type="text" class="form-control" name="website" id="fullnameInput" placeholder="Enter your website" value="{{ old('website', $customer->website) }}">
                                        @error('website')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">File bảng kê</label>
                                        <input type="file" name="document_file" id="documentFileInput" class="form-control mt-1 border p-2 rounded">
                                        @if(session()->has('_documentFile_temp'))
                                            <input type="hidden" name="_documentFile_temp" value="{{ session('_documentFile_temp') ?? null }}">
                                        @endif
                                        @if ($customer->document_file)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $customer->document_file) }}" target="_blank">
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
                    <div class="tab-pane fade" id="monthlyReport">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="flex-shrink-0 text-start">
                                <h6 class="mb-1">Tháng</h6>
                                <input type="month" id="monthSelector" class="form-control" value="{{ date('Y-m') }}">
                            </div>
                            <div>
                                <button type="button" id="exportInvoice" class="btn btn-outline-primary">
                                    <i class="las la-file-invoice align-middle me-1"></i> Xuất bảng kê
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="monthlyReportTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã chuyến hàng</th>
                                        <th>Ngày</th>
                                        <th>Điểm đi</th>
                                        <th>Điểm đến</th>
                                        <th>Số chuyến</th>
                                        <th>Khối lượng xe (kg)</th>
                                        <th>Đơn giá</th>
                                        <th>Phụ thu</th>
                                        <th>Thành tiền</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--  @if(isset($monthlyShipments) && count($monthlyShipments) > 0)
                                        @foreach($monthlyShipments as $shipment)
                                            <tr>
                                                <td>{{ $shipment['shipment_code'] }}</td>
                                                <td>{{ $shipment['departure_time'] }}</td>
                                                <td>{{ $shipment['origin'] }}</td>
                                                <td>{{ $shipment['destination'] }}</td>
                                                <td>{{ $shipment['trip_count'] }}</td>
                                                <td>{{ $shipment['cargo_weight'] }}</td>
                                                <td>{{ number_format($shipment['unit_price']) }}</td>
                                                <td>{{ $shipment['combined_fees'] > 0 ? number_format($shipment['combined_fees']) : '' }}</td>
                                                <td>{{ number_format($shipment['total_amount']) }}</td>
                                                <td>{{ $shipment['notes'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="11" class="text-center">Không có dữ liệu chuyến hàng trong tháng này</td>
                                        </tr>
                                    @endif  --}}
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary fw-bold">
                                        <td colspan="4">Tổng cộng</td>
                                        <td id="totalTrips">{{ isset($monthlyShipments) ? $monthlyShipments->sum('trip_count') : 0 }}</td>
                                        <td id="totalWeight">{{ isset($monthlyShipments) ? $monthlyShipments->sum('cargo_weight') : 0 }}</td>
                                        <td></td>
                                        <td id="totalCombinedFees">{{ isset($monthlyShipments) ? number_format($monthlyShipments->sum('combined_fees')) : 0 }}</td>
                                        <td id="grandTotal">{{ isset($monthlyShipments) ? number_format($monthlyShipments->sum('total_amount')) : 0 }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Transactions Tab -->
                    <div class="tab-pane fade show active" id="transactions">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Lịch sử giao dịch</h6>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#transactionModal"><i class="fas fa-plus me-1"></i>Thêm giao dịch</button>
                        </div>
                        
                        <!-- Search Form -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Tìm kiếm giao dịch</h6>
                            </div>
                            <div class="card-body">
                                <form id="transactionSearchForm" method="GET" action="{{ route('admin.customers.transactions', $customer) }}">
                                    <input type="hidden" name="active_tab" value="transactions">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Từ ngày</label>
                                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') ?? '' }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Đến ngày</label>
                                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') ?? '' }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Số tiền từ</label>
                                            <input type="text" name="amount_min" class="form-control number-format" value="{{ request('amount_min') ?? '' }}" placeholder="Số tiền từ">
                                            @error('amount_min')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Số tiền đến</label>
                                            <input type="text" name="amount_max" class="form-control number-format" value="{{ request('amount_max') ?? '' }}" placeholder="Số tiền đến">
                                            @error('amount_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <label class="form-label">Loại giao dịch</label>
                                            <select name="transaction_type" class="form-select">
                                                <option value="">Tất cả</option>
                                                @foreach(\App\Models\Transaction::getTypes() as $key => $type)
                                                    <option value="{{ $key }}" {{ request('transaction_type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @error('transaction_type')
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
                                                <button type="submit" class="btn btn-primary px-4">
                                                    <i class="fas fa-search me-2"></i>Tìm kiếm
                                                </button>
                                                <a href="{{ route('admin.customers.transactions', $customer) }}?active_tab=transactions" class="btn btn-outline-secondary px-4">
                                                    <i class="fas fa-undo me-2"></i>Đặt lại
                                                </a>
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
                <h5 class="modal-title">Thêm giao dịch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <form id="transactionForm" enctype="multipart/form-data" method="POST" action="{{ route('admin.customers.store-transaction', $customer) }}">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                            <input class="form-control number-format" type="text" placeholder="Số tiền" name="amount" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày thanh toán <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="payment_date" value="@formatDateForInput(date('Y-m-d'))" required />
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
                        <textarea class="form-control" rows="3" placeholder="Nhập chú thích" name="notes"></textarea>
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
                            <input class="form-control number-format" type="text" placeholder="Số tiền" name="amount" id="edit_amount" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày thanh toán <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="payment_date" id="edit_payment_date" required />
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
                        <textarea class="form-control" rows="3" placeholder="Nhập chú thích" name="notes" id="edit_notes"></textarea>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Handle tab activation to ensure data is loaded when monthly report tab is clicked
        const tabLinks = document.querySelectorAll('.nav-link');
        if (tabLinks) {
            tabLinks.forEach(tab => {
                tab.addEventListener('click', function(e) {
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
            monthSelector.addEventListener('change', function() {
                loadMonthlyReport(customerId, this.value);
            });
        }
        
        // Function to load monthly report data
        function loadMonthlyReport(customerId, month) {
            // Show loading indicator
            const tableBody = document.querySelector('#monthlyReportTable tbody');
            const loadingRow = '<tr><td colspan="11" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Đang tải dữ liệu...</td></tr>';
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
                        
                        // Update totals
                        totalTrips += parseInt(shipment.trip_count) || 0;
                        totalWeight += parseFloat(shipment.cargo_weight) || 0;
                        totalCombinedFees += parseFloat(shipment.combined_fees) || 0;
                        grandTotal += parseFloat(shipment.total_amount) || 0;
                        
                        // Format the row HTML
                        row.innerHTML = `
                            <td>${shipment.shipment_code}</td>
                            <td>${shipment.departure_time}</td>
                            <td>${shipment.origin}</td>
                            <td>${shipment.destination}</td>
                            <td>${shipment.trip_count}</td>
                            <td>${shipment.cargo_weight}</td>
                            <td>${numberFormat(shipment.unit_price)}</td>
                            <td>${shipment.combined_fees > 0 ? numberFormat(shipment.combined_fees) : ''}</td>
                            <td>${numberFormat(shipment.total_amount)}</td>
                            <td>${shipment.notes || ''}</td>
                        `;
                        
                        tableBody.appendChild(row);
                    });
                    
                    // Update footer totals
                    document.getElementById('totalTrips').textContent = totalTrips;
                    document.getElementById('totalWeight').textContent = totalWeight.toFixed(2);
                    document.getElementById('totalCombinedFees').textContent = numberFormat(totalCombinedFees);
                    document.getElementById('grandTotal').textContent = numberFormat(grandTotal);
                } else {
                    // No data found
                    tableBody.innerHTML = '<tr><td colspan="11" class="text-center">Không có dữ liệu chuyến hàng trong tháng này</td></tr>';
                    
                    // Reset footer totals
                    document.getElementById('totalTrips').textContent = '0';
                    document.getElementById('totalWeight').textContent = '0';
                    document.getElementById('totalCombinedFees').textContent = '0';
                    document.getElementById('grandTotal').textContent = '0';
                }
                
                // Re-enable controls
                if (monthSelector) monthSelector.disabled = false;
                const exportBtn = document.getElementById('exportMonthlyReport');
                if (exportBtn) exportBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching monthly report:', error);
                tableBody.innerHTML = '<tr><td colspan="11" class="text-center text-danger">Lỗi khi tải dữ liệu. Vui lòng thử lại.</td></tr>';
                
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
        
        // Handle invoice export button click
        const invoiceButton = document.getElementById('exportInvoice');
        if (invoiceButton) {
            invoiceButton.addEventListener('click', function() {
                try {
                    const month = monthSelector.value;
                    const monthDate = new Date(month + '-01');
                    
                    // Validate month format
                    if (isNaN(monthDate.getTime())) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Định dạng tháng không hợp lệ. Vui lòng chọn lại.'
                        });
                        return;
                    }
                    
                    // Check if there's data in the table
                    const rows = document.querySelectorAll('#monthlyReportTable tbody tr');
                    if (rows.length === 1 && rows[0].cells.length === 1 && rows[0].cells[0].colSpan === 11) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Không có dữ liệu',
                            text: 'Không có dữ liệu để xuất bảng kê'
                        });
                        return;
                    }
                    
                    // Get customer ID from URL
                    const urlParts = window.location.pathname.split('/');
                    const customerId = urlParts[urlParts.indexOf('customers') + 1];
                    
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Xác nhận xuất bảng kê?',
                        text: 'Bạn có chắc chắn muốn xuất bảng kê cho tháng này không?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Có, xuất ngay',
                        cancelButtonText: 'Hủy bỏ',
                        customClass: {
                            confirmButton: 'btn btn-secondary',
                            cancelButton: 'btn btn-light'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    
                                    // Create a fetch request to the export URL
                                    fetch(`${window.location.origin}/admin/customers/${customerId}/export-invoice?month=${month}&tax_rate=10`, {
                                        method: 'GET',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Network response was not ok');
                                        }
                                        return response.blob();
                                    })
                                    .then(blob => {
                                        // Create a download link and trigger it
                                        const url = window.URL.createObjectURL(blob);
                                        const link = document.createElement('a');
                                        link.href = url;
                                        
                                        // Create filename with customer name and month/year
                                        const customerName = '{{ $customer->name }}'.replace(/\s+/g, '_');
                                        const monthYear = month.replace('-', '_');
                                        const filename = `Bang_ke_${customerName}_${monthYear}.xlsx`;
                                        link.setAttribute('download', filename);
                                        
                                        document.body.appendChild(link);
                                        link.click();
                                        document.body.removeChild(link);
                                        
                                        // Show success message after download starts
                                        Swal.close();
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Xuất bảng kê thành công',
                                            showConfirmButton: false,
                                            timer: 2000
                                        });
                                    })
                                    .catch(error => {
                                        console.error('Error downloading file:', error);
                                        Swal.close();
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Lỗi',
                                            text: 'Đã xảy ra lỗi khi xuất bảng kê. Vui lòng thử lại sau.'
                                        });
                                    });
                                }
                            });
                        }
                    });
                } catch (error) {
                    console.error('Lỗi khi xuất bảng kê:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi xuất bảng kê. Vui lòng thử lại sau.'
                    });
                }
            });
        }
                
        // Transaction form submission
        $('#transactionForm').on('submit', function(e) {
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
                    success: function(response) {
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
                    error: function(xhr) {
                        // Show error message
                        let errorMessage = 'Đã xảy ra lỗi khi tạo giao dịch.';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            // Handle validation errors
                            if (xhr.responseJSON.errors) {
                                errorMessage = '<ul>';
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    errorMessage += '<li>' + value + '</li>';
                                });
                                errorMessage += '</ul>';
                            }
                        }
                        
                        errorContainer.html(errorMessage).show();
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html('Lưu');
                    }
                });
            });
            
        // Transaction search form AJAX submission
        $('#transactionSearchForm').on('submit', function(e) {
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
                success: function(response) {
                    if (response.success) {
                        resultsContainer.html(response.html);
                    } else {
                        resultsContainer.html('<div class="alert alert-danger">' + (response.message || 'Đã xảy ra lỗi khi tìm kiếm giao dịch.') + '</div>');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        
                        // Display validation errors for each field
                        $.each(errors, function(field, messages) {
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
        $(document).on('click', '#transaction-pagination .pagination a', function(e) {
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
                success: function(response) {
                    if (response.success) {
                        resultsContainer.html(response.html);
                    } else {
                        resultsContainer.html('<div class="alert alert-danger">' + (response.message || 'Đã xảy ra lỗi khi tải dữ liệu.') + '</div>');
                    }
                },
                error: function(xhr) {
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
        $('.number-format').each(function() {
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
        $(document).on('click', '.edit-transaction', function() {
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
        $('#editTransactionForm').on('submit', function(e) {
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
                success: function(response) {
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
                error: function(xhr) {
                    // Show error message
                    let errorMessage = 'Đã xảy ra lỗi khi cập nhật giao dịch.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Handle validation errors
                        if (xhr.responseJSON.errors) {
                            errorMessage = '<ul>';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessage += '<li>' + value + '</li>';
                            });
                            errorMessage += '</ul>';
                        }
                    }
                    
                    errorContainer.html(errorMessage).show();
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html('Cập nhật');
                }
            });
        });
        
        // Handle delete transaction button click
        $(document).on('click', '.delete-transaction', function() {
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
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: response.message || 'Giao dịch đã được xóa thành công.'
                            }).then(() => {
                                // Reload the page to update the transaction list
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
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
    });
</script>
@endpush