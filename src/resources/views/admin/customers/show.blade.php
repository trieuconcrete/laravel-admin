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
                            <h4>{{ $customer->name }}</h4>
                            <p class="text-muted">Mã khách hàng: {{ $customer->customer_code }}</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><i class="fas fa-building me-2 text-primary"></i> {{ $customer->getTypeLabelAttribute() }}</p>
                                    <p><i class="fas fa-map-marker-alt me-2 text-{{ $customer->getStatusBadgeClassAttribute() }}"></i>{{ $customer->address }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-{{ $customer->getStatusBadgeClassAttribute() }} mb-2">{{ $customer->getStatusLabelAttribute() }}</span>
                            <p><i class="fas fa-calendar-alt me-2 text-primary"></i> Ngày đăng ký: {{ $customer->establishment_date ? $customer->establishment_date->format('d/m/y') : null }}</p>
                        </div>
                    </div>
                </div>

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="customerDetailTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#generalInfo">Thông tin chung</a>
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
                        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="fullnameInput" placeholder="Enter your Full name" value="{{ old('name', $customer->name) }}">
                                        @error('name')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Mã khách hàng</label>
                                        <input disabled type="text" class="form-control" name="name" id="fullnameInput" placeholder="Enter your customer code" value="{{ old('customer_code', $customer->customer_code) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Mã số thuế <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="tax_code" id="fullnameInput" placeholder="Enter your tax_code" value="{{ old('tax_code', $customer->tax_code) }}">
                                        @error('tax_code')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Ngày thành lập</label>
                                        <input type="date" class="form-control" name="establishment_date" value="{{ old('establishment_date', optional($customer->establishment_date)->format('Y-m-d')) }}">
                                        @error('establishment_date')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Loại khách hàng: <span class="text-danger">*</span></label>
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
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Địa chỉ </label>
                                        <input type="text" class="form-control" name="address" id="fullnameInput" placeholder="Enter your address" value="{{ old('address', $customer->address) }}">
                                        @error('address')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="phone" id="fullnameInput" placeholder="Enter your address" value="{{ old('phone', $customer->phone) }}">
                                        @error('phone')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="fullnameInput" placeholder="Enter your email" value="{{ old('email', $customer->email) }}">
                                        @error('email')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="fullnameInput" class="form-label">Website </label>
                                        <input type="text" class="form-control" name="website" id="fullnameInput" placeholder="Enter your website" value="{{ old('website', $customer->website) }}">
                                        @error('website')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-start">
                                        <button type="submit" class="btn btn-secondary">Lưu</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                        </form>
                    </div>

                    <!-- monthly report -->
                    <div class="tab-pane fade" id="monthlyReport">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0 text-start">
                                <h6 class="mb-1">Tháng</h6>
                                <select class="form-select">
                                    @foreach(months_list() as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- <div class="d-flex justify-content-between mb-3">
                            <h6>Bảng kê vận chuyển tháng {{ date('m/Y') }}</h6>
                        </div> --}}
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
                                            <div class="btn-group">
                                                <a href="" class="btn btn-sm btn-outline-primary">Sửa</a>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    Xóa
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>20/03/2025</td>
                                        <td>Thanh toán</td>
                                        <td class="text-success">+30,000,000 VNĐ</td>
                                        <td>PAY-2025-0098</td>
                                        <td><span class="badge bg-success">Hoàn thành</span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="" class="btn btn-sm btn-outline-primary">Sửa</a>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    Xóa
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>05/03/2025</td>
                                        <td>Hóa đơn</td>
                                        <td class="text-danger">-30,000,000 VNĐ</td>
                                        <td>INV-2025-0087</td>
                                        <td><span class="badge bg-success">Đã thanh toán</span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="" class="btn btn-sm btn-outline-primary">Sửa</a>
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
                </div>
            </div>
        </div>
    </div>
</div>
<!-- container-fluid -->

@endsection