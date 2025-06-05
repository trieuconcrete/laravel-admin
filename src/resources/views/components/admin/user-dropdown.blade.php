<!-- resources/views/components/admin/user-dropdown.blade.php -->
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            @click.away="open = false"
            class="flex items-center space-x-3 text-sm rounded-lg hover:bg-gray-100 px-3 py-2 transition-colors">
        <div class="relative">
            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3b82f6&color=fff' }}" 
                 alt="{{ $user->name }}" 
                 class="w-8 h-8 rounded-full object-cover">
            @if($user->is_online ?? false)
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
            @endif
        </div>
        <div class="hidden md:block text-left">
            <p class="font-medium text-gray-700">{{ $user->name }}</p>
            <p class="text-xs text-gray-500">{{ $user->role_display_name ?? 'Administrator' }}</p>
        </div>
        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
    </button>
    
    <!-- Dropdown menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @keydown.escape.window="open = false"
         class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden z-50">
        
        <!-- User info header -->
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center">
                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3b82f6&color=fff' }}" 
                     alt="{{ $user->name }}" 
                     class="w-10 h-10 rounded-full object-cover">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
        </div>
        
        <!-- Quick stats -->
        @if(isset($showStats) && $showStats)
        <div class="px-4 py-3 bg-white border-b border-gray-200">
            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->posts_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Bài viết</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $user->total_views ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Lượt xem</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Menu items -->
        <nav class="py-2">
            <a href="{{ route('admin.profile') }}" 
               class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors group">
                <i class="fas fa-user-circle w-5 text-gray-400 group-hover:text-gray-600"></i>
                <span class="ml-3">Thông tin cá nhân</span>
            </a>
            
            <a href="{{ route('admin.account.settings') }}" 
               class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors group">
                <i class="fas fa-user-cog w-5 text-gray-400 group-hover:text-gray-600"></i>
                <span class="ml-3">Cài đặt tài khoản</span>
            </a>
            
            <a href="{{ route('admin.security') }}" 
               class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors group">
                <i class="fas fa-shield-alt w-5 text-gray-400 group-hover:text-gray-600"></i>
                <span class="ml-3">Bảo mật</span>
                @if($user->should_change_password ?? false)
                    <span class="ml-auto">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Cần cập nhật
                        </span>
                    </span>
                @endif
            </a>
            
            <a href="{{ route('admin.activity.log') }}" 
               class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors group">
                <i class="fas fa-history w-5 text-gray-400 group-hover:text-gray-600"></i>
                <span class="ml-3">Lịch sử hoạt động</span>
            </a>
        </nav>
        
        <!-- Theme toggle -->
        <div class="px-4 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700">
                    <i class="fas fa-moon w-5 text-gray-400"></i>
                    <span class="ml-3">Chế độ tối</span>
                </span>
                <button @click="$dispatch('toggle-dark-mode')" 
                        class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        :class="{ 'bg-blue-600': darkMode }">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                          :class="{ 'translate-x-6': darkMode, 'translate-x-1': !darkMode }"></span>
                </button>
            </div>
        </div>
        
        <!-- Language selector -->
        @if(isset($showLanguage) && $showLanguage)
        <div class="px-4 py-3 border-t border-gray-200">
            <label class="text-xs text-gray-500 uppercase tracking-wider">Ngôn ngữ</label>
            <select class="mt-1 block w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="vi" selected>Tiếng Việt</option>
                <option value="en">English</option>
            </select>
        </div>
        @endif
        
        <!-- Logout -->
        <div class="border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}" class="p-2">
                @csrf
                <button type="submit" 
                        class="flex items-center justify-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors group">
                    <i class="fas fa-sign-out-alt w-5 group-hover:text-red-700"></i>
                    <span class="ml-3 font-medium">Đăng xuất</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Keyboard shortcuts hint -->
<div x-show="open" 
     x-transition.opacity
     class="fixed bottom-4 right-4 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg shadow-lg">
    Nhấn <kbd class="px-1.5 py-0.5 bg-gray-700 rounded">ESC</kbd> để đóng
</div>