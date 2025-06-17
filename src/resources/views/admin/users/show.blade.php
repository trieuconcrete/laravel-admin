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
                                                    <label for="idNumber" class="form-label">CCCD/CMND <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="id_number" placeholder="Nhập CCCD/CMND" value="{{ old('id_number', $user->id_number) }}">
                                                    @error('id_number')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xxl-6">
                                                <div class="mb-3">
                                                    <label for="salaryBase" class="form-label">Lương cơ bản </label>
                                                    <input type="text" class="form-control" name="salary_base" placeholder="Nhập Lương cơ bản" value="{{ old('salary_base', $user->salary_base == '0.00' ? null : $user->salary_base) }}">
                                                    @error('salary_base')
                                                        <p class="text-danger text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
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
                                            <label class="block text-gray-700">Hình ảnh bằng lái</label>
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
                                        @foreach(months_list() as $month)
                                            <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã chuyến hàng</th>
                                            <th>Khách hàng</th>
                                            <th>Biển số xe</th>
                                            <th>Ngày</th>
                                            <th>Điểm đi</th>
                                            <th>Điểm đến</th>
                                            <th>Số tấn/chuyến</th>
                                            <th>Giá</th>
                                            <th>Trạng thái</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($shipments->count() > 0)
                                            @foreach($shipments as $shipment)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="text-primary">
                                                            {{ $shipment->shipment_code }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.customers.show', $shipment->customer->id) }}" class="text-primary">
                                                            {{ $shipment->customer->name ?? null }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $shipment?->vehicle?->plate_number ?? null}}</td>
                                                    <td>@formatDate($shipment->departure_time)</td>
                                                    <td>{{ $shipment->origin }}</td>
                                                    <td>{{ $shipment->destination }}</td>
                                                    <td>{{ $shipment->cargo_weight }} T</td>
                                                    <td>{{ number_format($shipment->unit_price) }}</td>
                                                    <td>
                                                        <span class="badge {{ $shipment->status_badge_class }}">{{ $shipment->status_label }}</span>
                                                    </td>
                                                    <td>{{ $shipment->notes }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="9" class="text-center">Không có chuyến hàng nào trong tháng này</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="salary" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header bg-soft-primary">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h5 class="card-title mb-0">Bảng lương tháng</h5>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <form action="{{ route('admin.users.show', $user->id) }}" method="GET" id="salaryMonthForm" class="d-flex align-items-center gap-2">
                                                        <input type="hidden" name="tab" value="salary">
                                                        <select class="form-select form-select-sm" name="month" id="salaryMonth" onchange="document.getElementById('salaryMonthForm').submit();">
                                                            @php
                                                                $joinDate = $user->join_date ? \Carbon\Carbon::parse($user->join_date) : null;
                                                                $currentDate = now();
                                                                $monthsList = [];
                                                                
                                                                if ($joinDate) {
                                                                    // Check if join date is in the current month
                                                                    $isJoinDateCurrentMonth = $joinDate->format('m/Y') === $currentDate->format('m/Y');
                                                                    
                                                                    if ($isJoinDateCurrentMonth) {
                                                                        // If join date is in current month, just use the current month
                                                                        $monthsList = [$currentDate->format('m/Y')];
                                                                    } else {
                                                                        // Calculate months between join date and current date
                                                                        $diffInMonths = $currentDate->diffInMonths($joinDate);
                                                                        // Get months list from join date to current date
                                                                        $monthsList = months_list($diffInMonths + 1);
                                                                        
                                                                        // Filter months to only include those >= join date
                                                                        $filteredMonths = [];
                                                                        foreach ($monthsList as $month) {
                                                                            $monthDate = \Carbon\Carbon::createFromFormat('m/Y', $month)->startOfMonth();
                                                                            if ($monthDate->greaterThanOrEqualTo($joinDate->startOfMonth())) {
                                                                                $filteredMonths[] = $month;
                                                                            }
                                                                        }
                                                                        $monthsList = array_reverse($filteredMonths); // Show newest months first
                                                                    }
                                                                } else {
                                                                    $monthsList = months_list();
                                                                }
                                                            @endphp
                                                            
                                                            @foreach($monthsList as $month)
                                                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>{{ $month }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" id="export-salary-btn" class="btn btn-sm btn-success d-inline-flex align-items-center" style="white-space: nowrap;">
                                                            <i class="ri-file-excel-2-line me-1"></i>
                                                            Xuất Bảng Lương
                                                        </button>
                                                        <button type="button" data-bs-toggle="modal" data-bs-target="#salaryAdvanceModal" class="btn btn-sm btn-primary d-inline-flex align-items-center" style="white-space: nowrap;">
                                                            <i class="ri-currency-fill me-1"></i>
                                                            Ứng Lương
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-borderless mb-0">
                                                    <tbody>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Lương cơ bản</td>
                                                            <td class="text-end">{{ number_format($salaryBase) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Trợ cấp</td>
                                                            <td class="text-end">{{ number_format($totalAllowance) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Chi phí chuyến hàng</td>
                                                            <td class="text-end">{{ number_format($totalExpenses) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom bg-soft-light">
                                                            <td class="fw-medium">Tổng trước khấu trừ</td>
                                                            <td class="text-end fw-semibold">{{ number_format($salaryBase + $totalAllowance + $totalExpenses) }} đ</td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium">Trừ BHXH (10%)</td>
                                                            <td class="text-end text-danger">- {{ number_format($insuranceDeduction) }} đ</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-bold fs-5">Tổng lương thực nhận</td>
                                                            <td class="text-end fw-bold fs-5 text-success">{{ number_format($totalSalary) }} đ</td>
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
                                                                <p class="text-muted mb-0">Cơ bản</p>
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
                                                                <p class="text-muted mb-0">Phụ cấp</p>
                                                                <h6>{{ number_format($totalAllowance + $totalExpenses) }} đ</h6>
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
                                                                <p class="text-muted mb-0">Khấu trừ</p>
                                                                <h6>{{ number_format($insuranceDeduction) }} đ</h6>
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
                                            <th>Mã chuyến hàng</th>
                                            <th>Ngày</th>
                                            <th>Chi phí chuyến hàng</th>
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
                                                    <td>{{ number_format($detail['amount']) }}</td>
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

<!-- Add Car Rental Modal -->
<div class="modal fade" id="salaryAdvanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yêu cầu ứng lương</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr>
            <form id="salaryAdvanceRequestForm" enctype="multipart/form-data" action="{{ route('admin.users.salary-advance-request', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số tiền ứng <span class="text-danger">*</span></label>
                            <input class="form-control number-format" type="text" placeholder="Số tiền" name="amount" required />
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

@endsection

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
    $('input[name="salary_base"]').on('input', function() {
        formatSalaryAsInteger($(this));
    });

    // Format on page load
    $(document).ready(function() {
        formatSalaryAsInteger($('input[name="salary_base"]'));
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

    document.addEventListener("DOMContentLoaded", function() {
        // ApexCharts options and config
        var options = {
            series: [{{ $salaryBase }}, {{ $totalAllowance }}, {{ $totalExpenses }}, {{ $insuranceDeduction }}],
            chart: {
                height: 250,
                type: 'pie',
            },
            labels: ['Lương cơ bản', 'Trợ cấp', 'Chi phí', 'BHXH'],
            colors: ['#0ab39c', '#299cdb', '#f7b84b', '#f06548'],
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
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
        
        var chart = new ApexCharts(document.querySelector("#salary_chart"), options);
        chart.render();
        
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
    });
    // Include the salary advance requests container in the salary tab
    document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endpush