<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoàng Phú Long - Vận Tải Chuyên Nghiệp | An Tâm Trên Vạn Dặm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .hero-gradient {
            background: linear-gradient(135deg, 
                rgba(15, 23, 42, 0.9) 0%, 
                rgba(30, 41, 59, 0.85) 35%, 
                rgba(51, 65, 85, 0.8) 100%),
                url('http://vantaihoangphulong.vn/slider/2989/slide2f.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .fleet-image {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(234, 88, 12, 0.1) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600"><defs><linearGradient id="roadGrad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:%23374151"/><stop offset="50%" style="stop-color:%234b5563"/><stop offset="100%" style="stop-color:%23374151"/></linearGradient></defs><rect width="800" height="600" fill="%23f1f5f9"/><rect x="0" y="450" width="800" height="150" fill="url(%23roadGrad)"/><rect x="50" y="470" width="40" height="10" fill="%23ffffff" opacity="0.8"/><rect x="150" y="470" width="40" height="10" fill="%23ffffff" opacity="0.8"/><rect x="250" y="470" width="40" height="10" fill="%23ffffff" opacity="0.8"/><rect x="350" y="470" width="40" height="10" fill="%23ffffff" opacity="0.8"/><rect x="450" y="470" width="40" height="10" fill="%23ffffff" opacity="0.8"/><rect x="550" y="470" width="40" height="10" fill="%23ffffff" opacity="0.8"/><rect x="650" y="470" width="40" height="10" fill="%23ffffff" opacity="0.8"/><g transform="translate(150,280)"><rect x="0" y="0" width="200" height="80" rx="10" fill="%23f59e0b"/><rect x="10" y="10" width="30" height="20" rx="5" fill="%23ffffff" opacity="0.9"/><rect x="50" y="10" width="30" height="20" rx="5" fill="%23ffffff" opacity="0.9"/><rect x="90" y="10" width="30" height="20" rx="5" fill="%23ffffff" opacity="0.9"/><rect x="130" y="10" width="30" height="20" rx="5" fill="%23ffffff" opacity="0.9"/><circle cx="40" cy="95" r="20" fill="%23374151"/><circle cx="160" cy="95" r="20" fill="%23374151"/><circle cx="40" cy="95" r="12" fill="%236b7280"/><circle cx="160" cy="95" r="12" fill="%236b7280"/></g><g transform="translate(450,300)"><rect x="0" y="0" width="150" height="60" rx="8" fill="%23ef4444"/><rect x="10" y="10" width="20" height="15" rx="3" fill="%23ffffff" opacity="0.9"/><rect x="40" y="10" width="20" height="15" rx="3" fill="%23ffffff" opacity="0.9"/><circle cx="30" cy="75" r="15" fill="%23374151"/><circle cx="120" cy="75" r="15" fill="%23374151"/><circle cx="30" cy="75" r="10" fill="%236b7280"/><circle cx="120" cy="75" r="10" fill="%236b7280"/></g><rect x="0" y="0" width="800" height="150" fill="%23e0e7ff" opacity="0.3"/><circle cx="100" cy="80" r="30" fill="%23ffffff" opacity="0.6"/><circle cx="700" cy="60" r="25" fill="%23ffffff" opacity="0.4"/><circle cx="300" cy="100" r="20" fill="%23ffffff" opacity="0.5"/></svg>');
            background-size: cover;
            background-position: center;
        }
        
        .warehouse-bg {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400"><rect width="600" height="400" fill="%23f8fafc"/><rect x="50" y="150" width="500" height="200" fill="%236b7280"/><rect x="60" y="160" width="480" height="180" fill="%23e5e7eb"/><rect x="100" y="180" width="60" height="80" fill="%23f59e0b"/><rect x="180" y="180" width="60" height="80" fill="%23ef4444"/><rect x="260" y="180" width="60" height="80" fill="%2306b6d4"/><rect x="340" y="180" width="60" height="80" fill="%2322c55e"/><rect x="420" y="180" width="60" height="80" fill="%23a855f7"/><rect x="100" y="280" width="60" height="40" fill="%23f59e0b" opacity="0.7"/><rect x="180" y="280" width="60" height="40" fill="%23ef4444" opacity="0.7"/><rect x="260" y="280" width="60" height="40" fill="%2306b6d4" opacity="0.7"/><rect x="340" y="280" width="60" height="40" fill="%2322c55e" opacity="0.7"/><rect x="420" y="280" width="60" height="40" fill="%23a855f7" opacity="0.7"/><rect x="200" y="100" width="200" height="50" fill="%23374151"/><polygon points="180,100 220,50 380,50 420,100" fill="%23475569"/><rect x="280" y="110" width="40" height="30" fill="%23f59e0b"/></svg>');
            background-size: cover;
            background-position: center;
        }
        
        .service-truck-bg {
            background: url('http://vantaihoangphulong.vn/dichvu_images/2989/van%20chuyen%20hang%20hoa%20(2).jpg');
            background-size: cover;
            background-position: center;
        }
        
        .service-container-bg {
            background: url('http://vantaihoangphulong.vn/dichvu_images/2989/van%20chuyen%20hang%20sieu%20truong%20(5).jpg');
            background-size: cover;
            background-position: center;
        }
        
        .service-fertilizer-bg {
            background: url('http://vantaihoangphulong.vn/dichvu_images/2989/van%20chuyen%20hang%20sieu%20truong%20(3).jpg');
            background-size: cover;
            background-position: center;
        }
        
        .service-crane-bg {
            background: url('http://vantaihoangphulong.vn/dichvu_images/2989/van%20chuyen%20hang%20hoa%20(8).jpg');
            background-size: cover;
            background-position: center;
        }
        
        .service-tourism-bg {
            background: url('http://vantaihoangphulong.vn/dichvu_images/2989/hoang%20phu%20long%20(15).jpg');
            background-size: cover;
            background-position: center;
        }
        
        .service-rental-bg {
            background: url('http://vantaihoangphulong.vn/dichvu_images/2989/cho%20thue%20xe%20nang%20(3).jpg');
            background-size: cover;
            background-position: center;
        }
        
        .service-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        }
        
        .service-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-10px) rotate(1deg); }
            66% { transform: translateY(5px) rotate(-1deg); }
        }
        
        .slide-up {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .slide-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .nav-blur {
            transition: all 0.3s ease;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
        }
        
        .nav-blur.scrolled {
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .feature-card {
            background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
            border: 1px solid rgba(226, 232, 240, 0.5);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: rgba(249, 115, 22, 0.3);
        }
        
        .stat-counter {
            font-weight: 800;
            font-size: 3rem;
            line-height: 1;
        }
        
        .truck-icon {
            filter: drop-shadow(0 4px 20px rgba(249, 115, 22, 0.3));
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="nav-blur fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br rounded-xl truck-icon">
                        <!-- <i class="fas fa-truck text-white text-2xl"></i> -->
                        <img src="http://vantaihoangphulong.vn/logo/2989/logocuoicung.png" alt="" width="180">
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-white hover:text-orange-300 transition-colors font-medium">Trang chủ</a>
                    <a href="#services" class="text-white hover:text-orange-300 transition-colors font-medium">Dịch vụ</a>
                    <a href="#about" class="text-white hover:text-orange-300 transition-colors font-medium">Giới thiệu</a>
                    <a href="#contact" class="text-white hover:text-orange-300 transition-colors font-medium">Liên hệ</a>
                    <div class="flex items-center space-x-3">
                        <a href="tel:0917712195" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors flex items-center space-x-2">
                            <i class="fas fa-phone"></i>
                            <span>0917 712 195</span>
                        </a>
                    </div>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="lg:hidden text-white" id="mobile-menu-btn">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div class="lg:hidden bg-slate-800/95 backdrop-blur-sm rounded-lg mx-4 mb-4 hidden" id="mobile-menu">
                <div class="py-4 space-y-2">
                    <a href="#home" class="block text-white py-3 px-4 hover:bg-slate-700 rounded-lg transition-colors">Trang chủ</a>
                    <a href="#services" class="block text-white py-3 px-4 hover:bg-slate-700 rounded-lg transition-colors">Dịch vụ</a>
                    <a href="#about" class="block text-white py-3 px-4 hover:bg-slate-700 rounded-lg transition-colors">Giới thiệu</a>
                    <a href="#contact" class="block text-white py-3 px-4 hover:bg-slate-700 rounded-lg transition-colors">Liên hệ</a>
                    <div class="px-4 pt-2">
                        <a href="tel:0917712195" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg font-semibold transition-colors flex items-center justify-center space-x-2 w-full">
                            <i class="fas fa-phone"></i>
                            <span>0917 712 195</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient min-h-screen flex items-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/20 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-white slide-up">
                    <div class="inline-flex items-center bg-orange-500/20 backdrop-blur-sm border border-orange-500/30 px-4 py-2 rounded-full text-orange-300 text-sm font-medium mb-6">
                        <i class="fas fa-star me-2"></i>
                        10 năm kinh nghiệm vận tải chuyên nghiệp
                    </div>
                    <h1 class="text-5xl lg:text-5xl font-black mb-6 leading-tight">
                        Vận Tải 
                        <span class="gradient-text">Hoàng Phú Long</span>
                    </h1>
                    <p class="text-xl lg:text-2xl mb-8 text-slate-300 font-light leading-relaxed">
                        Chuyên tuyến TP.HCM - Đông Nam Bộ với dịch vụ vận chuyển hàng hóa an toàn, nhanh chóng và tiết kiệm chi phí
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="tel:0917712195" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105 flex items-center justify-center space-x-3 shadow-xl">
                            <i class="fas fa-phone"></i>
                            <span>Gọi ngay: 0917 712 195</span>
                        </a>
                        <button class="bg-white/10 hover:bg-white/20 border border-white/30 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all backdrop-blur-sm flex items-center justify-center space-x-3">
                            <i class="fas fa-calculator"></i>
                            <span>Báo giá miễn phí</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-white/20">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-400 mb-1">30+</div>
                            <div class="text-slate-400 text-sm">Phương tiện</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-400 mb-1">10+</div>
                            <div class="text-slate-400 text-sm">Năm kinh nghiệm</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-400 mb-1">GPS</div>
                            <div class="text-slate-400 text-sm">Theo dõi hành trình</div>
                        </div>
                    </div>
                </div>
                
                <div class="float-animation">
                    <div class="glass-effect rounded-3xl p-8 shadow-2xl">
                        <h3 class="text-white text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-shipping-fast text-orange-400 me-3"></i>
                            Báo giá nhanh
                        </h3>
                        <form class="space-y-5" id="quote-form">
                            <div class="grid grid-cols-1 gap-4">
                                <input type="text" placeholder="Điểm đi (VD: TP.HCM)" class="w-full px-4 py-4 rounded-xl bg-white/90 backdrop-blur-sm focus:outline-none focus:ring-4 focus:ring-orange-400/50 border border-white/20 font-medium" required>
                                <input type="text" placeholder="Điểm đến (VD: Đồng Nai)" class="w-full px-4 py-4 rounded-xl bg-white/90 backdrop-blur-sm focus:outline-none focus:ring-4 focus:ring-orange-400/50 border border-white/20 font-medium" required>
                            </div>
                            <select class="w-full px-4 py-4 rounded-xl bg-white/90 backdrop-blur-sm focus:outline-none focus:ring-4 focus:ring-orange-400/50 border border-white/20 font-medium" required>
                                <option value="">Chọn loại hàng hóa</option>
                                <option value="phan-bon">Phân bón</option>
                                <option value="sat-thep">Sắt thép, tôn cuộn</option>
                                <option value="soi-hoa-chat">Sợi, hóa chất, hạt nhựa</option>
                                <option value="hang-dien-tu">Hàng điện tử</option>
                                <option value="cong-li-tam">Cống ly tâm, cống hộp</option>
                                <option value="coc-be-tong">Cọc bê tông</option>
                                <option value="hang-sieu-trong">Hàng siêu trường, siêu trọng</option>
                                <option value="khac">Hàng hóa khác</option>
                            </select>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" placeholder="Họ tên" class="w-full px-4 py-4 rounded-xl bg-white/90 backdrop-blur-sm focus:outline-none focus:ring-4 focus:ring-orange-400/50 border border-white/20 font-medium" required>
                                <input type="tel" placeholder="Số điện thoại" class="w-full px-4 py-4 rounded-xl bg-white/90 backdrop-blur-sm focus:outline-none focus:ring-4 focus:ring-orange-400/50 border border-white/20 font-medium" required>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane me-2"></i>Nhận báo giá ngay
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gradient-to-br from-slate-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 slide-up">
                <div class="inline-flex items-center bg-orange-100 px-4 py-2 rounded-full text-orange-600 text-sm font-semibold mb-4">
                    <i class="fas fa-truck-loading me-2"></i>
                    Dịch vụ chuyên nghiệp
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-800 mb-6">
                    Dịch Vụ Vận Tải 
                    <span class="gradient-text">Chuyên Nghiệp</span>
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    Với đội xe đa dạng và kinh nghiệm 10 năm, chúng tôi cung cấp giải pháp vận chuyển toàn diện
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="service-truck-bg h-48 w-full"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">Vận chuyển hàng hóa</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Chuyên tuyến TP.HCM - Đông Nam Bộ với đội xe đa dạng: xe tải, xe đầu kéo phù hợp mọi loại hàng hóa.
                        </p>
                        <ul class="space-y-3 text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Vận chuyển sắt thép, tôn cuộn</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Hàng điện tử, hóa chất</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Theo dõi GPS 24/7</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="service-container-bg h-48 w-full"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">Hàng siêu trường & siêu trọng</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Chuyên vận chuyển cống ly tâm, cống hộp, cọc bê tông và các hàng hóa có kích thước đặc biệt.
                        </p>
                        <ul class="space-y-3 text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Xe đầu kéo chuyên dụng</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Cống ly tâm, cống hộp</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Cọc bê tông các loại</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="service-fertilizer-bg h-48 w-full"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">Vận chuyển phân bón</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Dịch vụ chuyên biệt cho các đại lý phân bón với quy trình vận chuyển an toàn và đúng quy định.
                        </p>
                        <ul class="space-y-3 text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Chuyên chở các đại lý</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Bao bì không bị thất thoát</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Giá cả cạnh tranh</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="service-crane-bg h-48 w-full"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">Dịch vụ cẩu hàng</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Cẩu hàng và nâng hạ chuyên nghiệp cho những hàng hóa có trọng lượng lớn không thể dùng sức người.
                        </p>
                        <ul class="space-y-3 text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Xe cẩu hiện đại</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Tay máy có kinh nghiệm</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>An toàn tuyệt đối</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="service-tourism-bg h-48 w-full"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">Cho thuê xe du lịch</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Dịch vụ cho thuê xe du lịch 4, 7, 16, 29, 45 chỗ uy tín và chất lượng tại Đồng Nai.
                        </p>
                        <ul class="space-y-3 text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Đa dạng loại xe</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Tài xế kinh nghiệm</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Giá cả hợp lý</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="service-rental-bg h-48 w-full"></div>
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">Cho thuê cần cẩu</h3>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            Cho thuê cần cẩu, xe nâng với mức giá cạnh tranh, tiết kiệm chi phí đầu tư cho doanh nghiệp.
                        </p>
                        <ul class="space-y-3 text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Cần cẩu cũ và mới</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Chi phí cạnh tranh</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-3"></i>
                                <span>Hỗ trợ 24/7</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Crane Rental Services Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 slide-up">
                <div class="inline-flex items-center bg-yellow-100 px-4 py-2 rounded-full text-yellow-600 text-sm font-semibold mb-4">
                    <i class="fas fa-tools me-2"></i>
                    Dịch vụ cho thuê chuyên nghiệp
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-800 mb-6">
                    Dịch Vụ Cho Thuê 
                    <span class="gradient-text">Xe Nâng, Cần Cẩu</span>
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    Tiết kiệm chi phí đầu tư với dịch vụ cho thuê thiết bị chuyên dụng chất lượng cao
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-48 w-full bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 flex items-center justify-center">
                        <div class="text-center text-yellow-700">
                            <i class="fas fa-crane text-6xl mb-4"></i>
                            <div class="text-lg font-bold">Cần cẩu lớn</div>
                            <div class="text-sm">20-50 tấn</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-3">Cần cẩu lớn</h3>
                        <p class="text-slate-600 text-sm mb-4 leading-relaxed">
                            Cho thuê cần cẩu lớn 20-50 tấn phù hợp công trình lớn, xây dựng cao tầng.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Tải trọng 20-50 tấn</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Tay máy chuyên nghiệp</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-48 w-full bg-gradient-to-br from-orange-500/20 to-orange-600/20 flex items-center justify-center">
                        <div class="text-center text-orange-700">
                            <i class="fas fa-tools text-6xl mb-4"></i>
                            <div class="text-lg font-bold">Cần cẩu nhỏ</div>
                            <div class="text-sm">5-15 tấn</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-3">Cần cẩu nhỏ</h3>
                        <p class="text-slate-600 text-sm mb-4 leading-relaxed">
                            Cho thuê cần cẩu nhỏ 5-15 tấn cho các công việc vừa và nhỏ, linh hoạt di chuyển.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Tải trọng 5-15 tấn</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Linh hoạt, dễ di chuyển</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-48 w-full bg-gradient-to-br from-blue-500/20 to-blue-600/20 flex items-center justify-center">
                        <div class="text-center text-blue-700">
                            <i class="fas fa-forklift text-6xl mb-4"></i>
                            <div class="text-lg font-bold">Xe nâng điện</div>
                            <div class="text-sm">1-3 tấn</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-3">Xe nâng điện</h3>
                        <p class="text-slate-600 text-sm mb-4 leading-relaxed">
                            Xe nâng điện thân thiện môi trường, phù hợp kho bãi, nhà máy sản xuất.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Không khí thải</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Hoạt động êm ái</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-48 w-full bg-gradient-to-br from-green-500/20 to-green-600/20 flex items-center justify-center">
                        <div class="text-center text-green-700">
                            <i class="fas fa-weight-hanging text-6xl mb-4"></i>
                            <div class="text-lg font-bold">Xe nâng xăng</div>
                            <div class="text-sm">1-5 tấn</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-3">Xe nâng xăng/gas</h3>
                        <p class="text-slate-600 text-sm mb-4 leading-relaxed">
                            Xe nâng xăng/gas mạnh mẽ, phù hợp làm việc ngoài trời và tải trọng lớn.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Công suất mạnh mẽ</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2 text-xs"></i>
                                <span>Phù hợp ngoài trời</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="text-center mt-12 slide-up">
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-3xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">💰 Tiết kiệm chi phí đầu tư với dịch vụ cho thuê</h3>
                    <p class="text-yellow-100 mb-6">Xe cẩu vốn là loại xe cần đầu tư nhiều ngân sách, thuê ngoài sẽ tiết kiệm hơn</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="tel:0917712195" class="bg-white text-yellow-600 px-6 py-3 rounded-xl font-bold hover:bg-yellow-50 transition-colors inline-flex items-center space-x-2">
                            <i class="fas fa-phone"></i>
                            <span>Báo giá cho thuê: 0917 712 195</span>
                        </a>
                        <a href="#contact" class="bg-yellow-400 hover:bg-yellow-300 text-white px-6 py-3 rounded-xl font-bold transition-colors inline-flex items-center space-x-2">
                            <i class="fas fa-calculator"></i>
                            <span>Tính chi phí</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Other Services Section -->
    <section class="py-20 bg-gradient-to-br from-slate-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 slide-up">
                <div class="inline-flex items-center bg-blue-100 px-4 py-2 rounded-full text-blue-600 text-sm font-semibold mb-4">
                    <i class="fas fa-star me-2"></i>
                    Dịch vụ đa dạng
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-800 mb-6">
                    Một Số Dịch Vụ 
                    <span class="gradient-text">Khác</span>
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                    Hoàng Phú Long cung cấp nhiều dịch vụ bổ trợ khác để đáp ứng mọi nhu cầu của khách hàng
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-40 w-full bg-gradient-to-br from-indigo-500/20 to-indigo-600/20 flex items-center justify-center">
                        <div class="text-center text-indigo-700">
                            <i class="fas fa-satellite-dish text-5xl mb-3"></i>
                            <div class="text-lg font-bold">Định vị GPS</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Dịch vụ định vị GPS</h3>
                        <p class="text-slate-600 mb-4 leading-relaxed">
                            Công nghệ GPS hiện đại để quản lý và báo cáo tiến độ di chuyển hàng hóa với khách hàng.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Theo dõi thời gian thực</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Báo cáo chi tiết hành trình</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-40 w-full bg-gradient-to-br from-purple-500/20 to-purple-600/20 flex items-center justify-center">
                        <div class="text-center text-purple-700">
                            <i class="fas fa-handshake text-5xl mb-3"></i>
                            <div class="text-lg font-bold">Tư vấn chuyên sâu</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Tư vấn tận tâm</h3>
                        <p class="text-slate-600 mb-4 leading-relaxed">
                            Đội ngũ tư vấn nhiệt tình, sẵn sàng giải đáp mọi thắc mắc và hỗ trợ khách hàng 24/7.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Tư vấn miễn phí</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Hỗ trợ 24/7</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-40 w-full bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 flex items-center justify-center">
                        <div class="text-center text-emerald-700">
                            <i class="fas fa-shield-alt text-5xl mb-3"></i>
                            <div class="text-lg font-bold">Bảo hiểm hàng hóa</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-3">An toàn tuyệt đối</h3>
                        <p class="text-slate-600 mb-4 leading-relaxed">
                            Cam kết vận chuyển an toàn, không thất thoát hàng hóa với bảo hiểm toàn diện.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Bảo hiểm 100% giá trị</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Không thất thoát hàng</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-40 w-full bg-gradient-to-br from-rose-500/20 to-rose-600/20 flex items-center justify-center">
                        <div class="text-center text-rose-700">
                            <i class="fas fa-route text-5xl mb-3"></i>
                            <div class="text-lg font-bold">Tuyến đường tối ưu</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Lộ trình tối ưu</h3>
                        <p class="text-slate-600 mb-4 leading-relaxed">
                            Nghiên cứu và lựa chọn tuyến đường tối ưu để tiết kiệm thời gian và chi phí vận chuyển.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Tiết kiệm thời gian</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Giảm chi phí nhiên liệu</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-40 w-full bg-gradient-to-br from-amber-500/20 to-amber-600/20 flex items-center justify-center">
                        <div class="text-center text-amber-700">
                            <i class="fas fa-warehouse text-5xl mb-3"></i>
                            <div class="text-lg font-bold">Kho bãi tạm</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Dịch vụ kho bãi</h3>
                        <p class="text-slate-600 mb-4 leading-relaxed">
                            Cung cấp dịch vụ lưu trữ hàng hóa tạm thời với hệ thống kho bãi an toàn, bảo mật.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>An toàn, bảo mật</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Linh hoạt thời gian</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="service-card rounded-2xl shadow-lg overflow-hidden slide-up group">
                    <div class="h-40 w-full bg-gradient-to-br from-teal-500/20 to-teal-600/20 flex items-center justify-center">
                        <div class="text-center text-teal-700">
                            <i class="fas fa-users text-5xl mb-3"></i>
                            <div class="text-lg font-bold">Đội ngũ chuyên nghiệp</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Nhân sự giàu kinh nghiệm</h3>
                        <p class="text-slate-600 mb-4 leading-relaxed">
                            Đội ngũ tài xế và nhân viên có nhiều năm kinh nghiệm, được đào tạo bài bản.
                        </p>
                        <ul class="space-y-2 text-slate-600 text-sm">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>10+ năm kinh nghiệm</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 me-2"></i>
                                <span>Đào tạo chuyên nghiệp</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Call to Action -->
            <div class="text-center mt-12 slide-up">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">🚀 Chúng tôi không ngại bất cứ sản phẩm gì, kể cả trọng tải nặng</h3>
                    <p class="text-blue-100 mb-6">Liên hệ ngay để được tư vấn chi tiết về các dịch vụ phù hợp nhất</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="tel:0917712195" class="bg-white text-blue-600 px-6 py-3 rounded-xl font-bold hover:bg-blue-50 transition-colors inline-flex items-center space-x-2">
                            <i class="fas fa-phone"></i>
                            <span>Hotline: 0917 712 195</span>
                        </a>
                        <a href="mailto:vantaihoangphulong86@gmail.com" class="bg-blue-500 hover:bg-blue-400 text-white px-6 py-3 rounded-xl font-bold transition-colors inline-flex items-center space-x-2">
                            <i class="fas fa-envelope"></i>
                            <span>Gửi email tư vấn</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-orange-600/20 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 slide-up">
                <h2 class="text-4xl lg:text-5xl font-black mb-6">
                    Tại Sao Chọn 
                    <span class="gradient-text">Hoàng Phú Long?</span>
                </h2>
                <p class="text-xl text-slate-300 max-w-3xl mx-auto">
                    Cam kết mang lại cảm giác an tâm và tin cậy đến với khách hàng
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card rounded-2xl p-8 slide-up">
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">An toàn tuyệt đối</h3>
                    <p class="text-slate-600">
                        Đội ngũ tài xế có nhiều năm kinh nghiệm, xe được bảo dưỡng định kỳ đảm bảo an toàn tuyệt đối.
                    </p>
                </div>
                
                <div class="feature-card rounded-2xl p-8 slide-up">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6">
                        <i class="fas fa-satellite-dish"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Công nghệ GPS</h3>
                    <p class="text-slate-600">
                        Áp dụng công nghệ định vị GPS hiện đại để quý khách có thể theo dõi hành trình một cách dễ dàng.
                    </p>
                </div>
                
                <div class="feature-card rounded-2xl p-8 slide-up">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Đúng giờ cam kết</h3>
                    <p class="text-slate-600">
                        Vận chuyển nhanh chóng đến đúng địa điểm, thời gian giao hàng chính xác như cam kết.
                    </p>
                </div>
                
                <div class="feature-card rounded-2xl p-8 slide-up">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Giá cả cạnh tranh</h3>
                    <p class="text-slate-600">
                        Với kinh nghiệm 10 năm, chúng tôi cung cấp cước phí cạnh tranh nhất, tiết kiệm chi phí tối đa.
                    </p>
                </div>
                
                <div class="feature-card rounded-2xl p-8 slide-up">
                    <div class="bg-gradient-to-br from-red-500 to-red-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Hỗ trợ 24/7</h3>
                    <p class="text-slate-600">
                        Hoàng Phú Long luôn hỗ trợ nhiệt tình và tận tâm bất cứ khi nào quý khách cần.
                    </p>
                </div>
                
                <div class="feature-card rounded-2xl p-8 slide-up">
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Không thất thoát</h3>
                    <p class="text-slate-600">
                        Cam kết vận chuyển an toàn, không thất thoát hàng hóa với quy trình chuyên nghiệp.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-20 bg-gradient-to-br from-orange-500 to-orange-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="slide-up">
                    <div class="stat-counter text-white mb-2" data-counter="10">0</div>
                    <p class="text-orange-100 font-medium">Năm kinh nghiệm</p>
                </div>
                <div class="slide-up">
                    <div class="stat-counter text-white mb-2" data-counter="30">0</div>
                    <p class="text-orange-100 font-medium">Phương tiện</p>
                </div>
                <div class="slide-up">
                    <div class="stat-counter text-white mb-2" data-counter="1000">0</div>
                    <p class="text-orange-100 font-medium">Khách hàng tin tưởng</p>
                </div>
                <div class="slide-up">
                    <div class="stat-counter text-white mb-2" data-counter="24">0</div>
                    <p class="text-orange-100 font-medium">Hỗ trợ 24/7</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="slide-up">
                    <div class="inline-flex items-center bg-orange-100 px-4 py-2 rounded-full text-orange-600 text-sm font-semibold mb-6">
                        <i class="fas fa-building me-2"></i>
                        Về chúng tôi
                    </div>
                    <h2 class="text-4xl lg:text-4xl font-black text-slate-800 mb-6">
                        Công Ty TNHH MTV 
                        <span class="gradient-text">Vận Tải Hoàng Phú Long</span>
                    </h2>
                    <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                        Với gần 10 năm kinh nghiệm hoạt động trong lĩnh vực vận tải nội địa, chúng tôi chuyên tuyến từ TP.HCM đi các tỉnh Đông Nam Bộ và ngược lại.
                    </p>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Chúng tôi chuyên vận tải đường bộ với tiêu chí tiết kiệm, nhanh chóng, đảm bảo, mang lại cảm giác an tâm và tin cậy đến với khách hàng.
                    </p>
                    
                    <div class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-2xl p-8 mb-8">
                        <h4 class="text-xl font-bold text-slate-800 mb-4">Cam kết của chúng tôi:</h4>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-500 w-6 h-6 rounded-full flex items-center justify-center mt-1">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span class="text-slate-700">Tư vấn tận tâm, nhiệt tình cho Quý khách hàng</span>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-500 w-6 h-6 rounded-full flex items-center justify-center mt-1">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span class="text-slate-700">Tiết kiệm chi phí nhất, vận chuyển an toàn, không thất thoát hàng hóa</span>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="bg-orange-500 w-6 h-6 rounded-full flex items-center justify-center mt-1">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span class="text-slate-700">Thời gian giao hàng chính xác với công nghệ định vị GPS</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="tel:0917712195" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center space-x-2">
                            <i class="fas fa-phone"></i>
                            <span>Liên hệ ngay</span>
                        </a>
                        <a href="#contact" class="bg-slate-100 hover:bg-slate-200 text-slate-800 px-6 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center space-x-2">
                            <i class="fas fa-envelope"></i>
                            <span>Gửi email</span>
                        </a>
                    </div>
                </div>
                
                <div class="slide-up">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-3xl p-8 text-white overflow-hidden">
                            <!-- Background pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div class="fleet-image w-full h-full rounded-3xl"></div>
                            </div>
                            
                            <div class="relative z-10">
                                <div class="text-center mb-8">
                                    <h3 class="text-2xl font-bold mb-4">Đội xe đa dạng</h3>
                                    <p class="text-orange-100">Phù hợp với mọi nhu cầu vận chuyển</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center hover:bg-white/30 transition-all">
                                        <div class="bg-white/20 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-truck text-2xl"></i>
                                        </div>
                                        <div class="font-bold">Xe tải</div>
                                        <div class="text-sm text-orange-100">Các loại tải trọng</div>
                                    </div>
                                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center hover:bg-white/30 transition-all">
                                        <div class="bg-white/20 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-trailer text-2xl"></i>
                                        </div>
                                        <div class="font-bold">Xe đầu kéo</div>
                                        <div class="text-sm text-orange-100">Container, rơ moóc</div>
                                    </div>
                                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center hover:bg-white/30 transition-all">
                                        <div class="bg-white/20 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-crane text-2xl"></i>
                                        </div>
                                        <div class="font-bold">Xe cẩu</div>
                                        <div class="text-sm text-orange-100">Cẩu hàng nặng</div>
                                    </div>
                                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center hover:bg-white/30 transition-all">
                                        <div class="bg-white/20 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-bus text-2xl"></i>
                                        </div>
                                        <div class="font-bold">Xe du lịch</div>
                                        <div class="text-sm text-orange-100">4-45 chỗ</div>
                                    </div>
                                </div>
                                
                                <!-- Fleet showcase -->
                                <div class="mt-8 pt-6 border-t border-white/20">
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold">30+</div>
                                            <div class="text-orange-100">Phương tiện</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold">GPS</div>
                                            <div class="text-orange-100">Theo dõi</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold">24/7</div>
                                            <div class="text-orange-100">Hỗ trợ</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Gallery Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 slide-up">
                <div class="inline-flex items-center bg-orange-100 px-4 py-2 rounded-full text-orange-600 text-sm font-semibold mb-4">
                    <i class="fas fa-images me-2"></i>
                    Hình ảnh thực tế
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-800 mb-6">
                    Đội Xe & Kho Bãi 
                    <span class="gradient-text">Hoàng Phú Long</span>
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                    Khám phá đội xe hiện đại và hệ thống kho bãi chuyên nghiệp của chúng tôi
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Truck Fleet Image -->
                <div class="slide-up group">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br from-orange-100 to-orange-200">
                        <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-orange-500/20 to-orange-600/20">
                            <div class="fleet-image h-64 w-full transform group-hover:scale-110 transition-transform duration-500"></div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="text-xl font-bold mb-2">Đội xe tải chuyên nghiệp</h3>
                            <p class="text-sm text-gray-200">30+ phương tiện đa dạng từ xe tải đến xe đầu kéo</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                30+ xe
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Warehouse Image -->
                <div class="slide-up group">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br from-blue-100 to-blue-200">
                        <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-blue-500/20 to-blue-600/20">
                            <div class="warehouse-bg h-64 w-full transform group-hover:scale-110 transition-transform duration-500"></div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="text-xl font-bold mb-2">Kho bãi hiện đại</h3>
                            <p class="text-sm text-gray-200">Hệ thống kho bãi rộng rãi, an toàn và bảo mật</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Kho lớn
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Crane Service Image -->
                <div class="slide-up group">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br from-purple-100 to-purple-200">
                        <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-purple-500/20 to-purple-600/20">
                            <div class="h-64 w-full bg-gradient-to-br from-purple-500/30 to-purple-600/30 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-500">
                                <div class="text-center text-purple-700">
                                    <i class="fas fa-crane text-6xl mb-4"></i>
                                    <div class="text-lg font-bold">Dịch vụ cẩu hàng</div>
                                    <div class="text-sm">Chuyên nghiệp & An toàn</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="text-xl font-bold mb-2">Xe cẩu chuyên dụng</h3>
                            <p class="text-sm text-gray-200">Cẩu hàng nặng với tay máy có kinh nghiệm</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="bg-purple-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                An toàn
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Container Transport Image -->
                <div class="slide-up group">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br from-green-100 to-green-200">
                        <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-green-500/20 to-green-600/20">
                            <div class="h-64 w-full bg-gradient-to-br from-green-500/30 to-green-600/30 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-500">
                                <div class="text-center text-green-700">
                                    <i class="fas fa-shipping-fast text-6xl mb-4"></i>
                                    <div class="text-lg font-bold">Vận chuyển Container</div>
                                    <div class="text-sm">Nhanh chóng & Đúng hẹn</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="text-xl font-bold mb-2">Vận chuyển Container</h3>
                            <p class="text-sm text-gray-200">Chuyên chở hàng siêu trường và siêu trọng</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Siêu trọng
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- GPS Tracking Image -->
                <div class="slide-up group">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br from-red-100 to-red-200">
                        <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-red-500/20 to-red-600/20">
                            <div class="h-64 w-full bg-gradient-to-br from-red-500/30 to-red-600/30 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-500">
                                <div class="text-center text-red-700">
                                    <i class="fas fa-satellite-dish text-6xl mb-4"></i>
                                    <div class="text-lg font-bold">Theo dõi GPS</div>
                                    <div class="text-sm">24/7 Online</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="text-xl font-bold mb-2">Công nghệ GPS</h3>
                            <p class="text-sm text-gray-200">Theo dõi hành trình thời gian thực</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                24/7
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Service Image -->
                <div class="slide-up group">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br from-yellow-100 to-yellow-200">
                        <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-yellow-500/20 to-yellow-600/20">
                            <div class="h-64 w-full bg-gradient-to-br from-yellow-500/30 to-yellow-600/30 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-500">
                                <div class="text-center text-yellow-700">
                                    <i class="fas fa-headset text-6xl mb-4"></i>
                                    <div class="text-lg font-bold">Hỗ trợ khách hàng</div>
                                    <div class="text-sm">Tận tâm & Nhiệt tình</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <h3 class="text-xl font-bold mb-2">Hỗ trợ 24/7</h3>
                            <p class="text-sm text-gray-200">Đội ngũ tư vấn chuyên nghiệp</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Tư vấn
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="text-center mt-16 slide-up">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Bạn muốn xem thêm hình ảnh thực tế?</h3>
                    <p class="text-orange-100 mb-6">Liên hệ với chúng tôi để được tham quan trực tiếp đội xe và kho bãi</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="tel:0917712195" class="bg-white text-orange-600 px-6 py-3 rounded-xl font-bold hover:bg-orange-50 transition-colors inline-flex items-center space-x-2">
                            <i class="fas fa-phone"></i>
                            <span>Gọi ngay: 0917 712 195</span>
                        </a>
                        <a href="#contact" class="bg-orange-400 hover:bg-orange-300 text-white px-6 py-3 rounded-xl font-bold transition-colors inline-flex items-center space-x-2">
                            <i class="fas fa-envelope"></i>
                            <span>Gửi yêu cầu</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" class="py-20 bg-gradient-to-br from-slate-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 slide-up">
                <div class="inline-flex items-center bg-orange-100 px-4 py-2 rounded-full text-orange-600 text-sm font-semibold mb-4">
                    <i class="fas fa-phone me-2"></i>
                    Liên hệ ngay
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-800 mb-6">
                    Kết Nối Với 
                    <span class="gradient-text">Hoàng Phú Long</span>
                </h2>
                <p class="text-xl text-slate-600">Chúng tôi sẵn sàng tư vấn và hỗ trợ bạn 24/7</p>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="slide-up">
                    <div class="bg-white rounded-3xl shadow-xl p-8">
                        <h3 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                            <i class="fas fa-envelope text-orange-500 me-3"></i>
                            Gửi tin nhắn cho chúng tôi
                        </h3>
                        <form class="space-y-6" id="contact-form">
                            <div class="grid md:grid-cols-2 gap-4">
                                <input type="text" placeholder="Họ và tên *" class="w-full px-4 py-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-orange-400/50 focus:border-orange-400 font-medium" required>
                                <input type="email" placeholder="Email *" class="w-full px-4 py-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-orange-400/50 focus:border-orange-400 font-medium" required>
                            </div>
                            <input type="tel" placeholder="Số điện thoại *" class="w-full px-4 py-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-orange-400/50 focus:border-orange-400 font-medium" required>
                            <select class="w-full px-4 py-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-orange-400/50 focus:border-orange-400 font-medium" required>
                                <option value="">Dịch vụ quan tâm</option>
                                <option value="van-chuyen-hang-hoa">Vận chuyển hàng hóa</option>
                                <option value="hang-sieu-trong">Hàng siêu trường & siêu trọng</option>
                                <option value="phan-bon">Vận chuyển phân bón</option>
                                <option value="cau-hang">Dịch vụ cẩu hàng</option>
                                <option value="cho-thue-xe">Cho thuê xe du lịch</option>
                                <option value="cho-thue-cau">Cho thuê cần cẩu</option>
                            </select>
                            <textarea placeholder="Nội dung tin nhắn *" rows="5" class="w-full px-4 py-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-orange-400/50 focus:border-orange-400 font-medium resize-none" required></textarea>
                            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="slide-up space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-800 mb-2">Địa chỉ văn phòng</h4>
                                <p class="text-slate-600 leading-relaxed">
                                    Số 216, Tổ 4, Ấp 7, Xã Bình Sơn<br>
                                    Huyện Long Thành, Đồng Nai
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-800 mb-2">Hotline</h4>
                                <div class="space-y-1">
                                    <a href="tel:0917712195" class="block text-slate-600 hover:text-orange-500 font-semibold transition-colors">
                                        <i class="fas fa-phone-alt me-2 text-orange-500"></i>0917 712 195
                                    </a>
                                    <a href="tel:0916712195" class="block text-slate-600 hover:text-orange-500 font-semibold transition-colors">
                                        <i class="fas fa-phone-alt me-2 text-orange-500"></i>0916 712 195
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-gradient-to-br from-green-500 to-green-600 w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-800 mb-2">Email</h4>
                                <a href="mailto:vantaihoangphulong86@gmail.com" class="text-slate-600 hover:text-orange-500 transition-colors break-all">
                                    vantaihoangphulong86@gmail.com
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-800 mb-2">Thời gian làm việc</h4>
                                <div class="text-slate-600 space-y-1">
                                    <div>Thứ 2 - Thứ 6: 8:00 - 18:00</div>
                                    <div>Thứ 7: 8:00 - 12:00</div>
                                    <div>Chủ nhật: Nghỉ</div>
                                    <div class="text-orange-500 font-semibold mt-2">Hỗ trợ khẩn cấp: 24/7</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
                        <div class="text-center">
                            <i class="fas fa-phone-volume text-3xl mb-4"></i>
                            <h4 class="text-xl font-bold mb-2">Gọi ngay để được tư vấn miễn phí!</h4>
                            <p class="text-orange-100 mb-4">Chúng tôi sẵn sàng báo giá và tư vấn 24/7</p>
                            <a href="tel:0917712195" class="bg-white text-orange-600 px-6 py-3 rounded-xl font-bold text-lg hover:bg-orange-50 transition-colors inline-flex items-center space-x-2">
                                <i class="fas fa-phone"></i>
                                <span>0917 712 195</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="bg-gradient-to-br rounded-xl">
                            <img src="http://vantaihoangphulong.vn/logo/2989/logocuoicung.png" alt="" width="180">
                        </div>
                        <div>
                            <div class="text-white text-xl font-bold">Hoàng Phú Long</div>
                            <div class="text-orange-300 text-sm">An Tâm Trên Vạn Dặm</div>
                        </div>
                    </div>
                    <p class="text-slate-300 mb-6 leading-relaxed">
                        Công ty TNHH MTV Vận Tải Hoàng Phú Long - Đối tác tin cậy với gần 10 năm kinh nghiệm trong lĩnh vực vận tải nội địa, chuyên tuyến TP.HCM - Đông Nam Bộ.
                    </p>
                    <p class="text-slate-400 text-sm mb-4">
                        <strong>Giấy phép ĐKKD:</strong> 3603231556<br>
                        <strong>Do:</strong> Sở Kế Hoạch & Đầu Tư Tp. Đồng Nai cấp<br>
                        <strong>Ngày cấp:</strong> 27/11/2014
                    </p>
                </div>
                
                <div>
                    <h5 class="text-lg font-bold mb-6">Dịch vụ chính</h5>
                    <ul class="space-y-3 text-slate-300">
                        <li><a href="#" class="hover:text-orange-400 transition-colors">Vận chuyển hàng hóa</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">Hàng siêu trường & siêu trọng</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">Vận chuyển phân bón</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">Dịch vụ cẩu hàng</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">Cho thuê xe du lịch</a></li>
                        <li><a href="#" class="hover:text-orange-400 transition-colors">Cho thuê cần cẩu</a></li>
                    </ul>
                </div>
                
                <div>
                    <h5 class="text-lg font-bold mb-6">Liên hệ nhanh</h5>
                    <div class="space-y-4 text-slate-300">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-phone text-orange-400"></i>
                            <div>
                                <a href="tel:0917712195" class="hover:text-orange-400 transition-colors block">0917 712 195</a>
                                <a href="tel:0916712195" class="hover:text-orange-400 transition-colors block">0916 712 195</a>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-orange-400 mt-1"></i>
                            <a href="mailto:vantaihoangphulong86@gmail.com" class="hover:text-orange-400 transition-colors break-all">
                                vantaihoangphulong86@gmail.com
                            </a>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-orange-400 mt-1"></i>
                            <span class="text-sm">
                                Số 216, Tổ 4, Ấp 7, Xã Bình Sơn<br>
                                Huyện Long Thành, Đồng Nai
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-slate-400 text-sm mb-4 md:mb-0">
                        © 2025 Công ty TNHH MTV Vận Tải Hoàng Phú Long. Tất cả quyền được bảo lưu.
                    </p>
                    <p class="text-slate-500 text-xs">
                        Chịu trách nhiệm nội dung: Trương Hoàng Long
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Action Buttons -->
    <div class="fixed bottom-6 right-6 z-50 space-y-3">
        <a href="tel:0917712195" class="bg-green-500 hover:bg-green-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all transform hover:scale-110">
            <i class="fas fa-phone text-xl"></i>
        </a>
        <button id="scroll-to-top" class="bg-orange-500 hover:bg-orange-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all transform hover:scale-110 opacity-0 pointer-events-none">
            <i class="fas fa-arrow-up text-xl"></i>
        </button>
    </div>

    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('#mobile-menu-btn').click(function() {
                $('#mobile-menu').slideToggle(300);
                $(this).find('i').toggleClass('fa-bars fa-times');
            });
            
            // Smooth scrolling
            $('a[href^="#"]').click(function(e) {
                e.preventDefault();
                var target = $($(this).attr('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800, 'swing');
                }
                $('#mobile-menu').slideUp(300);
                $('#mobile-menu-btn').find('i').removeClass('fa-times').addClass('fa-bars');
            });
            
            // Navbar scroll effect
            $(window).scroll(function() {
                var scrollTop = $(window).scrollTop();
                
                if (scrollTop > 100) {
                    $('.nav-blur').addClass('scrolled');
                    $('#scroll-to-top').removeClass('opacity-0 pointer-events-none').addClass('opacity-100');
                } else {
                    $('.nav-blur').removeClass('scrolled');
                    $('#scroll-to-top').addClass('opacity-0 pointer-events-none').removeClass('opacity-100');
                }
                
                // Show elements on scroll
                $('.slide-up').each(function() {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();
                    
                    if (elementBottom > viewportTop && elementTop < viewportBottom - 100) {
                        $(this).addClass('visible');
                    }
                });
            });
            
            // Scroll to top
            $('#scroll-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 800, 'swing');
            });
            
            // Counter animation
            function animateCounter() {
                $('[data-counter]').each(function() {
                    var $this = $(this);
                    var countTo = parseInt($this.attr('data-counter'));
                    
                    $({ countNum: 0 }).animate({
                        countNum: countTo
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.countNum) + '+');
                        },
                        complete: function() {
                            $this.text(this.countNum + '+');
                        }
                    });
                });
            }
            
            // Trigger counter animation when statistics section is visible
            var statsAnimated = false;
            $(window).scroll(function() {
                var statsSection = $('.py-20.bg-gradient-to-br.from-orange-500');
                if (statsSection.length && !statsAnimated) {
                    var elementTop = statsSection.offset().top;
                    var elementBottom = elementTop + statsSection.outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();
                    
                    if (elementBottom > viewportTop && elementTop < viewportBottom - 100) {
                        animateCounter();
                        statsAnimated = true;
                    }
                }
            });
            
            // Form validation and submission
            function showNotification(message, type = 'success') {
                var bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                
                $(`<div class="fixed top-20 right-4 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl z-50 opacity-0 transform translate-x-full transition-all duration-300">
                    <div class="flex items-center space-x-3">
                        <i class="fas ${icon}"></i>
                        <span>${message}</span>
                    </div>
                </div>`)
                .appendTo('body')
                .animate({opacity: 1}, 300)
                .css('transform', 'translateX(0)')
                .delay(4000)
                .animate({opacity: 0}, 300, function() {
                    $(this).remove();
                });
            }
            
            function validateEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
            
            function validatePhone(phone) {
                return /^[\d\s\-\+\(\)]{10,}$/.test(phone.replace(/\s/g, ''));
            }
            
            $('#quote-form').submit(function(e) {
                e.preventDefault();
                
                var isValid = true;
                var formData = {};
                
                // Validate required fields
                $(this).find('input[required], select[required]').each(function() {
                    var value = $(this).val().trim();
                    var fieldName = $(this).attr('placeholder') || 'Trường này';
                    
                    $(this).removeClass('border-red-500 ring-red-400');
                    
                    if (!value) {
                        $(this).addClass('border-red-500 ring-red-400');
                        isValid = false;
                    } else {
                        // Email validation
                        if ($(this).attr('type') === 'email' && !validateEmail(value)) {
                            $(this).addClass('border-red-500 ring-red-400');
                            isValid = false;
                        }
                        // Phone validation
                        if ($(this).attr('type') === 'tel' && !validatePhone(value)) {
                            $(this).addClass('border-red-500 ring-red-400');
                            isValid = false;
                        }
                        formData[fieldName] = value;
                    }
                });
                
                if (isValid) {
                    // Simulate form submission
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalText = submitBtn.html();
                    
                    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...').prop('disabled', true);
                    
                    setTimeout(() => {
                        showNotification('Cảm ơn bạn! Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất.', 'success');
                        this.reset();
                        submitBtn.html(originalText).prop('disabled', false);
                    }, 1500);
                } else {
                    showNotification('Vui lòng điền đầy đủ thông tin hợp lệ.', 'error');
                }
            });
            
            $('#contact-form').submit(function(e) {
                e.preventDefault();
                
                var isValid = true;
                
                // Validate required fields
                $(this).find('input[required], select[required], textarea[required]').each(function() {
                    var value = $(this).val().trim();
                    
                    $(this).removeClass('border-red-500 ring-red-400');
                    
                    if (!value) {
                        $(this).addClass('border-red-500 ring-red-400');
                        isValid = false;
                    } else {
                        // Email validation
                        if ($(this).attr('type') === 'email' && !validateEmail(value)) {
                            $(this).addClass('border-red-500 ring-red-400');
                            isValid = false;
                        }
                        // Phone validation
                        if ($(this).attr('type') === 'tel' && !validatePhone(value)) {
                            $(this).addClass('border-red-500 ring-red-400');
                            isValid = false;
                        }
                    }
                });
                
                if (isValid) {
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalText = submitBtn.html();
                    
                    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...').prop('disabled', true);
                    
                    setTimeout(() => {
                        showNotification('Tin nhắn đã được gửi thành công! Chúng tôi sẽ phản hồi sớm nhất.', 'success');
                        this.reset();
                        submitBtn.html(originalText).prop('disabled', false);
                    }, 1500);
                } else {
                    showNotification('Vui lòng điền đầy đủ thông tin hợp lệ.', 'error');
                }
            });
            
            // Remove error styling on input
            $('input, select, textarea').on('input change', function() {
                $(this).removeClass('border-red-500 ring-red-400');
            });
            
            // Parallax effect for hero background
            $(window).scroll(function() {
                var scrolled = $(window).scrollTop();
                var parallax = $('.hero-gradient');
                var speed = scrolled * 0.5;
                parallax.css('background-position', 'center ' + speed + 'px');
            });
            
            // Initialize animations on page load
            setTimeout(function() {
                $('.slide-up').first().addClass('visible');
            }, 300);
            
            // Add loading animation to external links
            $('a[href^="tel:"], a[href^="mailto:"]').click(function() {
                var $this = $(this);
                var originalHtml = $this.html();
                
                $this.html('<i class="fas fa-spinner fa-spin"></i>').addClass('pointer-events-none');
                
                setTimeout(() => {
                    $this.html(originalHtml).removeClass('pointer-events-none');
                }, 1000);
            });
            
            // Service cards hover effect enhancement
            $('.service-card').hover(
                function() {
                    $(this).find('i').addClass('animate-pulse');
                },
                function() {
                    $(this).find('i').removeClass('animate-pulse');
                }
            );
            
            // Auto-hide mobile menu on resize
            $(window).resize(function() {
                if ($(window).width() > 1024) {
                    $('#mobile-menu').hide();
                    $('#mobile-menu-btn').find('i').removeClass('fa-times').addClass('fa-bars');
                }
            });
        });
    </script>
</body>
</html>