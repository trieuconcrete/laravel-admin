<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-car me-2"></i>Quản lý Loại Xe
                </h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleTypeModal" onclick="openCreateModal()">
                    <i class="ri-add-line me-1"></i>Thêm mới
                </button>
            </div>
            <div class="card-body">
                <!-- Bộ lọc -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="search" class="form-control" placeholder="Tìm kiếm theo tên...">
                    </div>
                    <div class="col-md-2">
                        <select id="status-filter" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="sort-by" class="form-select">
                            <option value="vehicle_type_id">ID</option>
                            <option value="name">Tên</option>
                            <option value="status">Trạng thái</option>
                            <option value="created_at">Ngày tạo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="sort-direction" class="form-select">
                            <option value="asc">Tăng dần</option>
                            <option value="desc">Giảm dần</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-secondary" onclick="filterVehicleTypes()">
                            <i class="fas fa-filter me-1"></i>Lọc
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="fas fa-redo me-1"></i>Đặt lại
                        </button>
                    </div>
                </div>

                <!-- Bảng dữ liệu -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Tên loại xe</th>
                                <th width="20%">Số xe</th>
                                <th width="20%">Mô tả</th>
                                <th width="15%">Trạng thái</th>
                                <th width="15%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="vehicle-types-table">
                            @foreach($vehicleTypes as $vehicleType)
                            <tr>
                                <td>{{ $vehicleType->vehicle_type_id }}</td>
                                <td>
                                    <strong>{{ $vehicleType->name }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Hoạt động: {{ $vehicleType->active_vehicles_count }}
                                        </small>
                                        <small class="text-warning">
                                            <i class="fas fa-tools me-1"></i>Bảo trì: {{ $vehicleType->in_maintenance_vehicles_count }}
                                        </small>
                                        <small class="text-info">
                                            <i class="fas fa-car me-1"></i>Tổng: {{ $vehicleType->total_vehicles_count }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($vehicleType->description, 50) }}</span>
                                </td>
                                <td>
                                    @if($vehicleType->status)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Hoạt động
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause me-1"></i>Không hoạt động
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="openEditModal({{ $vehicleType->vehicle_type_id }})"
                                                data-bs-toggle="modal" data-bs-target="#vehicleTypeModal">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteVehicleType({{ $vehicleType->vehicle_type_id }})"
                                                {{ $vehicleType->hasActiveVehicles() ? 'disabled' : '' }}>
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-between align-items-center">
                    <div class="dataTables_info">
                        Hiển thị {{ $vehicleTypes->firstItem() ?? 0 }} đến {{ $vehicleTypes->lastItem() ?? 0 }} 
                        của {{ $vehicleTypes->total() }} kết quả
                    </div>
                    <div class="dataTables_paginate">
                        {{ $vehicleTypes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Loại Xe -->
<div class="modal fade" id="vehicleTypeModal" tabindex="-1" aria-labelledby="vehicleTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleTypeModalLabel">Thêm mới loại xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="vehicleTypeForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="vehicle_type_id" name="vehicle_type_id">
                    <input type="hidden" id="form_method" name="_method" value="POST">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên loại xe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback" id="description-error"></div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                            <label class="form-check-label" for="status">
                                Hoạt động
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Xử lý submit form
    $('#vehicleTypeForm').on('submit', function(e) {
        e.preventDefault();
        submitVehicleTypeForm();
    });

    // Tìm kiếm khi nhập
    $('#search').on('keyup', function() {
        if ($(this).val().length > 2 || $(this).val().length == 0) {
            filterVehicleTypes();
        }
    });
});

// Mở modal tạo mới
function openCreateModal() {
    $('#vehicleTypeModalLabel').text('Thêm mới loại xe');
    $('#vehicleTypeForm')[0].reset();
    $('#vehicle_type_id').val('');
    $('#form_method').val('POST');
    $('#submitBtn').html('<i class="fas fa-save me-1"></i>Lưu');
    $('#status').prop('checked', true);
    clearErrors();
}

// Mở modal chỉnh sửa
function openEditModal(id) {
    $('#vehicleTypeModalLabel').text('Chỉnh sửa loại xe');
    $('#form_method').val('PUT');
    $('#submitBtn').html('<i class="fas fa-save me-1"></i>Cập nhật');
    clearErrors();
    
    // Gọi API để lấy thông tin loại xe
    $.get(`/admin/vehicle-types/${id}`, function(data) {
        if (data.success) {
            const vehicleType = data.data;
            $('#vehicle_type_id').val(vehicleType.vehicle_type_id);
            $('#name').val(vehicleType.name);
            $('#description').val(vehicleType.description);
            $('#status').prop('checked', vehicleType.status == 1);
        }
    }).fail(function(xhr) {
        showError('Không thể tải thông tin loại xe');
    });
}

// Submit form
function submitVehicleTypeForm() {
    const formData = new FormData($('#vehicleTypeForm')[0]);
    const vehicleTypeId = $('#vehicle_type_id').val();
    const method = $('#form_method').val();
    
    let url = '/admin/vehicle-types';
    if (method === 'PUT' && vehicleTypeId) {
        url += `/${vehicleTypeId}`;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#vehicleTypeModal').modal('hide');
                location.reload(); // Reload trang để cập nhật dữ liệu
                showSuccess(response.message || 'Thao tác thành công');
            } else {
                showError(response.message || 'Có lỗi xảy ra');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                showValidationErrors(errors);
            } else {
                showError('Có lỗi xảy ra, vui lòng thử lại');
            }
        }
    });
}

// Xóa loại xe
function deleteVehicleType(id) {
    if (confirm('Bạn có chắc chắn muốn xóa loại xe này?')) {
        $.ajax({
            url: `/admin/vehicle-types/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                    showSuccess(response.message || 'Xóa thành công');
                } else {
                    showError(response.message || 'Có lỗi xảy ra');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    showError(xhr.responseJSON.message);
                } else {
                    showError('Có lỗi xảy ra, vui lòng thử lại');
                }
            }
        });
    }
}

// Lọc dữ liệu
function filterVehicleTypes() {
    const params = {
        search: $('#search').val(),
        status: $('#status-filter').val(),
        sort_by: $('#sort-by').val(),
        sort_direction: $('#sort-direction').val()
    };

    const queryString = $.param(params);
    window.location.href = `{{ route('admin.settings.index') }}?tab=vehicle-types&${queryString}`;
}

// Đặt lại bộ lọc
function resetFilters() {
    $('#search').val('');
    $('#status-filter').val('');
    $('#sort-by').val('vehicle_type_id');
    $('#sort-direction').val('asc');
    window.location.href = `{{ route('admin.settings.index') }}?tab=vehicle-types`;
}

// Xóa lỗi validation
function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

// Hiển thị lỗi validation
function showValidationErrors(errors) {
    clearErrors();
    for (const field in errors) {
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(errors[field][0]);
    }
}

// Hiển thị thông báo thành công
function showSuccess(message) {
    // Implement toast notification hoặc alert
    alert(message);
}

// Hiển thị thông báo lỗi
function showError(message) {
    // Implement toast notification hoặc alert
    alert(message);
}
</script>