@extends('layouts.admin')

@section('title', 'Bảng điều khiển')

@push('styles')
<!-- Chart.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    /* Custom styles */
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .activity-item {
        transition: all 0.2s ease;
    }
    .activity-item:hover {
        background-color: #f9fafb;
    }
    
    .quick-action-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    
    .progress-ring {
        transform: rotate(-90deg);
    }
    
    .trending-item {
        transition: background-color 0.2s ease;
    }
    .trending-item:hover {
        background-color: #f3f4f6;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Xin chào, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-blue-100">Hôm nay là {{ now()->locale('vi')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
            <div class="hidden md:block">
                <button class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Viết bài mới
                </button>
            </div>
        </div>
    </div>
    
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Posts -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-newspaper text-blue-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-green-600 bg-green-100 px-2 py-1 rounded">
                    <i class="fas fa-arrow-up text-xs mr-1"></i>12%
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">1,234</h3>
            <p class="text-sm text-gray-600 mt-1">Tổng bài viết</p>
            <div class="mt-4 flex items-center text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                Cập nhật 5 phút trước
            </div>
        </div>
        
        <!-- Total Views -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-purple-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-green-600 bg-green-100 px-2 py-1 rounded">
                    <i class="fas fa-arrow-up text-xs mr-1"></i>23%
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">45.2K</h3>
            <p class="text-sm text-gray-600 mt-1">Lượt xem</p>
            <div class="mt-4 flex items-center text-xs text-gray-500">
                <i class="fas fa-chart-line mr-1"></i>
                Tăng 2.3K tuần này
            </div>
        </div>
        
        <!-- Active Users -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-red-600 bg-red-100 px-2 py-1 rounded">
                    <i class="fas fa-arrow-down text-xs mr-1"></i>5%
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">892</h3>
            <p class="text-sm text-gray-600 mt-1">Người dùng hoạt động</p>
            <div class="mt-4">
                <div class="flex items-center">
                    <div class="flex -space-x-2">
                        <img class="w-6 h-6 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User1&background=random" alt="">
                        <img class="w-6 h-6 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User2&background=random" alt="">
                        <img class="w-6 h-6 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User3&background=random" alt="">
                    </div>
                    <span class="ml-2 text-xs text-gray-500">+889 khác</span>
                </div>
            </div>
        </div>
        
        <!-- Revenue -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-green-600 bg-green-100 px-2 py-1 rounded">
                    <i class="fas fa-arrow-up text-xs mr-1"></i>18%
                </span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">$12,345</h3>
            <p class="text-sm text-gray-600 mt-1">Doanh thu tháng này</p>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 75%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">75% mục tiêu</p>
            </div>
        </div>
    </div>
    
    <!-- Trending Posts & Todo List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Trending Posts -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Bài viết trending</h3>
                <span class="text-sm text-gray-500">
                    <i class="fas fa-fire text-orange-500 mr-1"></i>Hot
                </span>
            </div>
            <div class="space-y-3">
                <div class="trending-item p-3 rounded-lg cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 text-sm">10 framework JavaScript phổ biến nhất 2024</h4>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-gray-500">2.5K lượt xem</span>
                                <span class="mx-2 text-gray-300">•</span>
                                <span class="text-xs text-gray-500">234 bình luận</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-green-600">↑</span>
                            <span class="text-xs text-gray-500 block">+23%</span>
                        </div>
                    </div>
                </div>
                
                <div class="trending-item p-3 rounded-lg cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 text-sm">Hướng dẫn tối ưu performance React</h4>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-gray-500">1.8K lượt xem</span>
                                <span class="mx-2 text-gray-300">•</span>
                                <span class="text-xs text-gray-500">156 bình luận</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-green-600">↑</span>
                            <span class="text-xs text-gray-500 block">+15%</span>
                        </div>
                    </div>
                </div>
                
                <div class="trending-item p-3 rounded-lg cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 text-sm">AI và tương lai của lập trình</h4>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-gray-500">1.2K lượt xem</span>
                                <span class="mx-2 text-gray-300">•</span>
                                <span class="text-xs text-gray-500">89 bình luận</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-red-600">↓</span>
                            <span class="text-xs text-gray-500 block">-5%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Todo List -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Công việc cần làm</h3>
                <button class="text-sm text-blue-600 hover:text-blue-700">
                    <i class="fas fa-plus mr-1"></i>Thêm
                </button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">Review và duyệt 5 bài viết mới</p>
                        <p class="text-xs text-gray-500">Hạn: Hôm nay</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium text-red-600 bg-red-100 rounded">Urgent</span>
                </div>
                
                <div class="flex items-center p-3 rounded-lg">
                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">Cập nhật SEO cho 10 bài viết cũ</p>
                        <p class="text-xs text-gray-500">Hạn: Ngày mai</p>
                    </div>
                </div>
                
                <div class="flex items-center p-3 rounded-lg">
                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" checked>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-500 line-through">Backup database hàng tuần</p>
                        <p class="text-xs text-gray-400">Hoàn thành</p>
                    </div>
                </div>
                
                <div class="flex items-center p-3 rounded-lg">
                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">Họp team content tuần này</p>
                        <p class="text-xs text-gray-500">Hạn: Thứ 6</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Todo checkbox interactions
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const parent = this.closest('.flex');
            const text = parent.querySelector('.font-medium');
            const subtext = parent.querySelector('.text-xs');
            
            if (this.checked) {
                text.classList.add('line-through', 'text-gray-500');
                text.classList.remove('text-gray-900');
                if (subtext) {
                    subtext.textContent = 'Hoàn thành';
                    subtext.classList.add('text-gray-400');
                    subtext.classList.remove('text-gray-500');
                }
            } else {
                text.classList.remove('line-through', 'text-gray-500');
                text.classList.add('text-gray-900');
            }
        });
    });
});
</script>
@endpush