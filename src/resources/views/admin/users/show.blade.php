@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row mt-5">
        <!--end col-->
        <div class="col-xxl-12">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#driverLicense" role="tab">
                                <i class="far fa-user"></i> Bằng lái
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#shipment" role="tab">
                                <i class="far fa-user"></i> Xe đi trong tháng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#salary" role="tab">
                                <i class="far fa-user"></i> Bảng lương
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="far fa-user"></i> Cài đặt mật khẩu
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-3">
                                    <div class="card">
                                        <div class="card-body p-4">
                                            <div class="text-center">
                                                <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                                                    <img src="{{ !$user->avatar ? asset('no-image.jpeg') : asset('storage/' . $user->avatar) }}" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                                                </div>
                                                <h5 class="fs-16 mb-1">{{ $user->full_name }}</h5>
                                                <p class="text-muted mb-0">{{ $user->role }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-9">
                                <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input hidden type="text" name="user_action" value="{{ \App\Constants::USER_ACTION_CHANGE_INFORMATION }}" class="form-control">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="fullnameInput" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="full_name" id="fullnameInput" placeholder="Enter your Full name" value="{{ old('full_name', $user->full_name) }}">
                                                    @error('full_name')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="emailInput" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" name="email" id="emailInput" placeholder="Enter your Email" value="{{ old('email', $user->email) }}">
                                                    @error('email')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="phonenumberInput" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="phone" id="phonenumberInput" placeholder="Enter your phone number" value="{{ old('phone', $user->phone) }}">
                                                    @error('phone')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="birthdayInput" class="form-label">Ngày sinh</label>
                                                    <input type="date" class="form-control" name="birthday" id="birthdayInput" placeholder="Enter your email" value="{{ old('birthday', $user->birthday) }}">
                                                    @error('birthday')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label for="idNumber" class="form-label">CCCD/CMND <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="id_number" placeholder="Nhập CCCD/CMND" value="{{ old('id_number', $user->id_number) }}">
                                                    @error('id_number')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label for="salaryBase" class="form-label">Lương cơ bản <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="salary_base" placeholder="Nhập Lương cơ bản" value="{{ old('salary_base', $user->salary_base) }}">
                                                    @error('salary_base')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="Nhập địa chỉ" name="address" value="{{ old('address', $user->address) }}">
                                                    @error('address')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Ghi chú</label>
                                                    <textarea row=3 class="form-control" name="notes" placeholder="Nhập ghi chú">{!! old('notes', $user->notes ) !!}</textarea>
                                                    @error('notes')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6">
                                                <div class="mb-4">
                                                    <label for="salaryBase" class="form-label">Trạng thái làm việc</label>
                                                    <select name="status" class="form-select">
                                                        @foreach($statuses as $key => $label)
                                                            <option value="{{ $key }}"
                                                                {{ (string) old('status', $user->status) === (string) $key ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label class="block text-gray-700">Ảnh đại diện</label>
                                                    <input type="file" name="avatar" id="avatarInput" class="form-control mt-1 border p-2 rounded">
                                                    <img id="avatarPreview" src="{{ (isset($user) && $user->avatar) ? asset('storage/' . $user->avatar) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Avatar Preview">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-start">
                                                    <button type="submit" class="btn btn-secondary">Lưu</button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="driverLicense" role="tabpanel">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input hidden type="text" name="user_action" value="{{ \App\Constants::USER_ACTION_CHANGE_LICENSE }}" class="form-control">
                                <div class="row">
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Số bằng lái <span class="text-danger">*</span></label>
                                            <input type="text" name="license_number" class="form-control" value="{{ old('license_number', optional($user->license)->license_number) }}" placeholder="Nhập Số bằng lái">
                                            @error('license_number')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="licenseType" class="form-label">Loại bằng lái <span class="text-danger">*</span></label>
                                            <select name="license_type" class="form-control">
                                                <option value="">Chọn bằng lái</option>
                                                @foreach ($licenses as $key => $val )
                                                    <option value="{{ $key }}"
                                                        {{ (string) old('license_type', optional($user->license)->license_type) === (string) $key ? 'selected' : '' }}>
                                                        {{ $val }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('license_type')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Ngày cấp <span class="text-danger">*</span></label>
                                            <input 
                                                type="date"
                                                class="form-control"
                                                name="issue_date"
                                                value="{{ old('issue_date', optional($user->license)->issue_date ? optional($user->license)->issue_date->format('Y-m-d') : '') }}"
                                            >
                                            @error('issue_date')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Ngày hết hạng <span class="text-danger">*</span></label>
                                            <input 
                                                type="date"
                                                class="form-control"
                                                name="expiry_date"
                                                value="{{ old('expiry_date', optional($user->license)->expiry_date ? optional($user->license)->expiry_date->format('Y-m-d') : '') }}"
                                            >
                                            @error('expiry_date')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Nơi cấp <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ old('issued_by', optional($user->license)->issued_by) }}" name="issued_by" placeholder="Nhập nơi cấp">
                                            @error('issued_by')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Trạng thái</label>
                                            <select name="license_status" class="form-select">
                                                @foreach($licenseStatuses as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ (string) old('license_status', optional($user->license)->status) === (string) $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('license_status')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="block text-gray-700">Hình ảnh bằng lái</label>
                                            <input type="file" name="license_file" id="license_file_input" class="form-control mt-1 border p-2 rounded">
                                            <img id="license_file_preview" src="{{ (isset($user->license) && $user->license->license_file) ? asset('storage/' . $user->license->license_file) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Avatar Preview">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-start">
                                            <button type="submit" class="btn btn-secondary">Lưu</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <!--end tab-pane-->

                        <div class="tab-pane" id="shipment" role="tabpanel">
                            <div class="col-xxl-2 mb-5">
                                <label for="salaryBase" class="form-label">Tháng</label>
                                <select class="form-select">
                                    @foreach(months_list() as $month)
                                        <option value="{{ $month }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã chuyến hàng</th>
                                            <th>Biển số xe</th>
                                            <th>Ngày</th>
                                            <th>Điểm đi</th>
                                            <th>Điểm đến</th>
                                            <th>Số tấn/chuyến</th>
                                            <th>Giá</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CH001</td>
                                            <td>60LD 035.08</td>
                                            <td>10/10/2025</td>
                                            <td>CO</td>
                                            <td>HANAM-TENMA-NASAN</td>
                                            <td>8 T</td>
                                            <td>{{ number_format(1000000) }}</td>
                                            <td>Hoàn thành</td>
                                        </tr>
                                        <tr>
                                            <td>CH002</td>
                                            <td>60LD 035.08</td>
                                            <td>10/10/2025</td>
                                            <td>UZIN</td>
                                            <td>6 DANH</td>
                                            <td>14 T</td>
                                            <td>{{ number_format(2300000) }}</td>
                                            <td>Hoàn thành</td>
                                        </tr>
                                        <tr>
                                            <td>CH002</td>
                                            <td>60LD 035.08</td>
                                            <td>11/10/2025</td>
                                            <td>CO</td>
                                            <td>SEOWANG</td>
                                            <td>14 T</td>
                                            <td>{{ number_format(1200000) }}</td>
                                            <td>Hoàn thành</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="salary" role="tabpanel">
                            <div class="row mb-5">
                                <div class="col-xxl-2">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 text-start">
                                            <h6 class="mb-1">Tháng</h6>
                                            <select class="form-select">
                                                @foreach(months_list() as $month)
                                                    <option value="{{ $month }}">{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-10">   
                                    <div class="table-responsive">
                                        <table class="table table-nowrap table-striped-columns mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th scope="col">Tổng</th>
                                                    <th scope="col">Lương cơ bản</th>
                                                    <th scope="col">Trợ cấp</th>
                                                    <th scope="col">Trừ tiến ứng</th>
                                                    <th scope="col">Trừ BHXH</th>
                                                    <th scope="col">Tổng lương còn lại</th>   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ number_format(12000000) }}</td>
                                                    <td>{{ number_format(6000000) }}</td>
                                                    <td>{{ number_format(300000) }}</td>
                                                    <td>{{ number_format(num: 3000000) }}</td>
                                                    <td>{{ number_format((5500000*10.5)/100) }}</td>
                                                    <td>
                                                        {{ number_format(15000000) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h5 class="mb-2">Chi tiết bảng lương</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã chuyến hàng</th>
                                            <th>Ngày</th>
                                            <th>Số tiền</th>
                                            <th>Trợ cấp</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CH001</td>
                                            <td>10/10/2025</td>
                                            <td>{{ number_format(600000) }}</td>
                                            <td>{{ number_format(30000) }} Cơm trưa</td>
                                            <td>Hoàn thành</td>
                                        </tr>
                                        <tr>
                                            <td>CH002</td>
                                            <td>10/10/2025</td>
                                            <td>{{ number_format(750000) }}</td>
                                            <td>{{ number_format(100000) }} Đi sớm</td>
                                            <td>Hoàn thành</td>
                                        </tr>
                                        <tr>
                                            <td>CH002</td>
                                            <td>11/10/2025</td>
                                            <td>{{ number_format(750000) }}</td>
                                            <td>{{ number_format(150000) }} Chủ nhật</td>
                                            <td>Hoàn thành</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input hidden type="text" name="user_action" value="{{ \App\Constants::USER_ACTION_CHANGE_PASSWORD }}" class="form-control">
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="passwordInput" class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Enter password">
                                        </div>
                                        @error('password')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password_confirmation" id="confirmpasswordInput" placeholder="Confirm password">
                                        </div>
                                        @error('password_confirmation')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12 mt-3">
                                        <div class="text-start">
                                            <button type="submit" class="btn btn-secondary">Lưu</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

</div>
<!-- container-fluid -->

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

    document.getElementById('license_file_input').addEventListener('change', function(event) {
        const file = event.target.files[0];
    
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('license_file_preview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    });

    $('input[name="salary_base"]').on('input', function () {
        let value = $(this).val();

        value = value.replace(/[^0-9.]/g, '');

        let parts = value.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';

        $(this).val(integerPart + decimalPart);
    });

    let salaryInput = $('input[name="salary_base"]');
    let initial = salaryInput.val().replace(/[^0-9.]/g, '');
    if (initial) {
        let parts = initial.split('.');
        let formatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        if (parts[1]) {
            formatted += '.' + parts[1].slice(0, 2);
        }
        salaryInput.val(formatted);
    }
</script>
@endpush