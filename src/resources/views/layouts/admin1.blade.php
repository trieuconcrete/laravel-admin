<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js for dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Dropdown animation */
        .dropdown-enter {
            transition: all 0.2s ease-out;
            opacity: 0;
            transform: scale(0.95);
        }
        
        .dropdown-enter-active {
            opacity: 1;
            transform: scale(1);
        }
        
        /* User status indicator */
        .user-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background-color: #10b981;
            border: 2px solid white;
            border-radius: 50%;
        }
        
        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            background-color: #ef4444;
            color: white;
            font-size: 11px;
            font-weight: bold;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Header -->
    @section('header')
    <header class="bg-white shadow-sm border-b border-gray-200 relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left side -->
                <div class="flex items-center">
                    <!-- Logo/Brand -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center mr-6">
                        <i class="fas fa-tachometer-alt text-2xl text-blue-600 mr-2"></i>
                        <span class="text-xl font-bold text-gray-900">Admin Panel</span>
                    </a>
                    
                    <!-- Page title -->
                    <h1 class="text-lg font-medium text-gray-700">@yield('title')</h1>
                </div>
                
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    @yield('header-actions')
                    
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @click.away="open = false"
                                class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        
                        <!-- Notification dropdown -->
                        <div x-show="open"
                             x-transition:enter="dropdown-enter"
                             x-transition:enter-start="dropdown-enter"
                             x-transition:enter-end="dropdown-enter-active"
                             x-transition:leave="dropdown-enter-active"
                             x-transition:leave-start="dropdown-enter-active"
                             x-transition:leave-end="dropdown-enter"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900">Thông báo</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <!-- Notification items -->
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-comment-dots text-blue-500"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm text-gray-900">Bình luận mới từ <strong>Nguyễn Văn A</strong></p>
                                            <p class="text-xs text-gray-500 mt-1">5 phút trước</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user-plus text-green-500"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm text-gray-900">Người dùng mới đăng ký</p>
                                            <p class="text-xs text-gray-500 mt-1">1 giờ trước</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-chart-line text-purple-500"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm text-gray-900">Báo cáo tuần mới sẵn sàng</p>
                                            <p class="text-xs text-gray-500 mt-1">2 giờ trước</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200">
                                <a href="" class="text-sm text-blue-600 hover:text-blue-700">
                                    Xem tất cả thông báo
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Divider -->
                    <div class="h-8 w-px bg-gray-300"></div>
                    
                    <!-- User dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @click.away="open = false"
                                class="flex items-center space-x-3 text-sm rounded-lg hover:bg-gray-100 px-3 py-2 transition-colors">
                            <div class="relative">
                                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="w-8 h-8 rounded-full">
                                <span class="user-status"></span>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="font-medium text-gray-700">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->role ?? 'Administrator' }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        
                        <!-- User dropdown menu -->
                        <div x-show="open"
                             x-transition:enter="dropdown-enter"
                             x-transition:enter-start="dropdown-enter"
                             x-transition:enter-end="dropdown-enter-active"
                             x-transition:leave="dropdown-enter-active"
                             x-transition:leave-start="dropdown-enter-active"
                             x-transition:leave-end="dropdown-enter"
                             class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                            <!-- User info -->
                            <div class="px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center">
                                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="w-10 h-10 rounded-full">
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Menu items -->
                            <div class="py-2">
                                <a href="#" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-user-circle w-5 text-gray-400"></i>
                                    <span class="ml-3">Thông tin cá nhân</span>
                                </a>
                                
                                <a href="#" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-cog w-5 text-gray-400"></i>
                                    <span class="ml-3">Cài đặt</span>
                                </a>
                                
                                <a href="#" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-history w-5 text-gray-400"></i>
                                    <span class="ml-3">Lịch sử hoạt động</span>
                                </a>
                            </div>
                            
                            <!-- Status -->
                            <div class="px-4 py-2 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Trạng thái</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-sm text-gray-700">Đang hoạt động</span>
                                    </div>
                                    <button class="text-xs text-blue-600 hover:text-blue-700">Thay đổi</button>
                                </div>
                            </div>
                            
                            <!-- Logout -->
                            <div class="pt-2 border-t border-gray-200">
                                <form method="POST" action="#">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-5"></i>
                                        <span class="ml-3">Đăng xuất</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @show
    <!-- Main content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Global scripts -->
    <script>
        // Refresh button functionality
        $(document).on('click', '#refresh-btn', function() {
            $(this).find('i').addClass('fa-spin');
            location.reload();
        });
        
        // Global notification handler
        function showNotification(message, type = 'success') {
            // You can integrate with toastr or other notification library here
            console.log(`${type}: ${message}`);
        }
    </script>
    
    @stack('scripts')
</body>
</html>