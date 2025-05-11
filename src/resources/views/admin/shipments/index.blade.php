@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4><i class="ri-route-fill fs-1"></i> Quản lý hành trình</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createShipmentModal">
                                        <i class="ri-add-circle-line align-middle me-1"></i>Thêm chuyến hàng 
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
                                    <div class="text-muted">Tổng hành trình</div>
                                    <h4 class="mt-2">287</h4>
                                </div>
                                <div>
                                    <i class="ri-route-fill fs-1 text-muted"></i>
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
                                    <h4 class="mt-2">23</h4>
                                </div>
                                <div>
                                    <i class="ri-truck-fill fs-1 text-success"></i>
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
                                    <div class="text-muted">Sắp khởi hành</div>
                                    <h4 class="mt-2">15</h4>
                                </div>
                                <div>
                                    <i class="ri-time-fill fs-1 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card card-dashboard h-100" style="border-left-color: #36b9cc;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted">Hoàn thành (tháng)</div>
                                    <h4 class="mt-2">249</h4>
                                </div>
                                <div>
                                    <i class="ri-checkbox-circle-fill fs-1 text-info"></i>
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
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="pending">Sắp khởi hành</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="canceled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Từ</span>
                                <input type="date" class="form-control" id="startDateFilter">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Đến</span>
                                <input type="date" class="form-control" id="endDateFilter">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-filter me-2"></i>Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã chuyến</th>
                                    <th>Tuyến đường</th>
                                    <th>Tài xế</th>
                                    <th>Phương tiện</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Tiến độ</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>TR-00128</strong></td>
                                    <td>HCM - Hà Nội</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>Nguyễn Văn A</div>
                                        </div>
                                    </td>
                                    <td>51C-123.45</td>
                                    <td>
                                        <div>KH: 02/05/2025 08:00</div>
                                        <div>DK: 04/05/2025 17:00</div>
                                    </td>
                                    <td><span class="badge bg-success">Đang hoạt động</span></td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                                        </div>
                                        <small class="text-muted">75%</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tripDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>TR-00127</strong></td>
                                    <td>HCM - Đà Nẵng</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>Trần Thị B</div>
                                        </div>
                                    </td>
                                    <td>51D-456.78</td>
                                    <td>
                                        <div>KH: 02/05/2025 09:30</div>
                                        <div>DK: 03/05/2025 14:00</div>
                                    </td>
                                    <td><span class="badge bg-success">Đang hoạt động</span></td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 40%"></div>
                                        </div>
                                        <small class="text-muted">40%</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tripDetailModal">
                                                Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>TR-00124</strong></td>
                                    <td>HCM - Cần Thơ</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>Vũ Văn E</div>
                                        </div>
                                    </td>
                                    <td>51B-789.01</td>
                                    <td>
                                        <div>KH: 01/05/2025 10:00</div>
                                        <div>DK: 01/05/2025 13:30</div>
                                    </td>
                                    <td><span class="badge bg-secondary">Hoàn thành</span></td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 100%"></div>
                                        </div>
                                        <small class="text-muted">100%</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tripDetailModal">
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
                </div>
                <div class="card-footer bg-white">
                    <nav>
                        <ul class="pagination justify-content-end mb-0">
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

<!-- Create Trip Modal -->
<div class="modal fade" id="createShipmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo chuyến hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <div class="modal-body">
                <form>
                    <h5 class="mb-3">Thông tin cơ bản</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hợp đồng</label>
                            <select class="form-select">
                                <option value="">Chọn hợp đồng</option>
                                <option value="51C-123.45">HD001 - Cty TNHH ABC</option>
                                <option value="51D-456.78">HD002 - Cty TNHH XYZ</option>
                                <option value="51H-789.01">HD003 - Cty TNHH 123</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Điểm khởi hành</label>
                            <input type="text" class="form-control" placeholder="Nhập điểm khởi hành">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Điểm đến</label>
                            <input type="text" class="form-control" placeholder="Nhập điểm đến">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Thời gian khởi hành</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thời gian dự kiến đến</label>
                            <input type="datetime-local" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" rows="2" placeholder="Nhập ghi chú"></textarea>
                    </div>

                    <hr>
                    <h5 class="mb-3">Chọn tài xế và phương tiện</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phương tiện</label>
                            <select class="form-select">
                                <option value="">Chọn phương tiện</option>
                                <option value="51C-123.45">51C-123.45 (Xe tải - 5 tấn)</option>
                                <option value="51D-456.78">51D-456.78 (Container - 20 tấn)</option>
                                <option value="51H-789.01">51H-789.01 (Xe máy - 0.1 tấn)</option>
                                <option value="51A-345.67">51A-345.67 (Xe tải - 8 tấn)</option>
                                <option value="51B-789.01">51B-789.01 (Container - 15 tấn)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tài xế</label>
                            <input class="form-control" placeholder="Tự động điền khi chọn phương tiện" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Chọn tài xế khác</label>
                            <select class="form-select">
                                <option value="">Chọn tài xế</option>
                                <option value="TX001">Nguyễn Văn A (Xe tải - C)</option>
                                <option value="TX002">Trần Thị B (Xe tải - B2)</option>
                                <option value="TX003">Lê Văn C (Container - E)</option>
                                <option value="TX004">Phạm Thị D (Xe tải - C)</option>
                                <option value="TX005">Vũ Văn E (Xe tải - B2)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lơ xe</label>
                            <select class="form-select">
                                <option value="">Chọn lơ xe</option>
                                <option value="TX001">Lơ xe 1</option>
                                <option value="TX002">Lơ xe 2</option>
                                <option value="TX003">Lơ xe 2</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Thông tin hàng hóa</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Danh sách hàng hóa</label>
                            <button type="button" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i>Thêm hàng hóa
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tên hàng</th>
                                        <th>Mô tả</th>
                                        <th>Số lượng</th>
                                        <th>Trọng lượng (kg)</th>
                                        <th>Giá trị (VNĐ)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Mã hàng"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Mô tả"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Số lượng"></td>
                                        <td><input type="number" class="form-control form-control-sm" placeholder="Trọng lượng"></td>
                                        <td><input type="number" class="form-control form-control-sm" placeholder="Giá trị"></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary">Tạo chuyến hàng</button>
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