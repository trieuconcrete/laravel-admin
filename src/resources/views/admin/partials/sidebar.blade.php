<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="24">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.dashboard') }}">
                        <i class="ri-dashboard-line"></i> </i> <span data-key="t-dashboards">{{ __('sidebar.report') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.users.index') }}">
                        <i class="ri-group-line"></i> </i> <span data-key="t-users">{{ __('sidebar.driver_management') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.vehicles.index') }}">
                        <i class="ri-truck-line"></i> </i> <span data-key="t-vehicles">{{ __('sidebar.vehicles_management') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.customers.index') }}">
                        <i class="ri-team-line"></i> </i> <span data-key="t-customers">{{ __('sidebar.customer_management') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.quotes.index') }}">
                        <i class="ri-suitcase-line"></i> </i> <span data-key="t-trips">{{ __('sidebar.quote_management') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.shipments.index') }}">
                        <i class="ri-route-line"></i> </i> <span data-key="t-trips">{{ __('sidebar.shipment_management') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('admin.salary.index') }}">
                        <i class="ri-currency-line"></i> </i> <span data-key="t-trips">{{ __('sidebar.salary_management') }}</span>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link menu-link" href="#">
                        <i class="ri-file-list-2-line"></i> </i> <span data-key="t-timekeeping">{{ __('sidebar.income_and_expense_management') }}</span>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#">
                        <i class="ri-settings-2-line"></i> </i> <span data-key="t-settings">{{ __('sidebar.settings') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->