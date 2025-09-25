<style>
    #status {
        width: 40px;
        height: 20px;
        position: static;
        left: 0;
        top: 50%;
        margin: 0;
    }

    .form-switch {
        padding-left: 0;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .form-check .form-check-input {
        float: unset;
    }

    .form-check-label {
        display: inline;

    }

    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .invalid-feedback {
        display: block;
    }

    .btn:disabled {
        cursor: not-allowed !important;
        pointer-events: auto !important;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-car me-2"></i>Quản lý Loại Xe
                </h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleTypeModal"
                    onclick="openCreateModal()">
                    <i class="ri-add-line me-1"></i>Thêm mới
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="search" class="form-control" placeholder="Tìm kiếm theo tên...">
                    </div>
                    <div class="col-md-2">
                        <select id="status-filter" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="sort-by" class="form-select">
                            <option value="vehicle_type_id" {{ request('sort_by') == 'vehicle_type_id' ? 'selected' : '' }}>ID</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Tên</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Trạng thái</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="sort-direction" class="form-select">
                            <option value="asc" {{ request('sort_by') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                            <option value="desc" {{ request('sort_by') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
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
                            @foreach ($vehicleTypes as $vehicleType)
                                <tr id="vehicle-type-{{ $vehicleType->vehicle_type_id }}">
                                    <td>{{ $vehicleType->vehicle_type_id }}</td>
                                    <td><strong>{{ $vehicleType->name }}</strong></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Hoạt động:
                                                {{ $vehicleType->active_vehicles_count }}
                                            </small>
                                            <small class="text-warning">
                                                <i class="fas fa-tools me-1"></i>Bảo trì:
                                                {{ $vehicleType->in_maintenance_vehicles_count }}
                                            </small>
                                            <small class="text-info">
                                                <i class="fas fa-car me-1"></i>Tổng:
                                                {{ $vehicleType->total_vehicles_count }}
                                            </small>
                                        </div>
                                    </td>
                                    <td><span class="text-muted">{{ Str::limit($vehicleType->description, 50) }}</span>
                                    </td>
                                    <td>
                                        @if ($vehicleType->status)
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Hoạt
                                                động</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-pause me-1"></i>Không hoạt
                                                động</span>
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

                <div class="d-flex justify-content-between align-items-center">
                    <div class="dataTables_info">
                        Hiển thị {{ $vehicleTypes->firstItem() ?? 0 }} đến {{ $vehicleTypes->lastItem() ?? 0 }}
                        của {{ $vehicleTypes->total() }} kết quả
                    </div>
                    <div class="dataTables_paginate">
                        {{ $vehicleTypes->appends(array_merge(request()->except('page'), ['tab' => 'vehicle-types']))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vehicleTypeModal" tabindex="-1" aria-labelledby="vehicleTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleTypeModalLabel">Thêm mới loại xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="vehicleTypeForm">
                <div class="modal-body">
                    <input type="hidden" id="vehicle_type_id" name="vehicle_type_id">
                    <input type="hidden" id="form_method" name="_method" value="POST">

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên loại xe <span
                                class="text-danger">*</span></label>
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
                            <input class="form-check-input" type="checkbox" id="status" name="status"
                                value="1">
                            <label class="form-check-label" for="status">Hoạt động</label>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#search').on('keyup', function() {
            if ($(this).val().length > 2 || $(this).val().length == 0) {
                filterVehicleTypes();
            }
        });

        $('#vehicleTypeForm').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            const form = $(this);
            const submitBtn = $('#submitBtn');
            const originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin me-1"></i>Đang lưu...');
            clearErrors();
            const actionUrl = getActionUrl();
            const method = $('#form_method').val();
            const formData = getFormData();

            $.ajax({
                url: actionUrl,
                type: method === 'POST' ? 'POST' : 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') ||
                        'dummy-token'
                },
                success: function(response) {
                    if (response.success) {
                        $('#vehicleTypeModal').modal('hide');
                        setTimeout(function() {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');
                        }, 500);
                        showToast("success", response.message || 'Lưu thành công!');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                        const vehicleType = response.data;
                        updateTableRow(vehicleType);
                        form[0].reset();
                        clearErrors();
                    } else {
                        showToast("error", response.message || 'Có lỗi xảy ra!');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            showToast("error", "Dữ liệu không hợp lệ!");
                        }
                    } else {
                        const message = (xhr.responseJSON && xhr.responseJSON.message) ?
                            xhr.responseJSON.message :
                            'Có lỗi xảy ra khi lưu dữ liệu!';
                        showToast("error", message);
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });

            return false;
        });

        $('#vehicleTypeForm input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#submitBtn').click();
                return false;
            }
        });
    });

    function getActionUrl() {
        const vehicleTypeId = $('#vehicle_type_id').val();
        const baseUrl = '/admin/vehicle-types';
        return vehicleTypeId ? `${baseUrl}/${vehicleTypeId}` : baseUrl;
    }

    function getFormData() {
        return {
            name: $('#name').val().trim(),
            description: $('#description').val().trim(),
            status: $('#status').is(':checked') ? 1 : 0,
            _method: $('#form_method').val()
        };
    }

    function updateTableRow(vehicleType) {
        const rowHtml = `
            <tr id="vehicle-type-${vehicleType.vehicle_type_id}">
                <td>${vehicleType.vehicle_type_id}</td>
                <td><strong>${vehicleType.name}</strong></td>
                <td>
                    <div class="d-flex flex-column">
                        <small class="text-success"><i class="fas fa-check-circle me-1"></i>Hoạt động: ${vehicleType.active_vehicles_count || 0}</small>
                        <small class="text-warning"><i class="fas fa-tools me-1"></i>Bảo trì: ${vehicleType.in_maintenance_vehicles_count || 0}</small>
                        <small class="text-info"><i class="fas fa-car me-1"></i>Tổng: ${vehicleType.total_vehicles_count || 0}</small>
                    </div>
                </td>
                <td><span class="text-muted">${vehicleType.description ? vehicleType.description.substring(0,50) : ''}</span></td>
                <td>
                    ${vehicleType.status ?
                        '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Hoạt động</span>' :
                        '<span class="badge bg-secondary"><i class="fas fa-pause me-1"></i>Không hoạt động</span>'
                    }
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditModal(${vehicleType.vehicle_type_id})" data-bs-toggle="modal" data-bs-target="#vehicleTypeModal">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteVehicleType(${vehicleType.vehicle_type_id})" ${vehicleType.hasActiveVehicles ? 'disabled' : ''}>
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        const existingRow = $(`#vehicle-type-${vehicleType.vehicle_type_id}`);
        if (existingRow.length) {
            existingRow.replaceWith(rowHtml);
        } else {
            $('#vehicle-types-table').append(rowHtml);
        }
    }

    function openCreateModal() {
        $('#vehicleTypeModalLabel').text('Thêm mới loại xe');
        $('#vehicleTypeForm')[0].reset();
        $('#form_method').val('POST');
        $('#vehicle_type_id').val('');
        $('#status').prop('checked', true);
        $('#submitBtn').html('<i class="fas fa-save me-1"></i>Lưu');
        clearErrors();
    }

    function openEditModal(id) {
        $('#vehicleTypeModalLabel').text('Đang tải...');
        $.ajax({
            url: `/admin/vehicle-types/${id}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || 'dummy-token'
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#vehicleTypeModalLabel').text('Chỉnh sửa loại xe');
                    $('#form_method').val('PUT');
                    $('#vehicle_type_id').val(data.vehicle_type_id);
                    $('#name').val(data.name);
                    $('#description').val(data.description || '');
                    $('#status').prop('checked', data.status == 1);
                    $('#submitBtn').html('<i class="fas fa-save me-1"></i>Cập nhật');
                    clearErrors();
                } else {
                    showToast("error", response.message || "Không thể tải dữ liệu");
                    $('#vehicleTypeModal').modal('hide');
                }
            },
            error: function(xhr) {
                const message = (xhr.responseJSON && xhr.responseJSON.message) ?
                    xhr.responseJSON.message :
                    'Lỗi khi tải dữ liệu loại xe!';
                showToast("error", message);
                $('#vehicleTypeModal').modal('hide');
            }
        });
    }

    function deleteVehicleType(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Loại xe này sẽ bị xóa vĩnh viễn và không thể khôi phục!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/vehicle-types/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || 'dummy-token'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast("success", response.message || 'Xóa thành công!');
                            $(`#vehicle-type-${id}`).fadeOut(300, function() {
                                $(this).remove();
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            showToast("error", response.message || 'Có lỗi xảy ra khi xóa!');
                        }
                    },
                    error: function(xhr) {
                        const message = (xhr.responseJSON && xhr.responseJSON.message) ?
                            xhr.responseJSON.message :
                            'Có lỗi xảy ra khi xóa!';
                        showToast("error", message);
                    }
                });
            }
        });
    }

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function showValidationErrors(errors) {
        clearErrors();
        for (const field in errors) {
            $(`#${field}`).addClass('is-invalid');
            $(`#${field}-error`).text(errors[field][0]);
        }
    }

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

    function resetFilters() {
        $('#search').val('');
        $('#status-filter').val('');
        $('#sort-by').val('vehicle_type_id');
        $('#sort-direction').val('asc');
        window.location.href = `{{ route('admin.settings.index') }}?tab=vehicle-types`;
    }
</script>
