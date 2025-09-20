@extends('admin.layout')

@section('title', 'Quản lý cài đặt')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 pb-1">
            <div class="col-12">
                <div class="d-flex align-items-lg-center flex-lg-row flex-column mb-3">
                    <div class="flex-grow-1"></div>
                    <div class="mt-3 mt-lg-0">
                        <div class="row g-3 mb-0 align-items-center flex">
                            <div class="col-auto">
                                <form action="{{ route('admin.settings.clear-cache') }}" method="GET" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-broom mr-1"></i> Xóa cache
                                    </button>
                                </form>
                                <form action="{{ route('admin.settings.reset') }}" method="GET" class="d-inline ml-2">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-undo mr-1"></i> Khôi phục mặc định
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-xxl-12">
                <div class="card mt-xxl-n5">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            @php
                                $tabs = [
                                    'company' => ['label' => 'Thông tin công ty', 'icon' => 'fas fa-building'],
                                    'shipment-fee' => ['label' => 'Chi phí chuyến xe', 'icon' => 'fas fa-bell'],
                                    'vehicle-types' => ['label' => 'Loại xe', 'icon' => 'fas fa-car'],
                                ];
                            @endphp
                            @foreach ($tabs as $key => $tab)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $activeTab == $key ? 'active' : '' }}"
                                        id="{{ $key }}-tab" data-bs-toggle="tab" href="#{{ $key }}"
                                        role="tab" aria-controls="{{ $key }}"
                                        aria-selected="{{ $activeTab == $key ? 'true' : 'false' }}">
                                        <i class="{{ $tab['icon'] }} mr-1"></i> {{ $tab['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <div class="tab-pane fade {{ $activeTab == 'company' ? 'show active' : '' }}" id="company"
                                role="tabpanel">
                                @include('admin.settings.partials.company', ['activeTab' => $activeTab])
                            </div>
                            <div class="tab-pane fade {{ $activeTab == 'shipment-fee' ? 'show active' : '' }}"
                                id="shipment-fee" role="tabpanel">
                                @include('admin.settings.partials.shipment_fee', [
                                    'deductionTypes' => $deductionTypes,
                                ])
                            </div>
                            <div class="tab-pane fade {{ $activeTab == 'vehicle-types' ? 'show active' : '' }}"
                                id="vehicle-types" role="tabpanel">
                                @include('admin.settings.partials.vehicle_types', [
                                    'vehicleTypes' => $vehicleTypes,
                                ])
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
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.nav-tabs-custom .nav-link');

            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    const tabId = e.target.getAttribute('aria-controls');
                    const url = new URL(window.location.origin + window.location.pathname);
                    url.searchParams.set('tab', tabId);
                    window.history.pushState({}, '', url);
                });
            });

            window.addEventListener('popstate', function() {
                const params = new URLSearchParams(window.location.search);
                const tab = params.get('tab') || '{{ $activeTab }}';
                const targetTab = document.querySelector('.nav-tabs-custom .nav-link[aria-controls="' +
                    tab + '"]');
                if (targetTab) {
                    const bsTab = new bootstrap.Tab(targetTab);
                    bsTab.show();
                }
            });
        });
    </script>
@endpush
