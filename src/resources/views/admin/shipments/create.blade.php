@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Tạo chuyến hàng</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                            <div class="row g-3 mb-0 align-items-center">
                                <div class="col-auto">
                                    <button class="btn btn-success">
                                        <i class="ri-save-3-line align-middle me-1"></i>Lưu 
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
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#driverAllowance" role="tab">
                                        <i class="far fa-user"></i> Thông tin vận chuyển 
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#shipmentDetail" role="tab">
                                        <i class="fas fa-home"></i> Phương tiện & tài xế
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="driverAllowance" role="tabpanel">
                                    <h5 class="mb-3">Thông tin vận chuyển</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Chọn khách hàng<span class="text-danger">*</span></label>
                                            <select class="form-select">
                                                <option value="">Chọn khách hàng</option>
                                                <option value="51C-123.45">Cty TNHH ABC</option>
                                                <option value="51D-456.78">Cty TNHH XYZ</option>
                                                <option value="51H-789.01">Cty TNHH 123</option>
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
                                            <label class="form-label">Thời gian khởi hành<span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Thời gian dự kiến đến</label>
                                            <input type="datetime-local" class="form-control">
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
                                            <label class="form-label">Số KM</label>
                                            <input type="text" class="form-control" placeholder="Nhập số KM">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ghi chú</label>
                                        <textarea class="form-control" rows="2" placeholder="Nhập ghi chú"></textarea>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-2">Chi phí</h5>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Bốc xếp</th>
                                                        <th>Neo xe</th>
                                                        <th>Công an</th>
                                                        <th>Cầu đường</th>
                                                        <th>Tiền tự cầu</th>
                                                        <th>Khác</th>
                                                        <th>Ghi chú</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control form-control" placeholder="Số tiền"></td>
                                                        <td><input type="text" class="form-control form-control" placeholder="Số tiền"></td>
                                                        <td><input type="text" class="form-control form-control" placeholder="Số tiền"></td>
                                                        <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                        <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                        <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-2">Danh sách hàng hóa</h5>
                                            <button type="button" class="btn btn-outline-primary">
                                                <i class="fas fa-plus me-1"></i>Thêm hàng hóa
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Tên hàng</th>
                                                        <th>Mô tả</th>
                                                        <th>Số lượng</th>
                                                        <th>Trọng lượng (Tấn)</th>
                                                        <th>Giá trị (VNĐ)</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control form-control" placeholder="Mã hàng"></td>
                                                        <td><input type="text" class="form-control form-control" placeholder="Mô tả"></td>
                                                        <td><input type="text" class="form-control form-control" placeholder="Số lượng"></td>
                                                        <td><input type="number" class="form-control form-control" placeholder="Trọng lượng"></td>
                                                        <td><input type="number" class="form-control form-control" placeholder="Giá trị"></td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger">
                                                                <i class="ri-delete-bin-fill"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="shipmentDetail" role="tabpanel">
                                    <form>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Phương tiện<span class="text-danger">*</span></label>
                                                <select class="form-select">
                                                    <option value="">Chọn phương tiện</option>
                                                    <option value="51C-123.45">51C-123.45 (Xe tải - 5 tấn)</option>
                                                    <option value="51D-456.78">51D-456.78 (Container - 20 tấn)</option>
                                                    <option value="51H-789.01">51H-789.01 (Xe máy - 0.1 tấn)</option>
                                                    <option value="51A-345.67">51A-345.67 (Xe tải - 8 tấn)</option>
                                                    <option value="51B-789.01">51B-789.01 (Container - 15 tấn)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fas fa-plus me-1"></i>Thêm tài xế hoặc lơ xe
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Chọn tài xế</th>
                                                            <th>Tiền ứng trước</th>
                                                            <th>Phụ cấp<br> cơm trưa</th>
                                                            <th>Phụ cấp<br> cơm tối</th>
                                                            <th>Phụ cấp<br> chủ nhật</th>
                                                            <th>Phụ cấp<br> đi sớm</th>
                                                            <th>Phụ cấp<br> đi khuya</th>
                                                            <th>Phụ cấp lễ</th>
                                                            <th>Phụ cấp khác</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select class="form-select">
                                                                    <option value="">Chọn tài xế</option>
                                                                    <option value="TX001">Nguyễn Văn A (Xe tải - C)</option>
                                                                    <option value="TX002">Trần Thị B (Xe tải - B2)</option>
                                                                    <option value="TX003">Lê Văn C (Container - E)</option>
                                                                    <option value="TX004">Phạm Thị D (Xe tải - C)</option>
                                                                    <option value="TX005">Vũ Văn E (Xe tải - B2)</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td><input type="text" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td><input type="number" class="form-control form-control" placeholder="Số tiền"></td>
                                                            <td>
                                                                <button type="button" class="btn btn-outline-danger">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('avatarInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
    
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
