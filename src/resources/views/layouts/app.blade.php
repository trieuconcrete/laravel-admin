<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@section('title', 'Nguyen Trieu - IT Professional Portfolio')</title>
    @stack('meta')
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #64748b;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        /* Avatar styling */
        .avatar-ring {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            padding: 3px;
            animation: pulse-ring 2s ease-in-out infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        
        /* Header language select */
        #language-select {
            min-width: 80px;
        }
        #language-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Skill tag animation */
        .skill-tag {
            transition: all 0.3s ease;
        }
        .skill-tag:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Timeline line */
        .timeline-line {
            background: linear-gradient(180deg, #e2e8f0 0%, #cbd5e1 100%);
        }
        
        /* Fade in animation */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Post card hover effect */
        .post-card {
            transition: all 0.3s ease;
        }
        .post-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        /* Line clamp for blog excerpts */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Mobile menu animation */
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .mobile-menu.active {
            transform: translateX(0);
        }
        
        /* Form validation styles */
        .form-error {
            border-color: #ef4444 !important;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Button loading state */
        .btn-loading {
            opacity: 0.8;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900">
    
    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="lg:hidden fixed top-4 left-4 z-50 bg-white p-3 rounded-lg shadow-lg">
        <i class="fas fa-bars text-gray-700"></i>
    </button>
    
    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="mobile-menu lg:translate-x-0 fixed left-0 top-0 h-full w-72 bg-white shadow-xl z-40 overflow-y-auto">
        <div class="p-4">
            <!-- Profile Section -->
            <div class="text-center mb-3">
                <div class="avatar-ring w-32 h-32 mx-auto mb-4 rounded-full">
                    <img src="{{ asset('assets/images/avatar.jpg') }}" 
                         alt="Nguyen Trieu Avatar" 
                         class="w-full h-full rounded-full bg-white">
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2" data-vi="Nguyen Trieu" data-en="Nguyen Trieu">Nguyen Trieu</h1>
                <p class="text-gray-600 mb-4" data-vi="Senior Web Developer" data-en="Web Developer">Senior Web Developer</p>
                <div class="flex justify-center space-x-3 mb-5">
                    <a href="https://github.com/trieuconcrete" target="_blank" class="text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/nguy%E1%BB%85n-b%C3%A1-tri%E1%BB%81u-0b545079/" target="_blank" class="text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="mailto:trieunb@concrete-corp.com" target="_blank" class="text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                    </a>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="#about" class="nav-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-user w-5 mr-3"></i>
                    <span data-vi="Giới thiệu" data-en="About Me">Giới thiệu</span>
                </a>
                <a href="#skills" class="nav-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-code w-5 mr-3"></i>
                    <span data-vi="Kỹ năng" data-en="Skills">Kỹ năng</span>
                </a>
                <a href="#experience" class="nav-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-briefcase w-5 mr-3"></i>
                    <span data-vi="Kinh nghiệm" data-en="Experience">Kinh nghiệm</span>
                </a>
                <a href="#projects" class="nav-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-project-diagram w-5 mr-3"></i>
                    <span data-vi="Dự án" data-en="Projects">Dự án</span>
                </a>
                <a href="#blog" class="nav-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-pen-fancy w-5 mr-3"></i>
                    <span data-vi="Bài viết" data-en="Blog">Bài viết</span>
                </a>
                <a href="#contact" class="nav-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-envelope w-5 mr-3"></i>
                    <span data-vi="Liên hệ" data-en="Contact">Liên hệ</span>
                </a>
            </nav>
            
            <!-- Stats -->
            <div class="mt-5 pt-5 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">8+</div>
                        <div class="text-sm text-gray-600" data-vi="Năm kinh nghiệm" data-en="Years Experience">Năm kinh nghiệm</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">20+</div>
                        <div class="text-sm text-gray-600" data-vi="Dự án hoàn thành" data-en="Projects Completed">Dự án hoàn thành</div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="lg:ml-72 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800"></h2>
                    <div class="flex items-center space-x-4">
                        <select id="language-select" class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium outline-none cursor-pointer hover:border-gray-400 transition-colors bg-white">
                            <option value="vi">🇻🇳 VIE</option>
                            <option value="en">🇬🇧 ENG</option>
                        </select>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content Feed -->
        @yield('content')
    </main>
    
    <!-- Back to top button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hidden hover:bg-blue-700 transition-colors z-30">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    @stack('scripts')
</body>
</html>