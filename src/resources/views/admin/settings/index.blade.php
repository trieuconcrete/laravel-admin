@extends('admin.layout')

@section('title', 'Quản lý cài đặt')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Quản lý cài đặt hệ thống</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Cài đặt</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Cài đặt hệ thống</h6>
            <div>
                <form id="clear-cache-form" action="{{ route('admin.settings.clear-cache') }}" method="GET" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="fas fa-broom mr-1"></i> Xóa cache
                    </button>
                </form>
                <form id="reset-settings-form" action="{{ route('admin.settings.reset') }}" method="GET" class="d-inline ml-2">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-undo mr-1"></i> Khôi phục mặc định
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
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
                        <a class="nav-link {{ $activeTab == 'notifications' ? 'active' : '' }}" id="notifications-tab" data-bs-toggle="tab" href="#notifications" role="tab" aria-controls="notifications" aria-selected="{{ $activeTab == 'notifications' ? 'true' : 'false' }}">
                            <i class="fas fa-bell mr-1"></i> Thông báo
                        </a>
                    </li>
                </ul>

                <input type="hidden" name="group" id="settingGroup" value="{{ $activeTab ?? 'company' }}">
                
                <div class="tab-content" id="settingsTabContent">
                    <div class="tab-pane fade {{ $activeTab == 'company' ? 'show active' : '' }}" id="company" role="tabpanel" aria-labelledby="company-tab">
                        <div class="row">
                            @include('admin.settings.partials.company')
                        </div>
                    </div>
                    
                    <div class="tab-pane fade {{ $activeTab == 'system' ? 'show active' : '' }}" id="system" role="tabpanel" aria-labelledby="system-tab">
                        <div class="row">
                            @include('admin.settings.partials.system')
                        </div>
                    </div>
                    
                    <div class="tab-pane fade {{ $activeTab == 'shipment' ? 'show active' : '' }}" id="shipment" role="tabpanel" aria-labelledby="shipment-tab">
                        <div class="row">
                            @include('admin.settings.partials.shipment')
                        </div>
                    </div>
                    
                    <div class="tab-pane fade {{ $activeTab == 'notifications' ? 'show active' : '' }}" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                        <div class="row">
                            @include('admin.settings.partials.notifications')
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Lưu cài đặt
                    </button>
                </div>
            </form>
        </div>
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
        $('form').on('submit', function() {
            const activeTabId = $('.nav-link.active').attr('href').replace('#', '');
            $('#settingGroup').val(activeTabId);
            console.log('Form submitted, setting group to:', activeTabId);
        });
        
        // Xử lý hiển thị lỗi validation trong tab tương ứng
        $(document).ready(function() {
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
        });
        
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
