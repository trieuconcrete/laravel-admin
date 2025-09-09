    <!doctype html>
    <html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

    <head>

        <meta charset="utf-8" />
        <title>@yield('title', 'Vận Tải Hoàng Phú Long')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Vận Tải Hoàng Phú Long - An Tâm Trên Vạn Dặm" name="description" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

        <!-- jsvectormap css -->
        <link href="{{ asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

        <!--Swiper slider css-->
        <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Sweet Alert css-->
        <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">

        <!-- Layout config Js -->
        <script src="{{ asset('assets/js/layout.js') }}"></script>
        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- custom Css-->
        <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
        
        <style>
            .highlight-error {
                border-color: #ff3d60 !important;
                box-shadow: 0 0 0 0.15rem rgba(255, 61, 96, 0.25) !important;
                animation: pulse-error 1.5s ease-in-out;
            }
            
            @keyframes pulse-error {
                0% { box-shadow: 0 0 0 0 rgba(255, 61, 96, 0.4); }
                70% { box-shadow: 0 0 0 10px rgba(255, 61, 96, 0); }
                100% { box-shadow: 0 0 0 0 rgba(255, 61, 96, 0); }
            }
        </style>

        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <!-- Optional: Flatpickr Material Blue theme -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
        
        <!-- Page-specific CSS -->
        @stack('styles')
    </head>

    <body>

        <!-- Begin page -->
        <div id="layout-wrapper">
        
            @include('admin.partials.header')
            <!-- ========== App Menu ========== -->
            @include('admin.partials.sidebar')
            <!-- Vertical Overlay-->
            <div class="vertical-overlay"></div>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    {{-- @if (session('success'))
                        <div id="alert-message" class="p-4 mb-4 text-sm text-success bg-green-100 rounded-lg" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div id="alert-message" class="p-4 mb-4 text-sm text-danger bg-red-100 rounded-lg" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif --}}

                    @yield('content')
                </div>
                <!-- End Page-content -->

                @include('admin.partials.footer')

                
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->



        <!--start back-to-top-->
        <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
            <i class="ri-arrow-up-line"></i>
        </button>
        <!--end back-to-top-->

        <!--preloader-->
        <div id="preloader">
            <div id="status">
                <div class="spinner-border text-primary avatar-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <!-- JAVASCRIPT -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
        <script src="{{ asset('assets/js/plugins.js') }}"></script>
        <!--select2 cdn-->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('assets/js/pages/select2.init.js') }}"></script>

        <!-- apexcharts -->
        <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

        <!-- Vector map-->
        <script src="{{ asset('assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
        <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

        <!--Swiper slider js-->
        <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

        <!-- Dashboard init -->
        <script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

        <!-- Sweet Alerts js -->
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

        <!-- Sweet alert init js-->
        <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.js') }}"></script>
        <script src="{{ asset('assets/js/common.js') }}"></script>

        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            // Tự động ẩn alert sau 3 giây
            document.addEventListener('DOMContentLoaded', function() {
                const alert = document.getElementById('alert-message');
                if (alert) {
                    setTimeout(() => {
                        alert.style.transition = "opacity 0.5s ease";
                        alert.style.opacity = "0";
                        setTimeout(() => alert.remove(), 500); // Remove hẳn sau fadeout
                    }, 3000); // 3 giây
                }
            });
        </script>

        <x-toast-notification />

        <script>
            $(document).ready(function() {
                var currentUrl = window.location.href;
        
                $('li.nav-item a').each(function() {
                    if (this.href === currentUrl) {
                        // 1. Active <a> current
                        $(this).addClass('active');

                        // 2. Active <li> parent
                        $(this).closest('li.nav-item').addClass('active');

                        // 3. If the link is inside a submenu (div.collapse)
                        var $collapse = $(this).closest('div.collapse');
                        if ($collapse.length) {
                            $collapse.addClass('show'); // open submenu
                            $collapse.prev('a.menu-link').addClass('active').attr('aria-expanded', 'true'); // active menu parent
                        }
                    }
                });

                // Common modal configuration - Prevent closing when clicking outside
                $('.modal').each(function() {
                    // Only apply to modals that don't already have backdrop configuration
                    if (!$(this).attr('data-bs-backdrop')) {
                        $(this).attr('data-bs-backdrop', 'static');
                    }
                    if (!$(this).attr('data-bs-keyboard')) {
                        $(this).attr('data-bs-keyboard', 'false');
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                // Set system date format to dd/mm/YYYY
                const dateFormatPlaceholder = 'dd/mm/YYYY';
                const systemDateFormat = 'd/m/Y';
                
                document.querySelectorAll('input[type="date"]').forEach(function (input) {
                    // Lưu giá trị ban đầu
                    const originalValue = input.value;
                    
                    // Lấy định dạng từ thuộc tính data nếu có, mặc định là d/m/Y
                    const dateFormat = input.getAttribute('data-date-format') || "d/m/Y";
                    
                    // Chuyển từ input type="date" sang input type="text" để sử dụng flatpickr
                    input.type = 'text';
                    input.placeholder = dateFormatPlaceholder; // Sử dụng định dạng dd/mm/YYYY
                    
                    // Khởi tạo flatpickr với định dạng dd/mm/YYYY
                    flatpickr(input, {
                        dateFormat: systemDateFormat, // d/m/Y format for flatpickr
                        allowInput: true,
                        defaultDate: originalValue || null,
                        // Đảm bảo giá trị được parse đúng với định dạng dd/mm/YYYY
                        parseDate: (datestr, format) => {
                            if (datestr) {
                                // Parse date in dd/mm/YYYY format
                                const parts = datestr.split('/');
                                if (parts.length === 3) {
                                    const day = parseInt(parts[0], 10);
                                    const month = parseInt(parts[1], 10) - 1; // Month is 0-indexed
                                    const year = parseInt(parts[2], 10);
                                    return new Date(year, month, day);
                                }
                            }
                            return new Date(datestr);
                        },
                        // Format date as dd/mm/YYYY when displaying
                        formatDate: (date, format) => {
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        }
                    });
                });
            });
        </script>

        <!-- Page-specific JS -->
        @stack('scripts')
    </body>

    </html>