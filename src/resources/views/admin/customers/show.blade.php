@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <!-- Customer Info Header -->
    <div class="row mt-5">
        <!--end col-->
        <div class="col-xxl-12">
            <div class="card mt-xxl-n5">
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
                        <a class="nav-link" data-bs-toggle="tab" href="#monthlyReport">Bảng kê theo tháng</a>
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
                                <thead class="table-light">
                                    <tr>
                                        <th>Họ tên</th>
                                        <th>Chức vụ</th>
                                        <th>Điện thoại</th>
                                        <th>Email</th>
                                        <th>Trạng thái</th>
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
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Trần Thị Y</td>
                                        <td>Kế toán trưởng</td>
                                        <td>0987654321</td>
                                        <td>tranthiy@abccompany.com</td>
                                        <td></td>
                                        <td>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- monthly report -->
                    <div class="tab-pane fade" id="monthlyReport">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Bảng kê vận chuyển tháng {{ date('m/Y') }}</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã chuyến hàng</th>
                                        <th>Ngày</th>
                                        <th>Điểm đi</th>
                                        <th>Điểm đến</th>
                                        <th>Số chuyến</th>
                                        <th>Số tấn xe</th>
                                        <th>Đơn giá</th>
                                        <th>Phụ thu bốc xếp</th>
                                        <th>Phụ thu kết hợp</th>
                                        <th>Thành tiền</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>CH001</td>
                                        <td>10/05/2025</td>
                                        <td>AD - KCN NHƠN TRẠCH2</td>
                                        <td>EVERTIE - KCN NHƠN TRẠCH VI</td>
                                        <td>1</td>
                                        <td>5</td>
                                        <td>900,000</td>
                                        <td></td>
                                        <td></td>
                                        <td>900,000</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>CH002</td>
                                        <td>12/05/2025</td>
                                        <td>AD - KCN NHƠN TRẠCH2</td>
                                        <td>EVERTIE - KCN NHƠN TRẠCH VI</td>
                                        <td>1</td>
                                        <td>5</td>
                                        <td>900,000</td>
                                        <td></td>
                                        <td>50,000</td>
                                        <td>950,000</td>
                                        <td></td>
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
        </div>
    </div>
</div>
<!-- container-fluid -->

@endsection