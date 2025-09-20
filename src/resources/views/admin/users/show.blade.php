@extends('admin.layout')
@section('title', 'Thông tin cá nhân')
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
                                    <input type="hidden" name="tab" id="activeTabInput" value="personalDetails">
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
                                                    <label for="phonenumberInput" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="phone" id="phonenumberInput" placeholder="Enter your phone number" value="{{ old('phone', $user->phone) }}">
                                                    @error('phone')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label for="idNumber" class="form-label">Số CCCD <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="id_number" placeholder="Nhập số CCCD" value="{{ old('id_number', $user->id_number) }}">
                                                    @error('id_number')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6">
                                                <label class="form-label">Ngày cấp </label>
                                                <input type="date" name="id_number_issuance_date" class="form-control" value="{{ old('id_number', $user->id_number_issuance_date) }}">
                                                <div class="text-danger error" data-field="id_number_issuance_date"></div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="emailInput" class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email" id="emailInput" placeholder="Enter your Email" value="{{ old('email', $user->email) }}">
                                                    @error('email')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="birthdayInput" class="form-label">Ngày sinh</label>
                                                    <input type="date" class="form-control" name="birthday" id="birthdayInput" placeholder="Enter your email" value="@formatDateForInput($user?->birthday)">
                                                    @error('birthday')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="joinDateInput" class="form-label">Ngày vào làm <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="join_date" id="joinDateInput" placeholder="Enter your email" value="@formatDateForInput($user?->join_date)">
                                                    @error('join_date')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6">
                                                <div class="mb-4">
                                                    <label for="salaryBase" class="form-label">Giới tính</label>
                                                    <select name="gender" class="form-select">
                                                        <option value="1" @selected($user->gender == 1)>Nam</option>
                                                        <option value="0" @selected($user->gender == 0)>Nữ</option>
                                                    </select>
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

                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label for="salaryBase" class="form-label">Lương cơ bản </label>
                                                    <input type="text" class="form-control" name="salary_base" placeholder="Nhập Lương cơ bản" value="{{ old('salary_base', $user->salary_base == '0.00' ? null : $user->salary_base) }}">
                                                    @error('salary_base')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Bảo hiểm xã hội</label>
                                                    <div class="form-check form-check-secondary">
                                                        <input class="form-check-input" type="checkbox" value="1" id="hasInsurance" name="has_insurance" {{ old('has_insurance', $user->has_insurance) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="hasInsurance">
                                                            Có đóng bảo hiểm
                                                        </label>
                                                    </div>
                                                    @error('has_insurance')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xxl-6" id="insuranceStartDateContainer" style="{{ old('has_insurance', $user->has_insurance) ? '' : 'display: none;' }}">
                                                    <div class="mb-3">
                                                        <label for="insuranceStartDate" class="form-label">Ngày bắt đầu đóng bảo hiểm</label>
                                                        <input type="date" class="form-control" name="insurance_start_date" id="insuranceStartDate" value="{{ old('insurance_start_date', $user->insurance_start_date) }}">
                                                        @error('insurance_start_date')
                                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            <div class="col-xxl-6" id="socialInsuranceAmountContainer" style="{{ old('has_insurance', $user->has_insurance) ? '' : 'display: none;' }}">
                                                <div class="mb-3">
                                                    <label for="socialInsuranceAmount" class="form-label">Mức lương đóng BHXH (VND)</label>
                                                    <input type="text" class="form-control" name="social_insurance_amount" id="socialInsuranceAmount"
                                                           value="{{ old('social_insurance_amount', $user->social_insurance_amount ? number_format($user->social_insurance_amount, 0, ',', ',') : '') }}"
                                                           placeholder="Nhập mức lương đóng BHXH" data-mask="000,000,000">
                                                    @error('social_insurance_amount')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                    <small class="text-muted">Để trống sẽ sử dụng mức lương mặc định từ hệ thống</small>
                                                </div>
                                            </div>
                                            <div class="col-xxl-6" id="socialInsuranceNumberContainer" style="{{ old('has_insurance', $user->has_insurance) ? '' : 'display: none;' }}">
                                                <div class="mb-3">
                                                    <label for="socialInsuranceNumber" class="form-label">Số bảo hiểm</label>
                                                    <input type="text" class="form-control" name="social_insurance_number" id="socialInsuranceNumber"
                                                           value="{{ old('social_insurance_number', $user->social_insurance_number ?? '') }}"
                                                           placeholder="Nhập số bảo hiểm xã hội">
                                                    @error('social_insurance_number')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            </div>
                                            @if($user->role === 'driver')
                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label for="salaryType" class="form-label">Loại lương tài xế</label>
                                                    <select name="salary_type" id="salaryType" class="form-select">
                                                        <option value="1" {{ $user->salary_type?->value == 1 ? 'selected' : '' }}>Tài xế ăn lương cơ bản</option>
                                                        <option value="2" {{ $user->salary_type?->value == 2 ? 'selected' : '' }}>Tài xế ăn lương doanh số</option>
                                                    </select>
                                                    @error('salary_type')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6" id="salaryByPercentContainer" style="{{ $user->salary_type?->value == 2 ? '' : 'display: none;' }}">
                                                <div class="mb-3">
                                                    <label class="form-label">Lương phần trăm doanh số (%) <span class="text-muted">*Mặc định: 12%</span></label>
                                                    <input type="number" name="salary_by_percent" class="form-control" placeholder="Nhập phần trăm (VD: 12)" value="{{ old('salary_by_percent', $user->salary_by_percent ?? 12) }}" min="1" max="100" step="0.01">
                                                    <div class="text-danger error" data-field="salary_by_percent"></div>
                                                    <small class="text-muted">Phần trăm lương theo doanh số (áp dụng cho tài xế ăn lương doanh số)</small>
                                                </div>
                                            </div>
                                            @endif

                                            @if($user->isEligibleForLunchAllowance())
                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label for="lunchAllowance" class="form-label">Trợ cấp ăn trưa</label>
                                                    <div class="form-control-plaintext">
                                                        <strong>{{ number_format($user->getDailyLunchAllowance()) }} đ/ngày</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Địa chỉ </label>
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
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label class="block text-gray-700">Ảnh đại diện</label>
                                                    <input type="file" name="avatar" id="avatarInput" class="form-control mt-1 border p-2 rounded">
                                                    @if(!(isset($user->avatar) && $user->avatar) && session()->has('_avatar_temp'))
                                                        <img id="avatarPreview" src="{{ session('_avatar_temp') }}" class="w-24 h-24 rounded-full mt-4" alt="Avatar Preview">
                                                        <input type="hidden" name="_avatar_temp" value="{{ session('_avatar_temp') }}">
                                                    @else
                                                        <img id="avatarPreview" src="{{ (isset($user) && $user->avatar) ? asset('storage/' . $user->avatar) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Avatar Preview">
                                                    @endif
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
                                <input type="hidden" name="tab" id="activeTabInput" value="driverLicense">
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
                                            <label for="date" class="form-label">Ngày cấp </label>
                                            <input
                                                type="date"
                                                class="form-control"
                                                name="issue_date"
                                                value="@formatDateForInput($user?->license?->issue_date)"
                                            >
                                            @error('issue_date')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Ngày hết hạn </label>
                                            <input
                                                type="date"
                                                class="form-control"
                                                name="expiry_date"
                                                value="@formatDateForInput($user?->license?->expiry_date)"
                                            >
                                            @error('expiry_date')
                                                <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xxl-6">
                                        <div class="mb-3">
                                            <label for="date" class="form-label">Nơi cấp</label>
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
                                            <label class="block text-gray-700">Hình ảnh GPLX</label>
                                            <input type="file" name="license_file" id="license_file_input" class="form-control mt-1 border p-2 rounded">
                                            @if(!(isset($user->license) && $user->license->license_file) && session()->has('_license_file_temp'))
                                                <img id="license_file_preview" src="{{ session('_license_file_temp') }}" class="w-24 h-24 rounded-full mt-4" alt="License Preview">
                                                <input type="hidden" name="_license_file_temp" value="{{ session('_license_file_temp') }}">
                                            @else
                                                <img id="license_file_preview" src="{{ (isset($user->license) && $user->license->license_file) ? asset('storage/' . $user->license->license_file) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="License Preview">
                                            @endif
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
                            <form action="{{ route('admin.users.show', $user->id) }}" method="GET" id="shipmentMonthForm">
                                <input type="hidden" name="tab" value="shipment">
                                <div class="col-xxl-2 mb-5">
                                    <label for="month" class="form-label">Tháng</label>
                                    <select class="form-select" name="month" id="month" onchange="document.getElementById('shipmentMonthForm').submit();">
                                        @foreach(months_list(12, 12) as $month)
                                            <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã chuyến xe</th>
                                            <th>Khách hàng</th>
                                            <th>Biển số xe</th>
                                            <th>Ngày</th>
                                            <th>Điểm đi</th>
                                            <th>Điểm đến</th>
                                            <th>Số tấn</th>
                                            <th>Số chuyến</th>
                                            <th>
                                                @if($user->salary_type?->value == 2)
                                                    Giá cho tài xế
                                                @else
                                                    Giá
                                                @endif
                                            </th>
                                            <th>Trạng thái</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($shipmentsInMonth->count() > 0)
                                            @foreach($shipmentsInMonth as $shipment)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="text-primary">
                                                            {{ $shipment->shipment_code }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if($shipment->customer)
                                                        <a href="{{ route('admin.customers.show', optional($shipment->customer)->id) }}" class="text-primary">
                                                            {{ $shipment->customer->name ?? null }}
                                                        </a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $shipment?->vehicle?->plate_number ?? null}}</td>
                                                    <td>@formatDate($shipment->departure_time)</td>
                                                    <td>{{ $shipment->origin }}</td>
                                                    <td>{{ $shipment->destination }}</td>
                                                    <td>
                                                        @if($shipment->cargo_weight)
                                                            {{ $shipment->cargo_weight ?? 0 }} T
                                                        @endif
                                                    </td>
                                                    <td>{{ (int) $shipment->trip_count }}</td>
                                                    <td>
                                                        @if($user->salary_type?->value == 2)
                                                            {{ number_format((($shipment->unit_price_for_driver ?? 0) * $shipment->trip_count) * ($user->getSalaryByPercent() / 100)) }}
                                                        @else
                                                            {{ number_format($shipment->unit_price) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $shipment->status_badge_class }}">{{ $shipment->status_label }}</span>
                                                    </td>
                                                    <td>{{ $shipment->notes }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="9" class="text-center">Không có chuyến xe nào trong tháng này</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="salary" role="tabpanel">
                            @php
                                // Get the selected month from the request
                                $selectedMonth = request('month', now()->format('m/Y'));
                                list($month, $year) = explode('/', $selectedMonth);

                                // Find the salary period for the selected month
                                $salaryPeriod = \App\Models\SalaryPeriod::where('period_name', 'Kỳ lương tháng '.$selectedMonth)->first();

                                // Find the salary detail for this user and period if period exists
                                $salaryDetail = null;
                                if ($salaryPeriod) {
                                    $salaryDetail = \App\Models\SalaryDetail::where('employee_id', $user->id)
                                        ->where('period_id', $salaryPeriod->period_id)
                                        ->first();
                                }
                            @endphp

                            <div class="row mb-4">
                                <div class="col-lg-6">
                                    <div class="card salary-section">
                                        <div class="card-header bg-soft-primary">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h5 class="card-title mb-0">Bảng lương tháng</h5>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <form action="{{ route('admin.users.show', $user->id) }}" method="GET" id="salaryMonthForm" class="d-flex align-items-center gap-2">
                                                        <input type="hidden" name="tab" value="salary">
                                                        @php
                                                            $joinDate = $user->join_date ? \Carbon\Carbon::parse($user->join_date)->startOfMonth() : null;
                                                            $currentDate = now()->startOfMonth(); // Quan trọng: để đảm bảo so sánh đầu tháng
                                                            $monthsList = [];

                                                            if ($joinDate) {
                                                                // Nếu joinDate > currentDate thì không cần hiển thị gì
                                                                if ($joinDate->greaterThan($currentDate)) {
                                                                    $monthsList = [];
                                                                } else {
                                                                    $period = \Carbon\CarbonPeriod::create($joinDate, '1 month', $currentDate);

                                                                    foreach ($period as $date) {
                                                                        $monthsList[] = $date->format('m/Y');
                                                                    }

                                                                    // Optional: đảo ngược để tháng mới lên trước
                                                                    $monthsList = array_reverse($monthsList);
                                                                }
                                                            } else {
                                                                $monthsList = months_list(); // fallback mặc định 12 tháng gần nhất
                                                            }
                                                        @endphp
                                                        <select class="form-select form-select-sm" name="month" id="salaryMonth" onchange="document.getElementById('salaryMonthForm').submit();">
                                                            @foreach($monthsList as $month)
                                                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>{{ $month }}</option>
                                                            @endforeach
                                                        </select>

                                                        @if($user->isEligibleForLunchAllowance())
                                                        <button type="button" id="export-office-salary-btn" class="btn btn-sm btn-success d-inline-flex align-items-center" style="white-space: nowrap;">
                                                            <i class="ri-file-excel-2-line me-1"></i>
                                                            Xuất Bảng Lương Văn Phòng
                                                        </button>
                                                        @else
                                                        <button type="button" id="export-salary-btn" class="btn btn-sm btn-success d-inline-flex align-items-center" style="white-space: nowrap;">
                                                            <i class="ri-file-excel-2-line me-1"></i>
                                                            Xuất Bảng Lương
                                                        </button>
                                                        @endif
                                                        <button type="button" data-bs-toggle="modal" data-bs-target="#salaryAdvanceModal" class="btn btn-sm btn-primary d-inline-flex align-items-center" style="white-space: nowrap;">
                                                            <i class="ri-currency-fill me-1"></i>
                                                            Yêu Cầu
                                                        </button>
                                                        @if($salaryDetail)
                                                            <button type="button" class="btn btn-sm btn-info d-inline-flex align-items-center process-payment"
                                                                data-salary-id="{{ $salaryDetail->salary_id }}"
                                                                style="white-space: nowrap;">
                                                                <i class="ri-currency-fill me-1"></i>
                                                                {{ $salaryDetail->status === 'paid' ? 'Thanh toán lại' : 'Thanh toán' }}
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-secondary d-inline-flex align-items-center"
                                                                style="white-space: nowrap;"
                                                                disabled>
                                                                <i class="ri-information-line me-1"></i>
                                                                Chưa có dữ liệu lương
                                                            </button>
                                                        @endif
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive salary-table">
                                                <table class="table table-borderless mb-0">
                                                    <tbody>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">
                                                                @if($salaryType == 2)
                                                                    Lương doanh số ({{ $user->getSalaryByPercent() }}%)
                                                                    <small class="d-block text-muted">{{ number_format($totalTripValue) }} ₫ × {{ $user->getSalaryByPercent() }}%</small>
                                                                @else
                                                                    Lương cơ bản
                                                                @endif
                                                            </td>
                                                            <td class="text-end" data-salary="base">{{ number_format($salaryBase) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">
                                                                Trợ cấp
                                                                @if($user->isEligibleForLunchAllowance())
                                                                    <i class="bx bx-info-circle text-info ms-1"
                                                                       data-bs-toggle="tooltip"
                                                                       data-bs-placement="top"
                                                                       title="Phụ cấp cơm ngày (26 hoặc 27 ( tùy tháng ) × 35,000 đ) và các chi phí khác"></i>
                                                                @endif
                                                            </td>
                                                            <td class="text-end" data-salary="allowance">{{ number_format($totalAllowance) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Tiền thưởng</td>
                                                            <td class="text-end" data-salary="bonus">{{ number_format($totalBonus) }} đ</td>
                                                        </tr>
                                                        {{--  <tr class="border-bottom">
                                                            <td class="fw-medium">Tiền phạt</td>
                                                            <td class="text-end text-danger" data-salary="penalty">- {{ number_format($totalPenalty) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Ứng lương <small class="text-muted">(Đã duyệt/Đã chi)</small></td>
                                                            <td class="text-end text-danger" data-salary="other-deduction">- {{ number_format($totalOtherDeduction) }} đ</td>
                                                        </tr>  --}}
                                                        <tr class="border-bottom bg-soft-light">
                                                            <td class="fw-medium">Tổng trước khấu trừ</td>
                                                            <td class="text-end fw-semibold" data-salary="total-before-deduction">{{ number_format($salaryBase + $totalAllowance + $totalBonus) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Trừ BHXH ({{ \App\Models\Setting::get('social_insurance_contribution_rate', 10.5) }}%)<small class="text-muted">(Mức đóng lương cơ bản: {{ number_format($user->getSocialInsuranceAmount()) }} đ)</small></td>
                                                            <td class="text-end text-danger" data-salary="insurance">- {{ number_format($insuranceDeduction) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Trừ tiền phạt</td>
                                                            <td class="text-end text-danger" data-salary="penalty">- {{ number_format($totalPenalty) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Trừ ứng lương <small class="text-muted">(Đã duyệt/Đã chi)</small></td>
                                                            <td class="text-end text-danger" data-salary="other-deduction">- {{ number_format($totalOtherDeduction) }} đ</td>
                                                        </tr>
                                                        {{--  <tr class="border-bottom">
                                                            <td class="fw-medium">Trừ số tiền đã thanh toán <small class="text-muted">(Đã duyệt/Đã chi)</small></td>
                                                            <td class="text-end text-danger" data-salary="paid-deduction">- {{ number_format($totalPaid) }} đ</td>
                                                        </tr>  --}}
                                                        <tr>
                                                            <td class="fw-bold fs-5">Tổng lương thực nhận</td>
                                                            <td class="text-end fw-bold fs-5 text-success" data-salary="total">{{ number_format($totalSalary) }} đ</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header bg-soft-success">
                                            <h5 class="card-title mb-0">Biểu đồ lương</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="salary_chart" class="apex-charts" dir="ltr" style="height: 250px;"></div>
                                            <div class="text-center mt-3">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title rounded-circle bg-light text-primary">
                                                                    <i class="ri-money-cny-circle-line"></i>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <p class="text-muted mb-0">
                                                                    @if($salaryType == 2)
                                                                        Doanh số ({{ $user->getSalaryByPercent() }}%)
                                                                    @else
                                                                        Cơ bản
                                                                    @endif
                                                                </p>
                                                                <h6>{{ number_format($salaryBase) }} đ</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title rounded-circle bg-light text-success">
                                                                    <i class="ri-exchange-dollar-line"></i>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <p class="text-muted mb-0">Phụ cấp và thưởng</p>
                                                                <h6>{{ number_format($totalAllowance + $totalBonus) }} đ</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title rounded-circle bg-light text-danger">
                                                                    <i class="ri-subtract-line"></i>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <p class="text-muted mb-0">Khấu trừ (BHXH, Phạt, Ứng lương)</p>
                                                                <h6>{{ number_format($insuranceDeduction + $totalOtherDeduction + $totalPenalty) }} đ</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-2">Chi tiết bảng lương</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã chuyến xe</th>
                                            <th>Ngày</th>
                                            <th>Trợ cấp</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($salaryDetails) > 0)
                                            @foreach($salaryDetails as $detail)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.shipments.edit', $detail['shipment_id']) }}" class="text-primary">
                                                            {{ $detail['shipment_code'] }}
                                                        </a>
                                                    </td>
                                                    <td>@formatDate($detail['date'])</td>
                                                    <td>
                                                        @if($detail['allowance'] > 0)
                                                            {{ number_format($detail['allowance']) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $detail['notes'] }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">Không có dữ liệu lương trong tháng này</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="tab" id="activeTabInput" value="changePassword">
                                <input hidden type="text" name="user_action" value="{{ \App\Constants::USER_ACTION_CHANGE_PASSWORD }}" class="form-control">
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="passwordInput" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Enter password">
                                        </div>
                                        @error('password')
                                            <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password_confirmation" id="confirmpasswordInput" placeholder="Xác nhận mật khẩu">
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

<!-- Add Car Rental Modal -->
<div class="modal fade" id="salaryAdvanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <form id="salaryAdvanceRequestForm" enctype="multipart/form-data" action="{{ route('admin.users.salary-advance-requests.store', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                            <input class="form-control number-format" type="text" placeholder="Số tiền" name="amount" required />
                            @if(isset($paymentStatusData))
                                @php
                                    $remainingAmount = $paymentStatusData['payment_status']['remaining_amount'] ?? 0;
                                @endphp
                                @if($remainingAmount > 0)
                                <div class="form-check mt-2" id="payRemainingCheckbox" style="display: none;">
                                    <input class="form-check-input" type="checkbox" id="payRemainingAmount" name="pay_remaining_amount">
                                    <label class="form-check-label text-primary" for="payRemainingAmount">
                                        <i class="ri-money-dollar-circle-line me-1"></i>
                                        Thanh toán số tiền còn lại: <strong>{{ number_format($remainingAmount) }} đ</strong>
                                    </label>
                                </div>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày yêu cầu <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="advance_month" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                @foreach(\App\Models\SalaryAdvanceRequest::getTypes() as $value => $label)
                                    <option value="{{ $value }}" {{ $value == 'salary' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                @foreach(\App\Models\SalaryAdvanceRequest::getStatuses() as $value => $label)
                                    <option value="{{ $value }}" {{ $value == 'paid' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{--  <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Hình thức thanh toán <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                @foreach(\App\Models\Payment::getPaymentMethods() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>  --}}

                    <div class="mb-3">
                        <label class="form-label">Lý do</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập lý do" name="reason"></textarea>
                    </div>
                    <div id="salaryAdvanceRequestError" class="alert alert-danger mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submitSalaryAdvanceRequest">Tạo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add this modal edit salary advance request-->
<div class="modal fade" id="editSalaryAdvanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa yêu cầu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <form id="editSalaryAdvanceRequestForm" enctype="multipart/form-data" method="POST" action="#">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="request_id" value="">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                            <input class="form-control number-format" type="text" placeholder="Số tiền" name="amount" required />
                            @if(isset($paymentStatusData))
                                @php
                                    $remainingAmount = $paymentStatusData['payment_status']['remaining_amount'] ?? 0;
                                @endphp
                                @if($remainingAmount > 0)
                                <div class="form-check mt-2" id="payRemainingCheckboxEdit" style="display: none;">
                                    <input class="form-check-input" type="checkbox" id="payRemainingAmountEdit" name="pay_remaining_amount">
                                    <label class="form-check-label text-primary" for="payRemainingAmountEdit">
                                        <i class="ri-money-dollar-circle-line me-1"></i>
                                        Thanh toán số tiền còn lại: <strong>{{ number_format($remainingAmount) }} đ</strong>
                                    </label>
                                </div>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày yêu cầu <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="advance_month" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                @foreach(\App\Models\SalaryAdvanceRequest::getTypes() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                @foreach(\App\Models\SalaryAdvanceRequest::getStatuses() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do</label>
                        <textarea class="form-control" rows="3" placeholder="Nhập lý do" name="reason"></textarea>
                    </div>
                    <div id="editSalaryAdvanceRequestError" class="alert alert-danger mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="updateSalaryAdvanceRequest">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .tooltip-inner {
        max-width: 300px !important;
        text-align: left !important;
        white-space: pre-line !important;
        font-size: 12px !important;
        line-height: 1.4 !important;
    }

    .tooltip.show {
        opacity: 1 !important;
    }

    .bx-info-circle {
        cursor: help;
        transition: color 0.2s ease;
    }

    .bx-info-circle:hover {
        color: #0d6efd !important;
    }
</style>
@endpush

@push('scripts')
<!-- Include the salary advance requests handler script -->
<script src="{{ asset('js/salary-advance-requests-handler.js') }}"></script>
<script>
    // Number formatting for currency inputs
    document.querySelectorAll('.number-format').forEach(function(input) {
        input.addEventListener('input', function(e) {
            // Remove non-numeric characters except decimal point
            let value = this.value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            const decimalPoints = value.match(/\./g);
            if (decimalPoints && decimalPoints.length > 1) {
                const parts = value.split('.');
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Format with thousand separators
            if (value) {
                // Split by decimal point
                const parts = value.split('.');
                // Add thousand separators to the integer part
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                // Join back with decimal part if it exists
                value = parts.join('.');
            }

            this.value = value;

            // Store the raw numeric value in a data attribute for form submission
            this.dataset.rawValue = this.value.replace(/,/g, '');
        });
    });

    // Handle form submission to use raw numeric values
    document.getElementById('salaryAdvanceRequestForm').addEventListener('submit', function(e) {
        const amountInput = this.querySelector('input[name="amount"]');
        if (amountInput && amountInput.dataset.rawValue) {
            amountInput.value = amountInput.dataset.rawValue;
        }
    });

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

    function formatSalaryAsInteger(input) {
        // Get the value from the input
        let value = input.val();

        // First convert the value to a proper number to handle decimal points correctly
        // Remove all commas first
        value = value.replace(/,/g, '');

        // Try to parse as float to handle decimal values
        let numValue = parseFloat(value);

        // If it's a valid number, convert to integer and format
        if (!isNaN(numValue)) {
            // Convert to integer (remove decimal part)
            let intValue = Math.floor(numValue);

            // Format with commas for thousands
            let formatted = intValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.val(formatted);
        } else if (value) {
            // If not a valid number but has content, just remove non-digits and format
            let cleanValue = value.replace(/[^0-9]/g, '');
            if (cleanValue) {
                let formatted = cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                input.val(formatted);
            }
        }
    }

    // Format on input change
    $('input[name="social_insurance_amount"]').on('input', function() {
        formatSalaryAsInteger($(this));
    });
    // Format on change (for when value is set programmatically)
    $('input[name="social_insurance_amount"]').on('change', function() {
        formatSalaryAsInteger($(this));
    });

    // Format on input change
    $('input[name="salary_base"]').on('input', function() {
        formatSalaryAsInteger($(this));
    });

    // Format on page load
    $(document).ready(function() {
        formatSalaryAsInteger($('input[name="salary_base"]'));
        formatSalaryAsInteger($('input[name="social_insurance_amount"]'));
    });

    // Format on change (for when value is set programmatically)
    $('input[name="salary_base"]').on('change', function() {
        formatSalaryAsInteger($(this));
    });

    $('.nav-tabs-custom .nav-link').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href").replace('#', '');
        $('#activeTabInput').val(target);
    });

    // Get active tab from URL parameter or session
    function getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    let activeTab = getParameterByName('tab') || @json(session('active_tab'));
    if (activeTab) {
        $('.nav-tabs-custom .nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');

        $('.nav-tabs-custom .nav-link[href="#' + activeTab + '"]').addClass('active');
        $('#' + activeTab).addClass('show active');
    }

    // Xử lý nút xuất bảng lương
    $('#export-salary-btn').click(function () {
        // Kiểm tra xem có dữ liệu lương không
        const hasSalaryData = {{ count($salaryDetails) > 0 ? 'true' : 'false' }};
        const hasSalaryBase = {{ $salaryBase > 0 ? 'true' : 'false' }};

        if (!hasSalaryData && !hasSalaryBase) {
            Swal.fire({
                title: 'Không có dữ liệu!',
                text: 'Không có dữ liệu lương nào trong tháng {{ $selectedMonth }} cho {{ $user->full_name }}.',
                icon: 'warning',
                confirmButtonText: 'Đóng',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            return;
        }

        Swal.fire({
            title: 'Xác nhận xuất bảng lương?',
            text: 'Bạn có chắc chắn muốn xuất bảng lương tháng {{ $selectedMonth }} của {{ $user->full_name }} không?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Có, xuất ngay',
            cancelButtonText: 'Hủy bỏ',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        const link = document.createElement('a');
                        link.href = "{{ route('admin.users.export-salary', ['user' => $user->id, 'month' => $selectedMonth]) }}";
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        setTimeout(() => {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Xuất bảng lương thành công',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }, 2000);
                    }
                });
            }
        });
    });

    // Xử lý nút xuất bảng lương văn phòng
    $('#export-office-salary-btn').click(function () {
        Swal.fire({
            title: 'Xác nhận xuất bảng lương văn phòng?',
            text: 'Bạn có chắc chắn muốn xuất bảng lương văn phòng tháng {{ $selectedMonth }} của {{ $user->full_name }} không?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Có, xuất ngay',
            cancelButtonText: 'Hủy bỏ',
            customClass: {
                confirmButton: 'btn btn-info',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        const link = document.createElement('a');
                        link.href = "{{ route('admin.users.export-office-salary', ['user' => $user->id, 'month' => $selectedMonth]) }}";
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        setTimeout(() => {
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Xuất bảng lương văn phòng thành công',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }, 2000);
                    }
                });
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        // ApexCharts options and config
        var options = {
            series: [{{ $salaryBase }}, {{ $totalAllowance }}, {{ $insuranceDeduction }}, {{ $totalPenalty }}, {{ $totalOtherDeduction }}, {{ $totalBonus }}],
            chart: {
                height: 250,
                type: 'pie',
            },
            labels: [
                @if($salaryType == 2)
                    'Lương doanh số ({{ $user->getSalaryByPercent() }}%)',
                @else
                    'Lương cơ bản',
                @endif
                'Trợ cấp', 'BHXH', 'Phạt', 'Ứng lương', 'Thưởng'
            ],
            colors: ['#0ab39c', '#299cdb', '#f06548', '#ea5455', '#ff9f43', '#28c76f'],
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '14px',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 12
                },
                itemMargin: {
                    horizontal: 6,
                    vertical: 3
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    const value = opts.w.config.series[opts.seriesIndex];
                    if (value === 0) return '';
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                },
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold'
                },
                dropShadow: {
                    enabled: false
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        if (value === 0) return '0 ₫';
                        return new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value);
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 200
                    }
                }
            }]
        };

        // Clear existing chart if any
        const chartContainer = document.querySelector("#salary_chart");
        if (chartContainer) {
            // Destroy existing chart if it exists
            if (window.salaryChart) {
                window.salaryChart.destroy();
            }
            chartContainer.innerHTML = '';
        }

        var chart = new ApexCharts(chartContainer, options);
        chart.render();

        // Store chart instance globally for updates
        window.salaryChart = chart;

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Format number inputs
        $('.number-format').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value !== '') {
                value = parseInt(value, 10).toLocaleString('vi-VN');
                $(this).val(value);
            }
        });

        // Handle checkbox for paying remaining amount
        $(document).on('change', '#payRemainingAmount, #payRemainingAmountEdit', function() {
            const isChecked = $(this).is(':checked');
            const amountInput = $(this).closest('.modal-body').find('input[name="amount"]');

            if (isChecked) {
                // Get remaining amount from the label text
                const labelText = $(this).next('label').text();
                const remainingAmountMatch = labelText.match(/(\d{1,3}(,\d{3})*)/);

                if (remainingAmountMatch) {
                    const remainingAmount = remainingAmountMatch[0].replace(/,/g, '');
                    amountInput.val(parseInt(remainingAmount).toLocaleString('vi-VN'));
                }
            } else {
                // Clear the amount input when unchecked
                amountInput.val('');
            }
        });

        // Handle type selection to show/hide remaining amount checkbox
        $(document).on('change', 'select[name="type"]', function() {
            const selectedType = $(this).val();
            const modalBody = $(this).closest('.modal-body');
            const payRemainingCheckbox = modalBody.find('#payRemainingCheckbox, #payRemainingCheckboxEdit');

            if (selectedType === 'payment') {
                payRemainingCheckbox.show();
            } else {
                payRemainingCheckbox.hide();
                // Uncheck and clear amount when switching to other types
                payRemainingCheckbox.find('input[type="checkbox"]').prop('checked', false);
                modalBody.find('input[name="amount"]').val('');
            }
        });

        // Initialize checkbox visibility on modal open
        $(document).on('shown.bs.modal', '#salaryAdvanceModal, #editSalaryAdvanceModal', function() {
            const modalBody = $(this).find('.modal-body');
            const selectedType = modalBody.find('select[name="type"]').val();
            const payRemainingCheckbox = modalBody.find('#payRemainingCheckbox, #payRemainingCheckboxEdit');

            if (selectedType === 'payment') {
                payRemainingCheckbox.show();
            } else {
                payRemainingCheckbox.hide();
            }
        });

        // Handle salary advance request form submission
        $('#salaryAdvanceRequestForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = $('#submitSalaryAdvanceRequest');
            const errorContainer = $('#salaryAdvanceRequestError');

            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
            errorContainer.hide();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: response.message,
                        confirmButtonText: 'Đóng',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    }).then(() => {
                        // Reset form
                        form[0].reset();
                        // Reset submit button
                        submitBtn.prop('disabled', false).html('Lưu');
                        // Close modal
                        $('#salaryAdvanceModal').modal('hide');
                        // Reload page to show new data
                        refreshSalaryAdvanceRequests();
                        refreshSalaryDetails(); // Refresh salary details after creating a new request
                    });
                },
                error: function(xhr) {
                    // Enable submit button
                    submitBtn.prop('disabled', false).text('Tạo');

                    // Show error message
                    if (xhr.status === 422) {
                        // Validation errors
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '<ul class="mb-0">';

                        $.each(errors, function(key, value) {
                            errorMessage += '<li>' + value[0] + '</li>';
                        });

                        errorMessage += '</ul>';
                        errorContainer.html(errorMessage).show();
                    } else {
                        // Other errors
                        errorContainer.text(xhr.responseJSON?.message || 'Đã xảy ra lỗi. Vui lòng thử lại.').show();
                    }
                }
            });
        });

        // Include the salary advance requests container in the salary tab
        // Find the salary tab content
        const salaryTabContent = document.querySelector('#salary');
        if (salaryTabContent) {
            // Create container if it doesn't exist
            let container = document.getElementById('salaryAdvanceRequestsContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'salaryAdvanceRequestsContainer';
                container.className = 'mt-4';
                salaryTabContent.appendChild(container);
            }

            // Load salary advance requests
            refreshSalaryAdvanceRequests();
        }
    });

    // Handle salary payment
    $(document).on('click', '.process-payment', function() {
        const button = $(this);
        const salaryId = button.data('salary-id');

        Swal.fire({
            title: 'Xác nhận thanh toán',
            text: 'Bạn có chắc chắn muốn thanh toán lương này?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Đang xử lý...');

                // Make AJAX request
                $.ajax({
                    url: '{{ url("admin/salary") }}/' + salaryId + '/pay',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                // Update button state for repeated payment
                                button.html('<i class="ri-currency-fill me-1"></i>Thanh toán lại');
                                button.removeClass('btn-success').addClass('btn-info');
                                button.prop('disabled', false);
                                refreshSalaryDetails(); // Refresh salary details after paying
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message,
                                confirmButtonText: 'Đóng'
                            });
                            // Re-enable button with appropriate text
                            const buttonText = button.hasClass('btn-success') ? 'Thanh toán lại' : 'Thanh toán';
                            button.prop('disabled', false).html('<i class="ri-currency-fill me-1"></i>' + buttonText);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        const errorMessage = response && response.message ? response.message : 'Đã xảy ra lỗi khi xử lý yêu cầu.';

                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage,
                            confirmButtonText: 'Đóng'
                        });

                        // Re-enable button with appropriate text
                        const buttonText = button.hasClass('btn-success') ? 'Thanh toán lại' : 'Thanh toán';
                        button.prop('disabled', false).html('<i class="ri-currency-fill me-1"></i>' + buttonText);
                    }
                });
            }
        });
    });

    // Function to refresh salary details
    function refreshSalaryDetails() {
        const selectedMonth = document.getElementById('salaryMonth')?.value || '{{ $selectedMonth }}';
        const userId = {{ $user->id }};

        // Show loading state
        const salarySection = document.querySelector('.salary-section');
        if (salarySection) {
            salarySection.style.opacity = '0.6';
            salarySection.style.pointerEvents = 'none';
        }

        // Make AJAX request to get updated salary data
        $.ajax({
            url: '{{ route("admin.users.show", $user->id) }}',
            type: 'GET',
            data: {
                month: selectedMonth,
                ajax: true
            },
            success: function(response) {
                // Debug log (remove in production)
                console.log('AJAX response:', response);

                // Update salary table
                const salaryTable = document.querySelector('.salary-table tbody');
                if (salaryTable && response.salaryData) {
                    salaryTable.innerHTML = response.salaryData;
                }

                // Update salary chart
                if (response.chartData) {
                    updateSalaryChart(response.chartData);
                }

                // Update salary summary
                if (response.summaryData) {
                    updateSalarySummary(response.summaryData);
                }

                // Re-enable section
                if (salarySection) {
                    salarySection.style.opacity = '1';
                    salarySection.style.pointerEvents = 'auto';
                }

                {{--  // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Cập nhật thành công!',
                    text: 'Thông tin lương đã được cập nhật.',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });  --}}
            },
            error: function(xhr) {
                // Re-enable section
                if (salarySection) {
                    salarySection.style.opacity = '1';
                    salarySection.style.pointerEvents = 'auto';
                }

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể cập nhật thông tin lương. Vui lòng thử lại.',
                    confirmButtonText: 'Đóng'
                });
            }
        });
    }

    // Function to update salary chart
    function updateSalaryChart(chartData) {
        try {
            if (!window.salaryChart) {
                console.error('Salary chart not initialized');
                return;
            }

            if (!chartData || !chartData.series) {
                console.error('Invalid chart data provided');
                return;
            }

            // Ensure all series values are numbers
            const sanitizedSeries = chartData.series.map(value => {
                const numValue = parseFloat(value) || 0;
                return Math.max(0, numValue); // Ensure non-negative values
            });

            // Check if all values are zero (no data)
            const hasData = sanitizedSeries.some(value => value > 0);

            if (!hasData) {
                // If no data, show a message or hide chart
                console.log('No salary data available for chart');
                return;
            }

            // Update series data with sanitized values
            window.salaryChart.updateSeries(sanitizedSeries);

            // Update labels if provided
            if (chartData.labels) {
                window.salaryChart.updateOptions({
                    labels: chartData.labels,
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            const value = opts.w.config.series[opts.seriesIndex];
                            if (value === 0) return '';
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        },
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                if (value === 0) return '0 ₫';
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    }
                });
            }

            // Debug log (remove in production)
            console.log('Chart updated with data:', {
                series: sanitizedSeries,
                labels: chartData.labels,
                total: sanitizedSeries.reduce((sum, val) => sum + val, 0)
            });
        } catch (error) {
            console.error('Error updating salary chart:', error);
        }
    }

    // Function to update salary summary
    function updateSalarySummary(summaryData) {
        // Update salary base
        if (summaryData.salaryBase !== undefined) {
            const salaryBaseElement = document.querySelector('[data-salary="base"]');
            if (salaryBaseElement) {
                salaryBaseElement.textContent = new Intl.NumberFormat('vi-VN').format(summaryData.salaryBase) + ' đ';
            }
        }

        // Update total allowance
        if (summaryData.totalAllowance !== undefined) {
            const totalAllowanceElement = document.querySelector('[data-salary="allowance"]');
            if (totalAllowanceElement) {
                totalAllowanceElement.textContent = new Intl.NumberFormat('vi-VN').format(summaryData.totalAllowance) + ' đ';
            }
        }

        // Update total salary
        if (summaryData.totalSalary !== undefined) {
            const totalSalaryElement = document.querySelector('[data-salary="total"]');
            if (totalSalaryElement) {
                totalSalaryElement.textContent = new Intl.NumberFormat('vi-VN').format(summaryData.totalSalary) + ' đ';
            }
        }

        // Update other salary components
        if (summaryData.insuranceDeduction !== undefined) {
            const insuranceElement = document.querySelector('[data-salary="insurance"]');
            if (insuranceElement) {
                insuranceElement.textContent = new Intl.NumberFormat('vi-VN').format(summaryData.insuranceDeduction) + ' đ';
            }
        }

        if (summaryData.totalOtherDeduction !== undefined) {
            const otherDeductionElement = document.querySelector('[data-salary="other-deduction"]');
            if (otherDeductionElement) {
                otherDeductionElement.textContent = new Intl.NumberFormat('vi-VN').format(summaryData.totalOtherDeduction) + ' đ';
            }
        }

        if (summaryData.totalPenalty !== undefined) {
            const penaltyElement = document.querySelector('[data-salary="penalty"]');
            if (penaltyElement) {
                penaltyElement.textContent = new Intl.NumberFormat('vi-VN').format(summaryData.totalPenalty) + ' đ';
            }
        }
    }

    // Handle salary type change to show/hide salary_by_percent input
    $(document).ready(function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true,
                template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="max-width: 300px; text-align: left;"></div></div>'
            });
        });

        $('#salaryType').on('change', function() {
            const salaryType = $(this).val();
            const salaryByPercentContainer = $('#salaryByPercentContainer');
            const salaryByPercentInput = $('input[name="salary_by_percent"]');

            if (salaryType === '2') { // Tài xế ăn lương doanh số
                salaryByPercentContainer.show();
                // Set default value nếu input rỗng
                if (!salaryByPercentInput.val()) {
                    salaryByPercentInput.val(12);
                }
            } else { // Tài xế ăn lương cơ bản
                salaryByPercentContainer.hide();
                salaryByPercentInput.val(''); // Clear value khi hide
            }
        });

        // Trigger change event on page load để set đúng trạng thái
        $('#salaryType').trigger('change');

        // Handle insurance checkbox
        $('#hasInsurance').on('change', function() {
            const insuranceStartDateContainer = $('#insuranceStartDateContainer');
            const insuranceStartDateInput = $('#insuranceStartDate');
            const socialInsuranceAmountContainer = $('#socialInsuranceAmountContainer');
            const socialInsuranceAmountInput = $('#socialInsuranceAmount');
            const socialInsuranceNumberContainer = $('#socialInsuranceNumberContainer');
            const socialInsuranceNumberInput = $('#socialInsuranceNumber');

            if ($(this).is(':checked')) {
                insuranceStartDateContainer.show();
                socialInsuranceAmountContainer.show();
                socialInsuranceNumberContainer.show();
            } else {
                insuranceStartDateContainer.hide();
                insuranceStartDateInput.val(''); // Clear the date when unchecked
                socialInsuranceAmountContainer.hide();
                socialInsuranceAmountInput.val(''); // Clear the amount when unchecked
                socialInsuranceNumberContainer.hide();
                socialInsuranceNumberInput.val(''); // Clear the number when unchecked
            }
        });

        // Trigger change event on page load để set đúng trạng thái cho insurance
        $('#hasInsurance').trigger('change');
    });
</script>
@endpush
