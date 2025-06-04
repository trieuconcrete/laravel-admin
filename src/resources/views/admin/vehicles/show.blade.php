@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-xxl-3">
            <div class="card mt-n5">
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
            <!--end card-->
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-danger">
                                <i class="ri-phone-fill"></i>
                            </span>
                        </div>
                        <span style="line-height: 32px">{{ $user->phone }}</span>
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-primary">
                                <i class="ri-mail-fill"></i>
                            </span>
                        </div>
                        <span style="line-height: 32px">{{ $user->email }}</span>
                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-9">
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
                            <a class="nav-link" data-bs-toggle="tab" href="#salary" role="tab">
                                <i class="far fa-user"></i> Xe đi trong tháng({{ date(\App\Helpers\DateHelper::getMonthYearFormat()) }})
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
                            <form action="{{ route('admin.profile.update', $user) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="fullnameInput" class="form-label">Họ và tên</label>
                                            <input type="text" class="form-control" name="full_name" id="fullnameInput" placeholder="Enter your Full name" value="{{ $user->full_name }}">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="emailInput" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" id="emailInput" placeholder="Enter your Email" value="{{ $user->email }}">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="phonenumberInput" class="form-label">Số điện thoại</label>
                                            <input type="text" class="form-control" name="phone" id="phonenumberInput" placeholder="Enter your phone number" value="{{ $user->phone }}">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="birthdayInput" class="form-label">Ngày sinh</label>
                                            <input type="date" class="form-control" name="birthday" id="birthdayInput" placeholder="Enter your email" value="{{ $user->birthday }}">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="idNumber" class="form-label">CCCD/CMND</label>
                                            <input type="text" class="form-control" name="id_number" placeholder="Nhập CCCD/CMND">
                                        </div>
                                    </div>
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="salaryBase" class="form-label">Lương cơ bản</label>
                                            <input type="text" class="form-control" name="salary_base" value="" placeholder="Nhập Lương cơ bản">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Địa chỉ</label>
                                            <input type="text" class="form-control" placeholder="Nhập địa chỉ">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Ghi chú</label>
                                            <textarea row=3 class="form-control" placeholder="Nhập ghi chú"></textarea>
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
                        <!--end tab-pane-->
                        <div class="tab-pane" id="driverLicense" role="tabpanel">
                            <form action="#" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Số bằng lái</label>
                                            <input type="text" class="form-control" value="" placeholder="Nhập Số bằng lái">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="licenseType" class="form-label">Loại bằng lái</label>
                                            <select name="license_type" class="form-control">
                                                <option value="">Chọn bằng lái</option>
                                                @foreach ($licenses as $key => $val )
                                                    <option value="{{ $key }}" {{ request('license_type') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Ngày cấp</label>
                                            <input type="date" class="form-control" value="@formatDateForInput($vehicle?->license?->issue_date ?? null)" placeholder="Nhập ngày cấp">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Ngày hết hạn</label>
                                            <input type="date" class="form-control" value="@formatDateForInput($vehicle?->license?->expiry_date ?? null)" placeholder="Nhập ngày hết hạn">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Nơi cấp</label>
                                            <input type="text" class="form-control" value="" placeholder="Nhập nơi cấp">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Trạng thái</label>
                                            <input type="text" class="form-control" readonly value="">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="block text-gray-700">Hình ảnh bằng lái</label>
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
                        <!--end tab-pane-->
                        <div class="tab-pane" id="salary" role="tabpanel">
                            <h2>Salary</h2>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <form action="{{ route('admin.profile.change-password', $user) }}" method="POST">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="passwordInput" class="form-label">Password*</label>
                                            <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Enter password">
                                        </div>
                                        @error('password')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Confirm Password*</label>
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
</script>
@endpush