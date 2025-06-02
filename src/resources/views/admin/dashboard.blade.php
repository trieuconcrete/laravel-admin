@extends('admin.layout')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">{{ greeting_message() }}</h4>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createShipmentModal">
                                            <i class="ri-add-circle-line align-middle me-1"></i>Thêm chuyến hàng 
                                        </button>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng số phương tiện</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalUsers }}">0</span> </h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx bxs-truck text-secondary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng số tài xế</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $activeUsers }}">0</span></h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bxs-group text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng số khách hàng</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $inactiveUsers }}">0</span> </h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-user-circle text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng tiền lương tháng này</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $usersThisMonth }}">0</span> </h4>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div> <!-- end row-->

            </div> <!-- end .h-100-->

        </div> <!-- end col -->
    </div>

</div>
<!-- container-fluid -->


<!-- Create Shipment Modal -->
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
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select">
                                <option value="pending">Chờ xác nhận</option>
                                <option value="in_transit">Đang vận chuyển</option>
                                <option value="delivered">Đã giao hàng</option>
                                <option value="cancelled">Đã hủy</option>
                                <option value="delayed">Bị trễ</option>
                                <option value="completed">Hoàn thành</option>
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
                            <input type="datetime-local" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thời gian dự kiến đến</label>
                            <input type="datetime-local" class="form-control" value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}">
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