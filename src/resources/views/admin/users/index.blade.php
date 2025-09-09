@extends('admin.layout')
@section('title', 'Quản lý nhân sự')
@section('content')

<div class="container-fluid">
    <div class="row mb-3 pb-1">
        <div class="col-12">
            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                <div class="flex-grow-1">
                    {{-- <h4><i class="ri-group-fill fs-1"></i>Quản lý nhân sự</h4> --}}
                </div>
                <div class="mt-3 mt-lg-0">
                    <div class="row g-3 mb-0 align-items-center flex">
                        <div class="col-auto">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target=".add-driver-model"><i class="ri-add-circle-line align-middle me-1"></i> Thêm tài xế</button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".add-user-model"><i class="ri-add-circle-line align-middle me-1"></i> Thêm nhân viên</button>
                            <button type="button" class="btn btn-outline-secondary" id="export-user-btn"><i class="las la-file-export align-middle me-1"></i> Xuất dữ liệu</button>
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
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4 mb-4">
                <div class="row gy-4">
                    <div class="col-xxl-3 col-md-6">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm với Họ tên, SDT, Email..." class="form-control border p-2 rounded">
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <select name="position_id" class="form-control border p-2 rounded">
                            <option value="">Tất cả vị trí</option>
                            @foreach ($positions as $key => $val )
                            <option value="{{ $key }}" {{ request('position_id') == $key ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <select name="status" class="form-control border p-2 rounded">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang làm việc</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đã nghỉ việc</option>
                        </select>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <button type="submit" class="btn btn-outline-primary w-100">Tìm kiếm</button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div class="live-preview">
                        <div class="table-responsive table-card">
                            <table class="table table-hover align-middle table-nowrap mb-0">
                                <thead class="table-light text-uppercase">
                                    <tr class="bg-gray-100">
                                        <th class="">Thao tác</th>
                                        <th class="">HỌ TÊN</th>
                                        <th class="">NĂM SINH</th>
                                        <th class="">GIỚI TÍNH</th>
                                        <th class="">CHỨC VỤ</th>
                                        <th class="">Số CCCD</th>
                                        <th class="">Ngày cấp</th>
                                        <th class="">Loại BL</th>
                                        <th class="">Hạn BL</th>
                                        <th class="">SĐT</th>
                                        <th class="">EMAIL</th>
                                        <th class="">Lương cơ bản</th>
                                        <th class="">Bảo hiểm</th>
                                        <th class="">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class=" flex gap-2">
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                                    
                                                    @if ($user->role !== \App\Models\User::ROLE_ADMIN)
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger delete-user-btn"
                                                                data-user-id="{{ $user->id }}">
                                                            Xóa
                                                        </button>
                                                        
                                                        <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                            method="POST"
                                                            class="delete-user-form"
                                                            id="delete-form-{{ $user->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="">{{ $user->full_name }}</td>
                                            <td class="">{{ $user->birthday }}</td>
                                            <td class="">{{ $user->getGenderLabel() }}</td>
                                             <td class="">{{ ucfirst(optional($user->position)->name) }}</td>
                                             <td class="">{{ $user->id_number }}</td>
                                             <td class="">{{ $user->id_number_issuance_date }}</td>
                                             <td class="">{{  optional($user->license)->license_type }}</td>
                                            <td class="">
                                                @if (optional($user->license)->status == 'expired')
                                                    <span class="text-danger">{{ optional($user->license)->getStatusLabelAttribute() }}</span>
                                                @else
                                                    <span class="text-success">{{ optional($user->license)->getStatusLabelAttribute() }}</span>
                                                @endif
                                            </td>
                                            <td class="">{{ $user->phone }}</td>
                                            <td class="">{{ $user->email }}</td>
                                            <td class="">{{ number_format($user->salary_base) }}</td>
                                            <td class="">
                                                @if ($user->hasInsurance())
                                                    <span class="badge bg-success-subtle text-success">
                                                        {{ $user->getInsuranceStatusLabel() }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        {{ $user->getInsuranceStatusLabel() }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="">
                                                @if ($user->status) <span class="badge bg-success-subtle text-success">Đang làm việc</span>
                                                @else
                                                <span class="badge bg-danger-subtle text-danger">Đã nghỉ việc</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{ $users->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
<!-- container-fluid -->

<!-- Add Driver Model -->
<div class="modal fade add-driver-model" tabindex="-1" role="dialog" aria-labelledby="addDriverModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="add-driver-form" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addDriverModel">Thêm tài xế</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr>
                <div class="modal-body">
                    <input hidden type="text" name="add_driver" value="1" class="form-control">
                    <div class="row g-3">
                        <div class="col-xxl-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" placeholder="Nhập họ và tên">
                            <div class="text-danger error" data-field="full_name"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại">
                            <div class="text-danger error" data-field="phone"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Số CCCD <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" class="form-control" placeholder="Nhập số CCCD">
                            <div class="text-danger error" data-field="id_number"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Ngày cấp </label>
                            <input type="date" name="id_number_issuance_date" class="form-control" value="@formatDateForInput(request('id_number_issuance_date'))">
                            <div class="text-danger error" data-field="id_number_issuance_date"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email">
                            <div class="text-danger error" data-field="email"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Loại bằng lái <span class="text-danger">*</span></label>
                            <select name="license_type" class="form-control">
                                <option value="">Chọn bằng lái</option>
                                @foreach ($licenses as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="license_type"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Hạn bằng lái </label>
                            <input type="date" name="license_expire_date" class="form-control">
                            <div class="text-danger error" data-field="license_expire_date"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Ngày sinh </label>
                            <input type="date" name="birthday" class="form-control" value="@formatDateForInput(request('birthday'))">
                            <div class="text-danger error" data-field="birthday"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Ngày vào làm <span class="text-danger">*</span></label>
                            <input type="date" name="join_date" class="form-control">
                            <div class="text-danger error" data-field="join_date"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Lương cơ bản </label>
                            <input type="text" name="salary_base" class="form-control" placeholder="Nhập lương cơ bản">
                            <div class="text-danger error" data-field="salary_base"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Trạng thái làm việc</label>
                            <select name="status" class="form-select">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="status"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select">
                                    <option value="1">Nam</option>
                                    <option value="0">Nữ</option>
                            </select>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Loại lương tài xế</label>
                            <select name="salary_type" id="salaryTypeCreate" class="form-select">
                                <option value="1">Tài xế ăn lương cơ bản</option>
                                <option value="2">Tài xế ăn lương doanh số</option>
                            </select>
                            <div class="text-danger error" data-field="salary_type"></div>
                        </div>
                        <div class="col-xxl-6" id="salaryByPercentContainerCreate" style="display: none;">
                            <label class="form-label">Lương phần trăm doanh số (%) <span class="text-muted">*Mặc định: 12%</span></label>
                            <input type="number" name="salary_by_percent" class="form-control" placeholder="Nhập phần trăm (VD: 12)" value="{{ old('salary_by_percent', 12) }}" min="1" max="100" step="0.01">
                            <div class="text-danger error" data-field="salary_by_percent"></div>
                            <small class="text-muted">Phần trăm lương theo doanh số (áp dụng cho tài xế ăn lương doanh số)</small>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Địa chỉ </label>
                            <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ">
                            <div class="text-danger error" data-field="address"></div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Nhập ghi chú"></textarea>
                            <div class="text-danger error" data-field="notes"></div>
                        </div>
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end mt-3">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-secondary">Lưu tài xế</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.modal -->

<!-- Add User Model -->
<div class="modal fade add-user-model" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="add-user-form" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <hr>
                <div class="modal-body">
                    <input hidden type="text" name="add_driver" value="0" class="form-control">
                    <div class="row g-3">
                        <div class="col-xxl-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" placeholder="Nhập họ và tên">
                            <div class="text-danger error" data-field="full_name"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại">
                            <div class="text-danger error" data-field="phone"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Số CCCD <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" class="form-control" placeholder="Nhập Số CCCD">
                            <div class="text-danger error" data-field="id_number"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Ngày cấp </label>
                            <input type="date" name="id_number_issuance_date" class="form-control" value="@formatDateForInput(request('id_number_issuance_date'))">
                            <div class="text-danger error" data-field="id_number_issuance_date"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Vị trí <span class="text-danger">*</span></label>
                            <select name="position_id" class="form-control">
                                <option value="">Chọn vị trí</option>
                                @foreach ($positions as $key => $val)
                                    @if ($val !== 'Tài xế')
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="position_id"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email">
                            <div class="text-danger error" data-field="email"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="birthday" class="form-control">
                            <div class="text-danger error" data-field="birthday"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Ngày vào làm</label>
                            <input type="date" name="join_date" class="form-control">
                            <div class="text-danger error" data-field="join_date"></div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Lương cơ bản</label>
                            <input type="text" name="salary_base" class="form-control" placeholder="Nhập lương cơ bản">
                            <div class="text-danger error" data-field="salary_base"></div>
                        </div>
                        <div class="col-xxl-6 col-md-6">
                            <label class="form-label">Vai trò</label>
                            <div class="form-check form-check-secondary mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="isAdmin" name="is_admin" {{ request('is_admin') ? 'checked' : '' }}>
                                <label class="form-check-label" for="isAdmin">
                                    Admin
                                </label>
                            </div>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Trạng thái làm việc</label>
                            <select name="status" class="form-select">
                                @foreach ($statuses as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error" data-field="status"></div>
                        </div>
                         <div class="col-xxl-6">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select">
                                    <option value="1">Nam</option>
                                    <option value="0">Nữ</option>
                            </select>
                        </div>
                        <div class="col-xxl-6">
                            <label class="form-label">Bảo hiểm xã hội</label>
                            <div class="form-check form-check-secondary mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="hasInsurance" name="has_insurance" checked>
                                <label class="form-check-label" for="hasInsurance">
                                    Có đóng bảo hiểm
                                </label>
                            </div>
                        </div>
                        <div class="col-xxl-6" id="insuranceStartDateContainer">
                            <label class="form-label">Ngày bắt đầu đóng bảo hiểm</label>
                            <input type="date" name="insurance_start_date" class="form-control">
                            <div class="text-danger error" data-field="insurance_start_date"></div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Địa chỉ </label>
                            <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ">
                            <div class="text-danger error" data-field="address"></div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Nhập ghi chú"></textarea>
                            <div class="text-danger error" data-field="notes"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-secondary">Lưu nhân viên</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.modal -->
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.delete-user-btn').click(function (e) {
            e.preventDefault();

            const userId = $(this).data('user-id');
            const form = $('#delete-form-' + userId);

            Swal.fire({
                title: 'Bạn chắc chắn muốn xóa?',
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

        ['#add-driver-form', '#add-user-form'].forEach(function (formSelector) {
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
                                    if (typeof driverTable !== 'undefined') {
                                        driverTable.ajax.reload();
                                    } else {
                                        location.reload();
                                    }
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
    });

    $('input[name="salary_base"]').on('input', function () {
        let value = $(this).val();

        value = value.replace(/[^0-9.]/g, '');

        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';

        $(this).val(integerPart + decimalPart);
    });

    let salaryInput = $('input[name="salary_base"]');
    let initial = salaryInput.val().replace(/[^0-9.]/g, '');
    if (initial) {
        let parts = initial.split('.');
        let formatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        if (parts[1]) {
            formatted += '.' + parts[1].slice(0, 2);
        }
        salaryInput.val(formatted);
    }

    $('#export-user-btn').click(function () {
        Swal.fire({
            title: 'Xác nhận xuất dữ liệu?',
            text: 'Bạn có chắc chắn muốn xuất dữ liệu người dùng không?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Có, xuất ngay',
            cancelButtonText: 'Hủy bỏ',
            customClass: {
                confirmButton: 'btn btn-secondary',
                cancelButton: 'btn btn-light'
            },
            {{--  buttonsStyling: false  --}}
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        const link = document.createElement('a');
                        link.href = "{{ route('admin.users.export') }}";
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        setTimeout(() => {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Xuất dữ liệu thành công',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }, 2000);
                    }
                });
            }
        });
    });

    // Handle salary type change to show/hide salary_by_percent input for create form
    $(document).ready(function() {
        $('#salaryTypeCreate').on('change', function() {
            const salaryType = $(this).val();
            const salaryByPercentContainer = $('#salaryByPercentContainerCreate');
            const salaryByPercentInput = salaryByPercentContainer.find('input[name="salary_by_percent"]');
            
            if (salaryType === '2') { // Tài xế ăn lương doanh số
                salaryByPercentContainer.show();
                // Set default value nếu input rỗng
                if (!salaryByPercentInput.val()) {
                    salaryByPercentInput.val(12);
                }
            } else { // Tài xế ăn lương cơ bản
                salaryByPercentContainer.hide();
                salaryByPercentInput.val(''); // Clear value khi hide
            }
        });
        
        // Trigger change event on page load để set đúng trạng thái
        $('#salaryTypeCreate').trigger('change');
        
        // Handle insurance checkbox for create form
        $('#hasInsurance').on('change', function() {
            const insuranceStartDateContainer = $('#insuranceStartDateContainer');
            const insuranceStartDateInput = insuranceStartDateContainer.find('input[name="insurance_start_date"]');
            
            if ($(this).is(':checked')) {
                insuranceStartDateContainer.show();
            } else {
                insuranceStartDateContainer.hide();
                insuranceStartDateInput.val(''); // Clear the date when unchecked
            }
        });
        
        // Trigger change event on page load để set đúng trạng thái cho insurance
        $('#hasInsurance').trigger('change');
    });
</script>
@endpush