@extends('admin.layout')
@section('title', 'Quản lý xe')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                                        <i class="ri-add-circle-line align-middle me-1"></i>Thêm phương tiện
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
            <!-- Dashboard Cards -->

            <!-- Filter Section -->
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <form method="GET" action="{{ route('admin.vehicles.index') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select class="form-select" name="vehicle_type_id" id="vehicle_type_id">
                                    <option value="">Tất cả loại xe</option>
                                    @foreach ($vehicleTypes as $key => $val)
                                        <option value="{{ $key }}" {{ request('vehicle_type_id') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="statusFilter" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    @foreach ($vehicleStatuses as $val => $label)
                                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="is_car_rental" id="is_car_rental">
                                    <option value="">Tất cả loại xe</option>
                                    <option value="0" {{ request('is_car_rental') === '0' ? 'selected' : '' }}>Xe HPL</option>
                                    <option value="1" {{ request('is_car_rental') === '1' ? 'selected' : '' }}>Xe HPL Thuê</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input name="keyword" type="text" class="form-control" placeholder="Tìm kiếm..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-info w-100">
                                    <i class="ri-search-line me-1"></i>Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vehicles Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>Thao tác</th>
                                    <th>Biển số</th>
                                    <th>Loại xe</th>
                                    <th>Tài xế</th>
                                    <th>Tải trọng</th>
                                    <th>Trạng thái</th>
                                    <th>Xe HPL Thuê</th>
                                    <th>Đăng kiểm</th>
                                    <th>Bảo hiểm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehicles as $vehicle)
                                    <tr>
                                        <td>
                                            <div class="btn-group">
                                                <button 
                                                    class="btn btn-sm btn-outline-primary btn-show-vehicle" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal"
                                                    data-id="{{ $vehicle->vehicle_id }}"
                                                >
                                                    Chi tiết
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-vehicle-btn"
                                                        data-vehicle-id="{{ $vehicle->id }}">
                                                    Xóa
                                                </button>
                                                
                                                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                                                    method="POST"
                                                    class="delete-vehicle-form"
                                                    id="delete-form-{{ $vehicle->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                        <td>{{ $vehicle->plate_number }}</td>
                                        <td>{{ optional($vehicle->vehicleType)->name }}</td>
                                        <td>{{ optional($vehicle->driver)->full_name }}</td>
                                        <td>{{ $vehicle->capacity }}</td>
                                        <td class="py-2 px-4">
                                            <span 
                                                class="
                                                    status-indicator 
                                                    status-active 
                                                    text-{{ $vehicle->getStatusBadgeClassAttribute() }}
                                                "
                                            >
                                                {{ $vehicle->getStatusLabelAttribute() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($vehicle->is_car_rental)
                                                <span class="badge bg-info-subtle text-info">
                                                    <i class="ri-car-line me-1"></i>Xe HPL Thuê
                                                </span>
                                                @if($vehicle->customer)
                                                    <br><small class="text-muted">{{ $vehicle->customer->name }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    <i class="ri-building-line me-1"></i>Xe HPL
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $vehicleInspection = $vehicle->getLatestDocument(\App\Models\VehicleDocument::TYPE_INSPECTION);
                                            @endphp
                                            @if ($vehicleInspection && $vehicleInspection->expiry_date)
                                                @if ($vehicle->isDocumentExpired(\App\Models\VehicleDocument::TYPE_INSPECTION)) 
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        Hết hạn (@formatDate($vehicleInspection->expiry_date))
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success ">Còn hạn(@formatDate($vehicleInspection->expiry_date))</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $vehicleInsurance = $vehicle->getLatestDocument(\App\Models\VehicleDocument::TYPE_INSURANCE);
                                            @endphp
                                            @if ($vehicleInsurance && $vehicleInsurance->expiry_date)
                                                @if ($vehicle->isDocumentExpired(\App\Models\VehicleDocument::TYPE_INSURANCE)) 
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        Hết hạn (@formatDate($vehicleInsurance->expiry_date))
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success ">Còn hạn(@formatDate($vehicleInsurance->expiry_date))</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{ $vehicles->links('vendor.pagination.bootstrap-5') }}
        </div> <!-- end col -->
    </div>
</div>
<!-- container-fluid -->

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVehicleModalLabel">Thêm phương tiện mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <form id="add-vehicle-form" enctype="multipart/form-data" action="{{ route('admin.vehicles.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <!-- Checkbox Xe HPL Thuê -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" name="is_car_rental" class="form-check-input" id="modalIsCarRental" value="1">
                                <label class="form-check-label" for="modalIsCarRental">Xe HPL Thuê</label>
                                <div class="text-danger error" data-field="is_car_rental"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Khách hàng cho thuê xe (ẩn mặc định) -->
                    <div id="modalCarRentalCustomerForm" style="display: none;">
                        <hr>
                        <h6>Thông tin khách hàng cho thuê xe</h6>
                        <small class="badge bg-danger-subtle text-danger mb-1">Bạn có thể chọn 1 khách hàng hoặc nhập thông tin khách hàng mới</small>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Chọn đối tác</label>
                                <select name="customer_id" class="form-select" id="modalCustomerId">
                                    <option value="">Chọn đối tác</option>
                                    @foreach($carRentalCustomers as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger error" data-field="customer_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tên đối tác cho thuê xe <span class="text-danger">*</span></label>
                                <input name="customer_name" type="text" placeholder="Tên Tên đối tác cho thuê xe" class="form-control" id="modalCustomerName">
                                <div class="text-danger error" data-field="customer_name"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input name="customer_phone" type="text" placeholder="Số điện thoại" class="form-control" id="modalCustomerPhone">
                                <div class="text-danger error" data-field="customer_phone"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input name="customer_email" type="email" placeholder="Email" class="form-control" id="modalCustomerEmail">
                                <div class="text-danger error" data-field="customer_email"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Địa chỉ</label>
                                <textarea name="customer_address" placeholder="Địa chỉ" class="form-control" id="modalCustomerAddress"></textarea>
                                <div class="text-danger error" data-field="customer_address"></div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Biển số xe <span class="text-danger">*</span></label>
                            <input type="text" name="plate_number" id="plate_number" class="form-control" placeholder="Nhập biển số xe">
                            <div class="text-danger error" data-field="plate_number"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại phương tiện <span class="text-danger">*</span></label>
                            <select class="form-select" name="vehicle_type_id" id="vehicle_type_id">
                                <option value="">Chọn loại phương tiện</option>
                                @foreach ($vehicleTypes as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="vehicle_type_id"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tải trọng (tấn)</label>
                            <input type="number" step="0.1" class="form-control" name="capacity" id="capacity">
                            <div class="text-danger error" data-field="capacity"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Năm sản xuất</label>
                            <input type="number" class="form-control" name="manufactured_year" id="manufactured_year">
                            <div class="text-danger error" data-field="manufactured_year"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" name="status">
                                @foreach ($vehicleStatuses as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="status"></div>
                        </div>
                        <div class="col-md-6" id="driverSelect">
                            <label class="form-label">Tài xế </label>
                            <select class="form-select" name="driver_id">
                                <option value="">Chọn tài xế</option>
                                @foreach ($drivers as $key => $driver)
                                    <option value="{{ $key }}">{{ $driver }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="driver_id"></div>
                        </div>
                    </div>
                    <hr>
                    <div id="documentsGroup">
                        <h6>Thông tin đăng kiểm</h6>
                        <input type="text" class="form-control" name="documents[0][document_type]" value="{{ \App\Models\VehicleDocument::TYPE_INSPECTION }}" hidden>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Số giấy đăng kiểm </label>
                                <input type="text" class="form-control" name="documents[0][document_number]">
                                <div class="text-danger error" data-field="documents.0.document_number"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" class="form-control" name="documents[0][expiry_date]" value="@formatDateForInput(old('documents.0.expiry_date'))">
                                <div class="text-danger error" data-field="documents.0.expiry_date"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tệp đính kèm</label>
                            <input type="file" class="form-control" name="documents[0][document_file]" >
                            <div class="text-danger error" data-field="documents.0.document_file"></div>
                        </div>
                        <hr>
                        <h6>Thông tin bảo hiểm</h6>
                        <div class="row mb-3">
                            <input type="text" class="form-control" name="documents[1][document_type]" value="{{ \App\Models\VehicleDocument::TYPE_INSURANCE }}" hidden>
                            <div class="col-md-6">
                                <label class="form-label">Số hợp đồng bảo hiểm </label>
                                <input type="text" class="form-control" name="documents[1][document_number]">
                                <div class="text-danger error" data-field="documents.1.document_number"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày hết hạn </label>
                                <input type="date" class="form-control" name="documents[1][expiry_date]" value="@formatDateForInput(old('documents.1.expiry_date'))">
                                <div class="text-danger error" data-field="documents.1.expiry_date"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tệp đính kèm </label>
                            <input type="file" class="form-control" name="documents[1][document_file]" >
                            <div class="text-danger error" data-field="documents.1.document_file"></div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu phương tiện</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Vehicle Detail Modal -->
{{--  <div id="modalContainer"></div>  --}}

@include('admin.modals.loading_modal')

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Toggle car rental customer form in modal
        $('#modalIsCarRental').on('change', function() {
            const modalCarRentalCustomerForm = $('#modalCarRentalCustomerForm');
            const documentsGroup = $('#documentsGroup');
            const driverSelect  = $('#driverSelect');
            if (this.checked) {
                modalCarRentalCustomerForm.show();
                documentsGroup.hide();
                driverSelect.hide();
            } else {
                modalCarRentalCustomerForm.hide();
                documentsGroup.show();
                driverSelect.show();
            }
        });

        // Auto-fill customer data when customer is selected
        $('select[name="customer_id"]').on('change', function() {
            const customerId = $(this).val();
            const customerNameField = $('#modalCustomerName');
            const customerPhoneField = $('#modalCustomerPhone');
            const customerEmailField = $('#modalCustomerEmail');
            const customerAddressField = $('#modalCustomerAddress');
            
            if (customerId) {
                // Fetch customer data via AJAX
                $.ajax({
                    url: `/admin/customers/${customerId}?get_customer_data=1`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        // Fill customer data into form fields
                        customerNameField.val(data.name);
                        customerPhoneField.val(data.phone);
                        customerEmailField.val(data.email);
                        customerAddressField.val(data.address);
                        
                        // Disable input fields when customer is selected
                        customerNameField.prop('disabled', true);
                        customerPhoneField.prop('disabled', true);
                        customerEmailField.prop('disabled', true);
                        customerAddressField.prop('disabled', true);
                        
                        // Clear validation errors
                        $('.error[data-field="customer_name"]').text('');
                        $('.error[data-field="customer_phone"]').text('');
                        $('.error[data-field="customer_email"]').text('');
                        $('.error[data-field="customer_address"]').text('');
                    },
                    error: function(xhr) {
                        console.error('Error fetching customer data:', xhr);
                    }
                });
            } else {
                // Clear form fields if no customer selected
                customerNameField.val('');
                customerPhoneField.val('');
                customerEmailField.val('');
                customerAddressField.val('');
                
                // Enable input fields when no customer is selected
                customerNameField.prop('disabled', false);
                customerPhoneField.prop('disabled', false);
                customerEmailField.prop('disabled', false);
                customerAddressField.prop('disabled', false);
            }
        });

        // Frontend validation for modal form
        $('#add-vehicle-form').on('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            $('.error').text('');
            
            // Validate car rental fields if checkbox is checked
            if ($('#modalIsCarRental').is(':checked')) {
                const customerId = $('#modalCustomerId').val();
                const customerName = $('#modalCustomerName').val().trim();
                const customerPhone = $('#modalCustomerPhone').val().trim();
                const customerEmail = $('#modalCustomerEmail').val().trim();
                
                // If no customer selected, validate required fields
                if (!customerId) {
                    if (!customerName) {
                        $('.error[data-field="customer_name"]').text('Tên khách hàng là bắt buộc');
                        isValid = false;
                    }
                    
                    if (!customerPhone) {
                        $('.error[data-field="customer_phone"]').text('Số điện thoại khách hàng là bắt buộc');
                        isValid = false;
                    }
                    
                    // if (!customerEmail) {
                    //     $('.error[data-field="customer_email"]').text('Email khách hàng là bắt buộc');
                    //     isValid = false;
                    // } else 
                    if (!isValidEmail(customerEmail)) {
                        $('.error[data-field="customer_email"]').text('Email không đúng định dạng');
                        isValid = false;
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
        
        // Email validation function
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Reset modal form when modal is hidden
        $('#addVehicleModal').on('hidden.bs.modal', function () {
            $('#add-vehicle-form')[0].reset();
            $('#modalCarRentalCustomerForm').hide();
            $('#add-vehicle-form .error').text('');
        });

        $('.delete-vehicle-btn').click(function (e) {
            e.preventDefault();
    
            const vehicleId = $(this).data('vehicle-id');
            const form = $('#delete-form-' + vehicleId);
    
            Swal.fire({
                title: 'Bạn chắc chắn muốn xóa?',
                // text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        ['#add-vehicle-form'].forEach(function (formSelector) {
            const $form = $(formSelector);
            if ($form.length) {
                $form.on('submit', function (e) {
                    e.preventDefault();

                    const url = $form.attr('action');
                    const formData = new FormData(this);

                    // Xóa lỗi cũ
                    $form.find('.error').text('');

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        success: function (data) {
                            // close modal
                            const modalElement = $form.closest('.modal');
                            const modal = bootstrap.Modal.getInstance(modalElement[0]);
                            if (modal) modal.hide();

                            // Reset form
                            $form[0].reset();

                            // 
                            Swal.fire({
                                title: "Tạo thành công!",
                                icon: "success",
                                draggable: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Reload table
                                    location.reload();
                                }
                            });
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                $.each(errors, function (field, messages) {
                                    $form.find(`.error[data-field="${field}"]`).text(messages[0]);
                                });
                            } else {
                                console.error('Có lỗi xảy ra:', xhr);
                            }
                        }
                    });
                });
            }
        });

        $('.btn-show-vehicle').on('click', function () {
            let id = $(this).data('id');
            let modal = $('#detailModal');
            
            $('#detailContentModal').html('<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></div>');
            
            $('#editDetailBtn').data('id', id);

            // show modal
            modal.modal('show');
            
            $.ajax({
                url: `/admin/vehicles/${id}`,
                type: 'GET',
                success: function(response) {
                    $('#detailContentModal').html(response);
                },
                error: function(xhr) {
                    $('#detailContentModal').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.</div>');
                    console.error(xhr);
                }
            });
        });

        $('#editDetailBtn').on('click', function () {
            let id = $(this).data('id');
            if (id) {
                window.location.href = `/admin/vehicles/${id}/edit`;
            }
        });

    });
</script>
@endpush