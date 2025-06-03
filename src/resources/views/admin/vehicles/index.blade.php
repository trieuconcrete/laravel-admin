@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-truck-fill fs-1"></i> Quản lý phương tiện vận tải</h4>
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
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.vehicles.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select" name="vehicle_type_id" id="vehicle_type_id">
                                    <option value="">Tất cả loại xe</option>
                                    @foreach ($vehicleTypes as $key => $val)
                                        <option value="{{ $key }}" {{ request('vehicle_type_id') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    @foreach ($vehicleStatuses as $val => $label)
                                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input name="keyword" type="text" class="form-control" placeholder="Tìm kiếm..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vehicles Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Thao tác</th>
                                    <th>Biển số</th>
                                    <th>Loại xe</th>
                                    <th>Tài xế</th>
                                    <th>Tải trọng</th>
                                    <th>Trạng thái</th>
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
                        <div class="col-md-6">
                            <label class="form-label">Tài xế </label>
                            <select class="form-select" name="driver_id">
                                @foreach ($drivers as $key => $driver)
                                    <option value="{{ $key }}">{{ $driver }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="driver_id"></div>
                        </div>
                    </div>
                    <hr>
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