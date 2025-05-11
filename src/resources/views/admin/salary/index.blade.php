@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-currency-fill fs-1"></i> Quản lý lương</h4>
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>

            <!-- Dashboard Cards -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #4e73df;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Tổng nhân viên</div>
                                    <h4 class="mt-2">78</h4>
                                </div>
                                <div>
                                    <i class="ri-team-fill fs-1 text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #1cc88a;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Tổng lương đã trả (T4/2025)</div>
                                    <h4 class="mt-2">1.25 tỷ</h4>
                                </div>
                                <div>
                                    <i class="ri-currency-fill fs-1 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #f6c23e;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Lương chờ xử lý</div>
                                    <h4 class="mt-2">268 triệu</h4>
                                </div>
                                <div>
                                    <i class="ri-time-fill fs-1 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #e74a3b;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Chi phí lương TB/nhân viên</div>
                                    <h4 class="mt-2">16.8 triệu</h4>
                                </div>
                                <div>
                                    <i class="ri-line-chart-fill fs-1 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Statistics -->
            <div class="row mb-4">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Thống kê theo bộ phận</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Bộ phận</th>
                                            <th>Số người</th>
                                            <th>Tổng lương</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Tài xế</td>
                                            <td>42</td>
                                            <td>680 triệu</td>
                                        </tr>
                                        <tr>
                                            <td>Kỹ thuật</td>
                                            <td>15</td>
                                            <td>320 triệu</td>
                                        </tr>
                                        <tr>
                                            <td>Quản lý</td>
                                            <td>8</td>
                                            <td>240 triệu</td>
                                        </tr>
                                        <tr>
                                            <td>Văn phòng</td>
                                            <td>13</td>
                                            <td>278 triệu</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Biểu đồ chi phí lương theo tháng</h6>
                        </div>
                        <div class="card-body">
                            <div class="salary-chart">
                                <img src="/api/placeholder/800/300" alt="Biểu đồ chi phí lương" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-select" id="periodFilter">
                                <option value="">Kỳ lương: Tất cả</option>
                                <option value="1" selected>Tháng 4/2025</option>
                                <option value="2">Tháng 3/2025</option>
                                <option value="3">Tháng 2/2025</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="departmentFilter">
                                <option value="">Bộ phận: Tất cả</option>
                                <option value="1">Tài xế</option>
                                <option value="2">Kỹ thuật</option>
                                <option value="3">Quản lý</option>
                                <option value="4">Văn phòng</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm nhân viên...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100">
                                <i class="ri-search-line"></i>  Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Tất cả bảng lương</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Đã thanh toán</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Đang chờ duyệt</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Chưa thanh toán</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã NV</th>
                                    <th>Họ và tên</th>
                                    <th>Bộ phận</th>
                                    <th>Lương cơ bản</th>
                                    <th>Phụ cấp</th>
                                    <th>Thưởng</th>
                                    <th>Khấu trừ</th>
                                    <th>Tổng lương</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>NV001</td>
                                    <td>Nguyễn Văn A</td>
                                    <td>Tài xế</td>
                                    <td>15,000,000 ₫</td>
                                    <td>3,000,000 ₫</td>
                                    <td>2,500,000 ₫</td>
                                    <td>1,200,000 ₫</td>
                                    <td>19,300,000 ₫</td>
                                    <td><span class="status-indicator status-paid text-success">Đã thanh toán</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewSalaryModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>NV015</td>
                                    <td>Trần Thị B</td>
                                    <td>Văn phòng</td>
                                    <td>18,000,000 ₫</td>
                                    <td>2,000,000 ₫</td>
                                    <td>1,500,000 ₫</td>
                                    <td>800,000 ₫</td>
                                    <td>20,700,000 ₫</td>
                                    <td><span class="status-indicator status-paid text-success"> Đã thanh toán</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewSalaryModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>NV032</td>
                                    <td>Phạm Văn C</td>
                                    <td>Kỹ thuật</td>
                                    <td>20,000,000 ₫</td>
                                    <td>2,500,000 ₫</td>
                                    <td>0 ₫</td>
                                    <td>1,500,000 ₫</td>
                                    <td>21,000,000 ₫</td>
                                    <td><span class="status-indicator status-pending text-warning"> Chờ duyệt</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewSalaryModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>NV048</td>
                                    <td>Lê Thị D</td>
                                    <td>Quản lý</td>
                                    <td>25,000,000 ₫</td>
                                    <td>5,000,000 ₫</td>
                                    <td>3,000,000 ₫</td>
                                    <td>2,200,000 ₫</td>
                                    <td>30,800,000 ₫</td>
                                    <td><span class="status-indicator status-unpaid text-danger">Chưa thanh toán</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewSalaryModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
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