@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-suitcase-fill fs-1"></i> Quản lý hợp đồng</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                        <i class="ri-add-circle-line align-middle me-1"></i>Thêm hợp đồng mới
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
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="pending">Chờ phê duyệt</option>
                                <option value="cancelled">Hủy</option>
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
                                    <th>Mã HD</th>
                                    <th>Tên khách hàng</th>
                                    <th>Ngày ký</th>
                                    <th>Điện thoại</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng giá trị</th>
                                    <th>Đã thanh toán</th>
                                    <th>Còn lại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customerDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>HD001</td>
                                    <td>Công ty TNHH ABC</td>
                                    <td>10/10/2025</td>
                                    <td>0901234567</td>
                                    <td><span class="badge bg-primary">Đang hoạt động</span></td>
                                    <td>{{ number_format(123456789) }}</td>
                                    <td>{{ number_format(12345678) }}</td>
                                    <td>{{ number_format(123456789-12345678) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customerDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>HD002</td>
                                    <td>Công ty TNHH XYZ</td>
                                    <td>10/11/2025</td>
                                    <td>0901234567</td>
                                    <td><span class="badge bg-warning">Chờ phê duyệt</span></td>
                                    <td>{{ number_format(3456789324) }}</td>
                                    <td>{{ number_format(675363653) }}</td>
                                    <td>{{ number_format(3456789324-675363653) }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#customerDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>HD003</td>
                                    <td>Công ty TNHH 123</td>
                                    <td>10/06/2025</td>
                                    <td>0901234567</td>
                                    <td><span class="badge bg-success">Hoàn thành</span></td>
                                    <td>{{ number_format(345678543) }}</td>
                                    <td>{{ number_format(345678543) }}</td>
                                    <td></td>
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

<!-- Add Contract Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm hợp đồng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <select class="form-select">
                                <option value="">Chọn khách hàng</option>
                                <option value="1">Công ty TNHH ABC</option>
                                <option value="2">Công ty TNHH XYZ	</option>
                                <option value="3">Công ty TNHH 123</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mã hợp đồng</label>
                            <input type="text" class="form-control" placeholder="Để trống để tạo tự động" disabled>
                            <small class="text-muted">Hệ thống sẽ tự động tạo mã</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Giá trị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Nhập giá trị">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại hợp đồng</label>
                            <select class="form-select">
                                <option value="">Chọn loại hợp đồng</option>
                                <option value="1">Dài hạn</option>
                                <option value="2">Chuyến đơn</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Nhập ngày bắt đầu">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="text" class="form-control" placeholder="Nhập kết thúc">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả dịch vụ</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ"></textarea>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary">Lưu hợp đồng</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Detail Modal -->
<div class="modal fade" id="customerDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông tin chi tiết khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Customer Info Header -->
                <div class="customer-info-header p-3 mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Công ty TNHH ABC</h4>
                            <p class="text-muted">Mã khách hàng: KH001</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><i class="fas fa-building me-2 text-primary"></i> Doanh nghiệp</p>
                                    <p><i class="fas fa-map-marker-alt me-2 text-primary"></i> TP. Hồ Chí Minh</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-success mb-2">Đang hoạt động</span>
                            <p><i class="fas fa-calendar-alt me-2 text-primary"></i> Ngày đăng ký: 01/01/2020</p>
                        </div>
                    </div>
                </div>

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="customerDetailTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#generalInfo">Thông tin chung</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#contacts">Hợp đồng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#transactions">Lịch sử giao dịch</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-3 border border-top-0 rounded-bottom">
                    <!-- General Info Tab -->
                    <div class="tab-pane fade show active" id="generalInfo">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tên khách hàng:</strong> Công ty TNHH ABC</p>
                                <p><strong>Mã khách hàng:</strong> KH001</p>
                                <p><strong>Loại khách hàng:</strong> Doanh nghiệp</p>
                                <p><strong>Mã số thuế:</strong> 0123456789</p>
                                <p><strong>Ngày thành lập:</strong> 15/06/2010</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Địa chỉ:</strong> 123 Đường Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh</p>
                                <p><strong>Điện thoại:</strong> 0901234567</p>
                                <p><strong>Email:</strong> contact@abccompany.com</p>
                                <p><strong>Website:</strong> www.abccompany.com</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contacts Tab -->
                    <div class="tab-pane fade" id="contacts">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Danh sách hợp đồng</h6>
                            <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Thêm hợp đồng</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Họ tên</th>
                                        <th>Chức vụ</th>
                                        <th>Điện thoại</th>
                                        <th>Email</th>
                                        <th>Chính</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Nguyễn Văn X</td>
                                        <td>Giám đốc</td>
                                        <td>0912345678</td>
                                        <td>nguyenvanx@abccompany.com</td>
                                        <td><i class="fas fa-check text-success"></i></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Trần Thị Y</td>
                                        <td>Kế toán trưởng</td>
                                        <td>0987654321</td>
                                        <td>tranthiy@abccompany.com</td>
                                        <td></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Transactions Tab -->
                    <div class="tab-pane fade" id="transactions">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Lịch sử giao dịch</h6>
                            <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Thêm giao dịch</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Loại giao dịch</th>
                                        <th>Số tiền</th>
                                        <th>Số tham chiếu</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>15/04/2025</td>
                                        <td>Hóa đơn</td>
                                        <td class="text-danger">-25,000,000 VNĐ</td>
                                        <td>INV-2025-0123</td>
                                        <td><span class="badge bg-success">Đã thanh toán</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>20/03/2025</td>
                                        <td>Thanh toán</td>
                                        <td class="text-success">+30,000,000 VNĐ</td>
                                        <td>PAY-2025-0098</td>
                                        <td><span class="badge bg-success">Hoàn thành</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>05/03/2025</td>
                                        <td>Hóa đơn</td>
                                        <td class="text-danger">-30,000,000 VNĐ</td>
                                        <td>INV-2025-0087</td>
                                        <td><span class="badge bg-success">Đã thanh toán</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCustomerModal">Chỉnh sửa</button>
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