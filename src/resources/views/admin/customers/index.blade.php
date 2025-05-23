@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-group-fill fs-1"></i> Quản lý khách hàng</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                        <i class="ri-add-circle-line align-middle me-1"></i>Thêm khách hàng mới
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
                    <form method="GET" action="{{ route('admin.customers.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select" id="customerTypeFilter" name="type">
                                    <option value="">Tất cả loại khách hàng</option>
                                    @foreach ($customerTypes as $key => $val)
                                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter" name="is_active">
                                    <option value="">Tất cả trạng thái</option>
                                    @foreach ($customerStatusActives as $key => $val)
                                        <option value="{{ $key }}" {{ request('is_active') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input name="keyword" type="text" class="form-control" placeholder="Tìm kiếm...">
                                </div>
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
                                    <th>Mã KH</th>
                                    <th>Tên khách hàng</th>
                                    <th>Loại</th>
                                    <th>Điện thoại</th>
                                    <th>Email</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                                <button class="btn btn-sm btn-outline-danger delete-customer-btn" data-customer-id="{{ $customer->id }}">
                                                    Xóa
                                                </button>

                                                <form action="{{ route('admin.customers.destroy', $customer->id) }}"
                                                    method="POST"
                                                    class="delete-customer-form"
                                                    id="delete-form-{{ $customer->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                        <td>{{ $customer->customer_code }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td><span class="badge bg-{{ $customer->type_badge_class }}">{{ $customer->type_label }}</span></td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td><span class="badge bg-{{ $customer->status_badge_class }}">{{ $customer->status_label }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $customers->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div> <!-- end col -->
    </div>

</div>
<!-- container-fluid -->

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm khách hàng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <form id="add-customer-form" action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Loại khách hàng <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="individualType" value="individual">
                                    <label class="form-check-label" for="individualType">Cá nhân</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="businessType" value="business" checked>
                                    <label class="form-check-label" for="businessType">Doanh nghiệp</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nhập tên khách hàng">
                                <div class="text-danger error" data-field="name"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mã số thuế  <span class="text-danger">*</span></label>
                                <input name="tax_code" type="text" class="form-control" placeholder="Nhập mã số thuế">
                                <div class="text-danger error" data-field="tax_code"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="text" name="website" class="form-control" placeholder="Nhập website">
                                <div class="text-danger error" data-field="website"></div>
                            </div>
                            <div class="col-md-6" id="businessDateField">
                                <label class="form-label">Ngày thành lập</label>
                                <input name="establishment_date" type="date" class="form-control">
                                <div class="text-danger error" data-field="establishment_date"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Trạng thái</label>
                                <select name="is_active" class="form-select">
                                    @foreach ($customerStatusActives as $key => $val)
                                        <option value="{{ $key }}" {{ request('is_active') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger error" data-field="is_active"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Nhập địa chỉ"></textarea>
                            <div class="text-danger error" data-field="address"></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Điện thoại <span class="text-danger">*</span></label>
                                <input name="phone" type="tel" class="form-control" placeholder="Nhập số điện thoại">
                                <div class="text-danger error" data-field="phone"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Nhập email">
                                <div class="text-danger error" data-field="email"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Nhập ghi chú"></textarea>
                            <div class="text-danger error" data-field="notes"></div>
                        </div>

                        <div id="businessContactSection">
                            <hr>
                            <h6 class="mb-3">Thông tin người liên hệ chính</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ tên</label>
                                    <input name="primary_contact_name" type="text" class="form-control" placeholder="Nhập họ tên">
                                    <div class="text-danger error" data-field="primary_contact_name"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Chức vụ</label>
                                    <input name="primary_contact_position" type="text" class="form-control" placeholder="Nhập chức vụ">
                                    <div class="text-danger error" data-field="primary_contact_position"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Điện thoại</label>
                                    <input name="primary_contact_phone" type="tel" class="form-control" placeholder="Nhập số điện thoại">
                                    <div class="text-danger error" data-field="primary_contact_phone"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input name="primary_contact_email" type="email" class="form-control" placeholder="Nhập email">
                                    <div class="text-danger error" data-field="primary_contact_email"></div>
                                </div>
                            </div>
                        </div>
                </div>
                <hr>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu khách hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.delete-customer-btn').click(function (e) {
            e.preventDefault();

            const customerId = $(this).data('customer-id');
            const form = $('#delete-form-' + customerId);

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

        ['#add-customer-form'].forEach(function (formSelector) {
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

        $('.delete-user-btn').click(function (e) {
            e.preventDefault();
    
            var form = $(this).closest('.delete-user-form');
    
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
    });
    </script>
@endpush