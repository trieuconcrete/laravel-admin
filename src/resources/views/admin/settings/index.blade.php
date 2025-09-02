@extends('admin.layout')

@section('title', 'Quản lý cài đặt')

@section('content')
<div class="container-fluid">
    <div class="row mb-3 pb-1">
        <div class="col-12">
            <div class="d-flex align-items-lg-center flex-lg-row flex-column mb-3">
                <div class="flex-grow-1">
                    {{-- <h4><i class="ri-group-fill fs-1"></i>Quản lý nhân sự</h4> --}}
                </div>
                <div class="mt-3 mt-lg-0">
                    <div class="row g-3 mb-0 align-items-center flex">
                        <div class="col-auto">
                            <form id="clear-cache-form" action="{{ route('admin.settings.clear-cache') }}" method="GET" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-broom mr-1"></i> Xóa cache
                                </button>
                            </form>
                            <form id="reset-settings-form" action="{{ route('admin.settings.reset') }}" method="GET" class="d-inline ml-2">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-undo mr-1"></i> Khôi phục mặc định
                                </button>
                            </form>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div><!-- end card header -->
        </div>
        <!--end col-->
    </div>
    <div class="row mt-5">
        <!--end col-->
        <div class="col-xxl-12">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist" id="settingsTabs">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'company' ? 'active' : '' }}" id="company-tab" data-bs-toggle="tab" href="#company" role="tab" aria-controls="company" aria-selected="{{ $activeTab == 'company' ? 'true' : 'false' }}">
                                <i class="fas fa-building mr-1"></i> Thông tin công ty
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'system' ? 'active' : '' }}" id="system-tab" data-bs-toggle="tab" href="#system" role="tab" aria-controls="system" aria-selected="{{ $activeTab == 'system' ? 'true' : 'false' }}">
                                <i class="fas fa-cogs mr-1"></i> Hệ thống
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'shipment' ? 'active' : '' }}" id="shipment-tab" data-bs-toggle="tab" href="#shipment" role="tab" aria-controls="shipment" aria-selected="{{ $activeTab == 'shipment' ? 'true' : 'false' }}">
                                <i class="fas fa-truck mr-1"></i> Vận chuyển
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'shipment-fee' ? 'active' : '' }}" id="fee-tab" data-bs-toggle="tab" href="#shipment-fee" role="tab" aria-controls="shipment-fee" aria-selected="{{ $activeTab == 'shipment-fee' ? 'true' : 'false' }}">
                                <i class="fas fa-bell mr-1"></i> Chi phí chuyến xe
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'vehicle-types' ? 'active' : '' }}" id="vehicle-types-tab" data-bs-toggle="tab" href="#vehicle-types" role="tab" aria-controls="vehicle-types" aria-selected="{{ $activeTab == 'vehicle-types' ? 'true' : 'false' }}">
                                <i class="fas fa-car mr-1"></i> Loại xe
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="settingsTabContent">
                        <div class="tab-pane fade {{ $activeTab == 'company' ? 'show active' : '' }}" id="company" role="tabpanel" aria-labelledby="company-tab">
                            @include('admin.settings.partials.company', ['activeTab' => $activeTab])
                        </div>
                        
                        <div class="tab-pane fade {{ $activeTab == 'system' ? 'show active' : '' }}" id="system" role="tabpanel" aria-labelledby="system-tab">
                            @include('admin.settings.partials.system', ['activeTab' => $activeTab])
                        </div>
                        
                        <div class="tab-pane fade {{ $activeTab == 'shipment' ? 'show active' : '' }}" id="shipment" role="tabpanel" aria-labelledby="shipment-tab">
                            @include('admin.settings.partials.shipment', ['activeTab' => $activeTab])
                        </div>
                        <div class="tab-pane fade {{ $activeTab == 'shipment-fee' ? 'show active' : '' }}" id="shipment-fee" role="tabpanel" aria-labelledby="shipment-fee-tab">
                            @include('admin.settings.partials.shipment_fee', ['deductionTypes' => $deductionTypes])
                        </div>
                        <div class="tab-pane fade {{ $activeTab == 'vehicle-types' ? 'show active' : '' }}" id="vehicle-types" role="tabpanel" aria-labelledby="vehicle-types-tab">
                            @include('admin.settings.partials.vehicle_types', ['vehicleTypes' => $vehicleTypes])
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/settings.js') }}"></script>
<script>
    $(document).ready(function() {
        // Xử lý hiển thị/ẩn các trường phụ thuộc vào checkbox
        $('input[type="checkbox"]').on('change', function() {
            const target = $(this).data('toggle-target');
            if (target) {
                if ($(this).is(':checked')) {
                    $(target).removeClass('d-none');
                } else {
                    $(target).addClass('d-none');
                }
            }
        });

        // Kích hoạt sự kiện change cho tất cả checkbox khi trang tải
        $('input[type="checkbox"][data-toggle-target]').trigger('change');
        
        // Cập nhật giá trị group khi chuyển tab
        $('.nav-link').on('click', function() {
            const tabId = $(this).attr('href').replace('#', '');
            $('#settingGroup').val(tabId);
            console.log('Tab clicked, setting group to:', tabId);
        });
        
        // Lưu tab đang active vào localStorage
        $('.nav-link').on('shown.bs.tab', function (e) {
            const tabId = $(this).attr('href').replace('#', '');
            $('#settingGroup').val(tabId);
            localStorage.setItem('activeSettingsTab', tabId);
            console.log('Tab shown, setting group to:', tabId);
        });
        
        // Đảm bảo form submit đúng giá trị group
        // $('form').on('submit', function() {
        //     const activeTabId = $('.nav-link.active').attr('href').replace('#', '');
        //     $('#settingGroup').val(activeTabId);
        //     console.log('Form submitted, setting group to:', activeTabId);
        // });
        
        // Xử lý hiển thị lỗi validation trong tab tương ứng
        // $(document).ready(function() {
            // Kiểm tra xem có lỗi validation nào không
            const hasErrors = $('.is-invalid').length > 0;
            
            if (hasErrors) {
                // Tìm tab có lỗi đầu tiên và kích hoạt nó
                const tabsWithErrors = {};
                
                // Xác định các tab có lỗi
                $('.is-invalid').each(function() {
                    const inputName = $(this).attr('name');
                    if (inputName) {
                        // Lấy group từ tên input (ví dụ: company[name] -> company)
                        const groupMatch = inputName.match(/^([^\[]+)/);
                        if (groupMatch && groupMatch[1]) {
                            const group = groupMatch[1];
                            tabsWithErrors[group] = true;
                        }
                    }
                });
                
                // Kích hoạt tab đầu tiên có lỗi
                for (const tabId in tabsWithErrors) {
                    if (tabsWithErrors.hasOwnProperty(tabId)) {
                        $(`#nav-tab a[href="#${tabId}"]`).tab('show');
                        break;
                    }
                }
                
                // Thêm biểu tượng cảnh báo cho các tab có lỗi
                for (const tabId in tabsWithErrors) {
                    if (tabsWithErrors.hasOwnProperty(tabId)) {
                        const tabLink = $(`#nav-tab a[href="#${tabId}"]`);
                        if (!tabLink.find('.error-indicator').length) {
                            tabLink.append(' <i class="ri-error-warning-fill text-danger error-indicator"></i>');
                        }
                    }
                }
            }
        // });
        
        // Kiểm tra xem có tab được chỉ định trong URL không
        const urlParams = new URLSearchParams(window.location.search);
        const tabFromUrl = urlParams.get('tab');
        
        if (tabFromUrl && ['company', 'system', 'shipment', 'notifications'].includes(tabFromUrl)) {
            // Nếu có tab trong URL, sử dụng nó
            $('#' + tabFromUrl + '-tab').tab('show');
            $('#settingGroup').val(tabFromUrl);
            console.log('Tab from URL, setting group to:', tabFromUrl);
        } else {
            // Nếu không có tab trong URL, sử dụng localStorage hoặc activeTab từ controller
            const activeTab = localStorage.getItem('activeSettingsTab') || '{{ $activeTab }}';
            if (activeTab) {
                $('#' + activeTab + '-tab').tab('show');
                $('#settingGroup').val(activeTab);
                console.log('Tab from localStorage/controller, setting group to:', activeTab);
            }
        }
    });
</script>
@endsection
 