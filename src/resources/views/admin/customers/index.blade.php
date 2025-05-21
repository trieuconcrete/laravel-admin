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
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-select" id="customerTypeFilter">
                                <option value="">Tất cả loại khách hàng</option>
                                <option value="individual">Cá nhân</option>
                                <option value="business">Doanh nghiệp</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Không hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100">
                                Tìm kiếm
                            </button>
                        </div>
                    </div>
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
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>KH001</td>
                                    <td>Công ty TNHH ABC</td>
                                    <td><span class="badge bg-primary">Doanh nghiệp</span></td>
                                    <td>0901234567</td>
                                    <td>contact@abccompany.com</td>
                                    <td><span class="badge bg-success">Đang hoạt động</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>KH002</td>
                                    <td>Nguyễn Văn A</td>
                                    <td><span class="badge bg-info">Cá nhân</span></td>
                                    <td>0912345678</td>
                                    <td>nguyenvana@gmail.com</td>
                                    <td><span class="badge bg-success">Đang hoạt động</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>KH003</td>
                                    <td>Cơ sở sản xuất XYZ</td>
                                    <td><span class="badge bg-primary">Doanh nghiệp</span></td>
                                    <td>0978123456</td>
                                    <td>xyz@example.com</td>
                                    <td><span class="badge bg-success">Đang hoạt động</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>KH004</td>
                                    <td>Trần Thị B</td>
                                    <td><span class="badge bg-info">Cá nhân</span></td>
                                    <td>0987654321</td>
                                    <td>tranthib@gmail.com</td>
                                    <td><span class="badge bg-danger">Không hoạt động</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <nav>
                        <ul class="pagination justify-content-end">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Trước</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Sau</a>
                            </li>
                        </ul>
                    </nav>
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
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại khách hàng <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customerType" id="individualType" value="individual">
                                <label class="form-check-label" for="individualType">Cá nhân</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customerType" id="businessType" value="business" checked>
                                <label class="form-check-label" for="businessType">Doanh nghiệp</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Nhập tên khách hàng">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mã số thuế</label>
                            <input type="text" class="form-control" placeholder="Nhập mã số thuế">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" placeholder="Nhập website">
                        </div>
                        <div class="col-md-6" id="businessDateField">
                            <label class="form-label">Ngày thành lập</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                            <option value="1" selected>Đang hoạt động</option>
                            <option value="0">Không hoạt động</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" rows="2" placeholder="Nhập địa chỉ"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="Nhập email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập ghi chú"></textarea>
                    </div>

                    <div id="businessContactSection">
                        <hr>
                        <h6 class="mb-3">Thông tin người liên hệ chính</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ tên</label>
                                <input type="text" class="form-control" placeholder="Nhập họ tên">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chức vụ</label>
                                <input type="text" class="form-control" placeholder="Nhập chức vụ">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Điện thoại</label>
                                <input type="tel" class="form-control" placeholder="Nhập số điện thoại">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="Nhập email">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary">Lưu khách hàng</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
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