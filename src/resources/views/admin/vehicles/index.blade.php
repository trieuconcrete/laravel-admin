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
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #4e73df;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Tổng phương tiện</div>
                                    <h4 class="mt-2">152</h4>
                                </div>
                                <div>
                                    <i class="ri-truck-fill fs-1 text-muted"></i>
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
                                    <div class="text-muted">Đang hoạt động</div>
                                    <h4 class="mt-2">125</h4>
                                </div>
                                <div>
                                    <i class="ri-check-fill fs-1 text-success"></i>
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
                                    <div class="text-muted">Đang bảo trì</div>
                                    <h4 class="mt-2">15</h4>
                                </div>
                                <div>
                                    <i class="ri-tools-fill fs-1 text-warning"></i>
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
                                    <div class="text-muted">Sắp hết hạn đăng kiểm</div>
                                    <h4 class="mt-2">12</h4>
                                </div>
                                <div>
                                    <i class="ri-alert-fill fs-1 text-danger"></i>
                                </div>
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
                            <select class="form-select" id="vehicleTypeFilter">
                                <option value="">Tất cả loại xe</option>
                                <option value="1">Xe tải</option>
                                <option value="2">Container</option>
                                <option value="3">Xe thùng</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="maintenance">Đang bảo trì</option>
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
                                <i class="fas fa-filter me-2"></i>Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicles Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Tất cả phương tiện</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Đăng kiểm sắp hết hạn</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Bảo hiểm sắp hết hạn</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Lịch bảo trì</a>
                        </li>
                    </ul>
                </div>
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
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#vehicleDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>51C-123.45</td>
                                    <td>Xe tải</td>
                                    <td>Nguyen Van A</td>
                                    <td>5 tấn</td>
                                    <td><span class="status-indicator status-active"></span> Hoạt động</td>
                                    <td>Còn hạn (05/12/2025)</td>
                                    <td>Còn hạn (15/08/2025)</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#vehicleDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>51D-456.78</td>
                                    <td>Container</td>
                                    <td>Nguyen Van B</td>
                                    <td>20 tấn</td>
                                    <td><span class="status-indicator status-maintenance"></span> Bảo trì</td>
                                    <td>Còn hạn (18/09/2025)</td>
                                    <td>Còn hạn (30/11/2025)</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#vehicleDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>51H-789.01</td>
                                    <td>Xe thùng</td>
                                    <td>Nguyen Van C</td>
                                    <td>0.1 tấn</td>
                                    <td><span class="status-indicator status-active"></span> Hoạt động</td>
                                    <td>Sắp hết hạn (30/06/2025)</td>
                                    <td>Còn hạn (22/07/2025)</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#vehicleDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                    <td>51A-345.67</td>
                                    <td>Xe tải</td>
                                    <td>Nguyen Van D</td>
                                    <td>8 tấn</td>
                                    <td><span class="status-indicator status-expired"></span> Hết hạn đăng kiểm</td>
                                    <td><span class="text-danger">Hết hạn (15/04/2025)</span></td>
                                    <td>Còn hạn (10/09/2025)</td>
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

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVehicleModalLabel">Thêm phương tiện mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Biển số xe</label>
                            <input type="text" class="form-control" placeholder="Nhập biển số xe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại phương tiện</label>
                            <select class="form-select">
                                <option value="">Chọn loại phương tiện</option>
                                <option value="1">Xe tải</option>
                                <option value="2">Container</option>
                                <option value="3">Xe thùng</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tải trọng (tấn)</label>
                            <input type="number" step="0.1" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Năm sản xuất</label>
                            <input type="number" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select">
                                <option value="active">Đang hoạt động</option>
                                <option value="maintenance">Đang bảo trì</option>
                                <option value="inactive">Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <h6>Thông tin đăng kiểm</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số giấy đăng kiểm</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tệp đính kèm</label>
                        <input type="file" class="form-control">
                    </div>
                    <hr>
                    <h6>Thông tin bảo hiểm</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số hợp đồng bảo hiểm</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tệp đính kèm</label>
                        <input type="file" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Lưu phương tiện</button>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Detail Modal -->
<div class="modal fade" id="vehicleDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông tin chi tiết phương tiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-truck fa-5x text-primary mb-3"></i>
                            <h5>Xe tải - 51C-123.45</h5>
                            <span class="badge bg-success">Đang hoạt động</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Biển số</p>
                                <p class="fw-bold">51C-123.45</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Loại phương tiện</p>
                                <p class="fw-bold">Xe tải</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Tải trọng</p>
                                <p class="fw-bold">5 tấn</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Năm sản xuất</p>
                                <p class="fw-bold">2020</p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="nav nav-tabs" id="vehicleDetailTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#documents">Giấy tờ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#maintenance">Lịch sử bảo trì</a>
                    </li>
                </ul>
                <div class="tab-content p-3 border border-top-0 rounded-bottom">
                    <div class="tab-pane fade show active" id="documents">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Đăng kiểm</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>Số đăng kiểm:</td>
                                                        <td>DK123456789</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày cấp:</td>
                                                        <td>05/12/2024</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày hết hạn:</td>
                                                        <td>05/12/2025</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Trạng thái:</td>
                                                        <td><span class="badge bg-success">Còn hạn</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tài liệu:</td>
                                                        <td><a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Tải xuống</a></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Bảo hiểm</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <tr>
                                                        <td>Số hợp đồng:</td>
                                                        <td>BH987654321</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày cấp:</td>
                                                        <td>15/08/2024</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ngày hết hạn:</td>
                                                        <td>15/08/2025</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Trạng thái:</td>
                                                        <td><span class="badge bg-success">Còn hạn</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tài liệu:</td>
                                                        <td><a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Tải xuống</a></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="maintenance">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Lịch sử bảo trì</h6>
                            <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Thêm lịch bảo trì</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Loại bảo trì</th>
                                        <th>Đơn vị thực hiện</th>
                                        <th>Chi phí</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>15/03/2025</td>
                                        <td>Bảo dưỡng định kỳ</td>
                                        <td>Garage Minh Phát</td>
                                        <td>2,500,000 VNĐ</td>
                                        <td><span class="badge bg-success">Hoàn thành</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10/01/2025</td>
                                        <td>Thay lốp xe</td>
                                        <td>Garage Minh Phát</td>
                                        <td>4,800,000 VNĐ</td>
                                        <td><span class="badge bg-success">Hoàn thành</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>20/05/2025</td>
                                        <td>Thay dầu máy</td>
                                        <td>Garage Thành Đạt</td>
                                        <td>850,000 VNĐ</td>
                                        <td><span class="badge bg-warning text-dark">Đã lên lịch</span></td>
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editVehicleModal">Chỉnh sửa</button>
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