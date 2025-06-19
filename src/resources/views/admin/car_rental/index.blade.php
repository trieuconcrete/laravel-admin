@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-suitcase-fill fs-1"></i> Quản lý thuê xe</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarRentalModal">
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
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.quotes.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input name="keyword" value="{{ request('keyword') }}" type="text" class="form-control" placeholder="Nhập tên Khách hàng, Công ty,...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    @foreach ($carRentalstatuses as $val => $label)
                                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100">
                                    Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Thao tác</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Tải file</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carRentals as $carRental)
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.car-rental.edit', $carRental) }}" class="btn btn-sm btn-outline-primary ">Chi tiết</a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-car-rental-btn" data-car-rental-id="{{ $carRental->id }}">
                                                Xóa
                                            </button>

                                            <form action="{{ route('admin.car-rental.destroy', $carRental) }}" method="POST" class="delete-quote-form" id="delete-form-{{ $carRental->id }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                    <td>{{ optional($carRental->customer)->full_name }}</td>
                                    <td>{{ number_format($carRental->calculateTotalMoney()) }}</td>
                                    <td><span class="badge bg-{{ $carRental->getStatusColorAttribute() }}">{{ $carRental->getStatusLabelAttribute() }}</span>
                                    </td>
                                    <td>@formatDate($carRental->created_at)</td>
                                    <td><a href="{{ $carRental->file }}" class="" target="_blank">File thuê
                                            xe excel</a></td>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm thuê xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <form id="add-quote-form" enctype="multipart/form-data" action="{{ route('admin.car-rental.store') }}" method="POST">
                @csrf
                <div class="modal-body">
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
                                <option value="">Chọn trạng thái</option>
                                @foreach ($carRentalstatuses as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="status"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả dịch vụ</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ" name="description"></textarea>
                        <div class="text-danger error" data-field="description"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập ghi chú" name="notes"></textarea>
                        <div class="text-danger error" data-field="notes"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File thuê xe</label>
                        <input type="file" class="form-control" name="file">
                        <div class="text-danger error" data-field="file"></div>
                    </div>

                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Danh sách xe thuê</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-vehicle-btn">
                                <i class="fas fa-plus me-1"></i>Thêm phương tiện
                            </button>
                        </div>
                        <div class="table-responsive table-fixed">
                            <table class="table table-sm table-fixed" id="vehiclesTable">
                                <thead>
                                    <tr>
                                        <th width="200">Phương tiện <span class="text-danger">*</span></th>
                                        <th width="250">Tên hàng</th>
                                        <th width="120">Đơn vị</th>
                                        <th width="100">Số lượng</th>
                                        <th width="100">Đơn giá</th>
                                        <th width="100">Thành tiền</th>
                                        <th width="150">Ngày bắt đầu</th>
                                        <th width="150">Ngày kết thúc</th>
                                        <th width="100">Ghi chú</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="vehicle-rows">
                                    @php
                                    $vehicles = old('vehicles', []);
                                    @endphp

                                    @if (count($vehicles))
                                        @foreach ($vehicles as $i => $vehicle)
                                        {!! view('admin.car_rental.partials.vehicle-row', [
                                        'vehicleTypes' => $vehicleTypes,
                                        'vehicle' => $vehicle,
                                        'index' => $i
                                        ])->render() !!}
                                        @endforeach
                                    @else
                                        <template id="vehicle-row-template">
                                        {!! view('admin.car_rental.partials.vehicle-row', [
                                        'vehicleTypes' => $vehicleTypes,
                                        'vehicle' => [],
                                        'index' => '__INDEX__'
                                        ])->render() !!}
                                    </template>
                                    @endif
                                </tbody>
                            </table>
                        </div>
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

@include('admin.modals.loading_modal')
@endsection

@push('scripts')
<script src="{{ asset('js/car-rental.js') }}"></script>
<script>
    $(document).ready(function() {
        const hasOldVehicles = {{ count(old('vehicles', [])) > 0 ? 'true' : 'false' }};

        if (!hasOldVehicles) {
            addVehicleRow();
        }

        $('#add-vehicle-btn').on('click', addVehicleRow);

        $('#vehicle-rows').on('click', '.remove-row', removeVehicleRow);

        $('.delete-car-rental-btn').click(function(e) {
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

        ['#add-quote-form'].forEach(function(formSelector) {
            const $form = $(formSelector);
            if ($form.length) {
                $form.on('submit', function(e) {
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
                        , }
                        , success: function(data) {
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
                        , error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                $.each(errors, function(field, messages) {
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

        $('.btn-show-quote').on('click', function() {
            let id = $(this).data('id');
            let modal = $('#detailModal');

            modal.find('.modal-title').text('Thông tin chi tiết thuê xe');

            $('#detailContentModal').html(
                '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></div>'
            );


            $('#editDetailBtn').data('id', id);

            // show modal
            modal.modal('show');

            $.ajax({
                url: `/admin/quotes/${id}`
                , type: 'GET'
                , success: function(response) {
                    $('#detailContentModal').html(response);

                    const dateFormatPlaceholder =
                        '{{ \App\Helpers\DateHelper::getDateFormatPlaceholder() }}';
                    const systemDateFormat =
                        '{{ \App\Helpers\DateHelper::getSystemDateFormat() }}';

                    $('#detailContentModal').find('input[type="date"]').each(function() {
                        this.type = 'text';
                        this.placeholder = dateFormatPlaceholder;
                        flatpickr(this, {
                            dateFormat: systemDateFormat
                            , allowInput: true
                            , defaultDate: this.value || null
                        });
                    });
                }
                , error: function(xhr) {
                    $('#detailContentModal').html(
                        '<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.</div>'
                    );
                    console.error(xhr);
                }
            });
        });

        $('#editDetailBtn').on('click', function() {
            var quoteId = $(this).data('id');
            var $form = $('#editQuoteForm');
            var formData = $form.serialize();

            $.ajax({
                url: '/admin/quotes/' + quoteId
                , method: 'PUT'
                , data: formData
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    , 'Accept': 'application/json'
                , }
                , success: function(data) {
                    // close modal
                    const modalElement = $form.closest('.modal');
                    const modal = bootstrap.Modal.getInstance(modalElement[0]);
                    if (modal) modal.hide();

                    // Reset form
                    $form[0].reset();

                    //
                    Swal.fire({
                        title: "Cập nhật thành công!"
                        , icon: "success"
                        , draggable: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reload table
                            location.reload();
                        }
                    });
                }
                , error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            $form.find(`.error[data-field="${field}"]`).text(
                                messages[0]);
                        });
                    } else {
                        console.error('Có lỗi xảy ra:', xhr);
                    }
                }
            });
        });
    });

</script>
@endpush
