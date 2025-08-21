@extends('admin.layout')
@section('title', 'Quản lý thuê xe')
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
                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addCarRentalModal">
                                            <i class="ri-add-circle-line align-middle me-1"></i>Tạo thuê xe
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

                <!-- Filter Section -->
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.car-rental.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input name="keyword" value="{{ request('keyword') }}" type="text"
                                            class="form-control" placeholder="Nhập tên Khách hàng, Công ty,...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="typeFilter" name="type">
                                        <option value="">Chọn loại thuê xe</option>
                                        <option value="1" @selected(request()->type == 1)>Thuê xe theo chuyến</option>
                                        <option value="2" @selected(request()->type == 2)>Thuê xe kiểu khoáng</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" id="statusFilter" name="status">
                                        <option value="">Tất cả trạng thái</option>
                                        @foreach ($carRentalstatuses as $val => $label)
                                            <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-info w-100">
                                        <i class="ri-search-line me-1"></i>
                                        Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-hover align-middle table-nowrap mb-0">
                                <thead class="table-light text-uppercase">
                                    <tr>
                                        <th>Thao tác</th>
                                        <th>Trạng thái</th>
                                        <th>Khách hàng</th>
                                        <th>Thành tiền(VAT)</th>
                                        <th>Loại</th>
                                        <th>Tổng kết công nợ</th>
                                        <th>Ngày tạo</th>
                                        <th>File báo giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($carRentals as $carRental)
                                        <tr>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.car-rental.edit', $carRental) }}"
                                                        class="btn btn-sm btn-outline-primary ">Chi tiết</a>
                                                    {{--  @if ($carRental->status == \App\Models\CarRental::STATUS_APPROVED)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary summarize-debt-btn"
                                                        data-car-rental-id="{{ $carRental->id }}"
                                                        data-car-rental-type="{{ $carRental->type }}"
                                                        data-start-date="{{ $carRental->start_date }}"
                                                        data-end-date="{{ $carRental->end_date }}">
                                                        <i class="las la-calculator me-1"></i>
                                                        Tổng kết công nợ
                                                    </button>
                                                    @endif  --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-car-rental-btn"
                                                        data-car-rental-id="{{ $carRental->id }}">
                                                        Xóa
                                                    </button>

                                                    <form action="{{ route('admin.car-rental.destroy', $carRental) }}"
                                                        method="POST" class="delete-quote-form"
                                                        id="delete-form-{{ $carRental->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-{{ $carRental->getStatusColorAttribute() }}">{{ $carRental->getStatusLabelAttribute() }}</span></td>
                                            <td>
                                                 @if($carRental->customer)
                                                    <a href="{{ route('admin.customers.show', parameters: optional($carRental->customer)->id) }}" class="text-primary" target="_blank">
                                                        {{ $carRental->customer->name ?? '' }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ number_format($carRental->total_amount_with_vat) }}</td>
                                            <td>{{ $carRental->getTypeLabelAttribute() }}</td>
                                            <td>
                                                @if($carRental->shipmentReports && $carRental->shipmentReports->count() > 0)
                                                    <span class="badge bg-success">
                                                        <i class="las la-check me-1"></i>
                                                        Đã tổng kết
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="las la-clock me-1"></i>
                                                        Chưa tổng kết
                                                    </span>
                                                @endif
                                            </td>
                                            <td>@formatDate($carRental->created_at)</td>
                                            <td>
                                                @if ($carRental->file)
                                                    <a href="{{ $carRental->file }}" class="" target="_blank">Tải báo giá</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{ $carRentals->links('vendor.pagination.bootstrap-5') }}
            </div> <!-- end col -->
        </div>

    </div>
    <!-- container-fluid -->

    <!-- Add Car Rental Modal -->
    <div class="modal fade" id="addCarRentalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thuê xe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr>
                <form id="add-car-rental-form" enctype="multipart/form-data" action="{{ route('admin.car-rental.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <label class="form-label fs-5">Loại thuê xe <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <div class="form-check form-radio-primary mb-3">
                                <input class="form-check-input" type="radio" name="type" value="1" id="type1" {{ old('type', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type1">
                                    Thuê nguyên xe tính theo chuyến
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-radio-primary mb-3">
                                <input class="form-check-input" type="radio" name="type" value="2" id="type2" {{ old('type') == '2' ? 'checked' : '' }}>
                                <label class="form-check-label" for="type2">
                                    Thuê xe theo kiểu khoáng
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <select class="form-select" name="customer_id">
                                <option value="">Chọn khách hàng</option>
                                @foreach ($customers as $key => $customer)
                                    <option value="{{ $key }}">{{ $customer }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="customer_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" name="status">
                                @foreach ($carRentalstatuses as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="status"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" class="form-control date-input" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control date-input" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Phí thuê xe theo tháng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control number" name="monthly_rental_fee" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Phương tiện</label>
                                <select class="form-select" name="vehicle_id" id="vehicles">
                                    <option value="">Chọn phương tiện</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ (int)$vehicle->vehicle_id }}" {{ old('vehicle_id') == (int)$vehicle->vehicle_id ? 'selected' : '' }}>{{ $vehicle->plate_number . '-' . $vehicle->vehicleType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Điểm đi</label>
                                <input type="text" class="form-control" name="departure_point" placeholder="Nhập điểm đi">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Điểm đến</label>
                                <input type="text" class="form-control" name="destination_point" placeholder="Nhập điểm đến">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Số km tối đa</label>
                                <input type="text" class="form-control number" name="max_distance">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Phí theo km chạy vượt</label>
                                <input type="text" class="form-control number" name="over_distance_fee_per_km">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">Giờ bắt đầu làm việc</label>
                                <input type="time" class="form-control" name="start_working_hour" value="07:30">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">Giờ kết thúc làm việc</label>
                                <input type="time" class="form-control" name="end_working_hour" value="17:00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">Phí tăng ca/giờ</label>
                                <input type="text" class="form-control number" name="overtime_fee_per_hour" value="{{ old('overtime_fee_per_hour', '50000') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">Số hóa đơn</label>
                                <input type="text" class="form-control" name="invoice_number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">Số bảng kê</label>
                                <input type="text" class="form-control" name="statement_number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="form-label">Hợp đồng số</label>
                                <input type="text" class="form-control" name="contract_number">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tên hàng hóa</label>
                        <input class="form-control" placeholder="Nhập tên hàng hóa"name="product_name"></input>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả dịch vụ</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ"
                            name="description"></textarea>
                        <div class="text-danger error" data-field="description"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập ghi chú" name="notes"></textarea>
                        <div class="text-danger error" data-field="notes"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File báo giá</label>
                        <input type="file" class="form-control" name="file">
                        <div class="text-danger error" data-field="file"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tổng kết công nợ -->
    <div class="modal fade" id="debtSummaryModal" tabindex="-1" aria-labelledby="debtSummaryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="debtSummaryModalLabel">Xác nhận tổng kết công nợ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="debtSummaryForm">
                        <div class="mb-3">
                            <label for="reportStartDate" class="form-label">Từ ngày</label>
                            <input type="text" class="form-control" readonly id="reportStartDate" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="reportEndDate" class="form-label">Đến ngày</label>
                            <input type="text" class="form-control" readonly id="reportEndDate" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại tổng kết</label>
                            <input type="text" class="form-control" readonly id="reportType">
                        </div>
                        <input type="hidden" id="carRentalId">
                        <input type="hidden" id="carRentalType">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="submitSummarize">Tổng kết</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.modals.loading_modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/car-rental.js') }}"></script>
    <script>
        $(document).ready(function () {
            const hasOldVehicles = {{ count(old('vehicles', [])) > 0 ? 'true' : 'false' }};

            if (!hasOldVehicles) {
                addVehicleRow();
            }

            $('#add-vehicle-btn').on('click', addVehicleRow);

            $('#vehicle-rows').on('click', '.remove-row', removeVehicleRow);

            $('.delete-car-rental-btn').click(function (e) {
                e.preventDefault();

                const carRentalId = $(this).data('car-rental-id');
                const form = $('#delete-form-' + carRentalId);

                Swal.fire({
                    title: 'Bạn chắc chắn muốn xóa?',
                    // text: "Hành động này không thể hoàn tác!",
                    icon: 'warning'
                    , showCancelButton: true
                    , confirmButtonColor: '#d33'
                    , cancelButtonColor: '#3085d6'
                    , confirmButtonText: 'Xóa'
                    , cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            ['#add-car-rental-form'].forEach(function (formSelector) {
                const $form = $(formSelector);
                if ($form.length) {
                    $form.on('submit', function (e) {
                        e.preventDefault();

                        const url = $form.attr('action');
                        const formData = new FormData(this);

                        // Xóa lỗi cũ
                        $form.find('.error').text('');

                        $.ajax({
                            url: url
                            , method: 'POST'
                            , data: formData
                            , contentType: false
                            , processData: false
                            , headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                                , 'Accept': 'application/json'
                                ,
                            }
                            , success: function (data) {
                                // close modal
                                const modalElement = $form.closest('.modal');
                                const modal = bootstrap.Modal.getInstance(modalElement[
                                    0]);
                                if (modal) modal.hide();

                                // Reset form
                                $form[0].reset();

                                //
                                Swal.fire({
                                    title: "Tạo thành công!"
                                    , icon: "success"
                                    , draggable: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Reload table
                                        location.reload();
                                    }
                                });
                            }
                            , error: function (xhr) {
                                if (xhr.status === 422) {
                                    const errors = xhr.responseJSON.errors;
                                    $.each(errors, function (field, messages) {
                                        $form.find(
                                            `.error[data-field="${field}"]`)
                                            .text(messages[0]);
                                    });
                                } else {
                                    console.error('Có lỗi xảy ra:', xhr);
                                }
                            }
                        });
                    });
                }
            });
        });

        // Tổng kết công nợ
        $('.summarize-debt-btn').click(function() {
            const carRentalId = $(this).data('car-rental-id');
            const carRentalType = $(this).data('car-rental-type');
            const startDate = $(this).data('start-date');
            const endDate = $(this).data('end-date');

            // Hiển thị modal tổng kết
            $('#debtSummaryModal').modal('show');
            
            // Cập nhật thông tin trong modal
            $('#debtSummaryModal #carRentalId').val(carRentalId);
            $('#debtSummaryModal #carRentalType').val(carRentalType);
            $('#debtSummaryModal #reportStartDate').val(startDate);
            $('#debtSummaryModal #reportEndDate').val(endDate);
            $('#debtSummaryModal #reportType').val(carRentalType == 1 ? 'Thuê nguyên xe tính theo chuyến' : 'Thuê xe theo kiểu khoáng');
        });

        // Xử lý khi nhấn nút tổng kết
        $('#submitSummarize').click(function() {
            const startDate = $('#reportStartDate').val();
            const endDate = $('#reportEndDate').val();
            const carRentalId = $('#carRentalId').val();

            if (!startDate || !endDate) {
                Swal.fire({
                    title: "Lỗi",
                    text: "Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc",
                    icon: "error"
                });
                return;
            }

            // Hiển thị loading
            Swal.fire({
                title: "Đang xử lý...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Gọi API tổng kết công nợ
            $.ajax({
                url: `/admin/car-rental/${carRentalId}/summarize-report`,
                method: "POST",
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    
                    if (response.success) {
                        // Đóng modal tổng kết
                        $('#debtSummaryModal').modal('hide');
                        
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            title: "Thành công!",
                            text: "Đã tổng kết công nợ thành công",
                            icon: "success",
                            confirmButtonText: "Đóng"
                        }).then(() => {
                            // Reload trang để hiển thị trạng thái mới
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Lỗi",
                            text: response.message || "Đã xảy ra lỗi khi tổng kết công nợ",
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    
                    let errorMessage = "Đã xảy ra lỗi khi tổng kết công nợ";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: "Lỗi",
                        text: errorMessage,
                        icon: "error"
                    });
                }
            });
        });

    </script>
@endpush