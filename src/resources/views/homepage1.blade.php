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
                rgba(15, 23, 42, 0.95) 0%, 
                rgba(30, 41, 59, 0.9) 35%, 
                rgba(51, 65, 85, 0.85) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="%23ffffff" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="1200" height="800" fill="%23334155"/><rect width="1200" height="800" fill="url(%23grid)"/><path d="M0,400 Q300,350 600,400 T1200,400 L1200,800 L0,800 Z" fill="%23475569" opacity="0.3"/><circle cx="900" cy="200" r="80" fill="%23f59e0b" opacity="0.2"/><circle cx="200" cy="600" r="60" fill="%2306b6d4" opacity="0.2"/></svg>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-2 rounded-xl truck-icon">
                        <i class="fas fa-truck text-white text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-white text-xl font-bold">Hoàng Phú Long</div>
                        <div class="text-orange-300 text-xs font-medium">An Tâm Trên Vạn Dặm</div>
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
                    <h1 class="text-5xl lg:text-7xl font-black mb-6 leading-tight">
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
                <div class="service-card rounded-2xl shadow-lg p-8 slide-up group">
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-truck"></i>
                    </div>
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
                
                <div class="service-card rounded-2xl shadow-lg p-8 slide-up group">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-industry"></i>
                    </div>
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
                
                <div class="service-card rounded-2xl shadow-lg p-8 slide-up group">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-seedling"></i>
                    </div>
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
                
                <div class="service-card rounded-2xl shadow-lg p-8 slide-up group">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-crane"></i>
                    </div>
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
                
                <div class="service-card rounded-2xl shadow-lg p-8 slide-up group">
                    <div class="bg-gradient-to-br from-red-500 to-red-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-car"></i>
                    </div>
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
                
                <div class="service-card rounded-2xl shadow-lg p-8 slide-up group">
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tools"></i>
                    </div>
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
                    <h2 class="text-4xl lg:text-5xl font-black text-slate-800 mb-6">
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
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-3xl p-8 text-white">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold mb-4">Đội xe đa dạng</h3>
                                <p class="text-orange-100">Phù hợp với mọi nhu cầu vận chuyển</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                                    <i class="fas fa-truck text-3xl mb-3"></i>
                                    <div class="font-bold">Xe tải</div>
                                    <div class="text-sm text-orange-100">Các loại tải trọng</div>
                                </div>
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                                    <i class="fas fa-trailer text-3xl mb-3"></i>
                                    <div class="font-bold">Xe đầu kéo</div>
                                    <div class="text-sm text-orange-100">Container, rơ moóc</div>
                                </div>
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                                    <i class="fas fa-crane text-3xl mb-3"></i>
                                    <div class="font-bold">Xe cẩu</div>
                                    <div class="text-sm text-orange-100">Cẩu hàng nặng</div>
                                </div>
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                                    <i class="fas fa-bus text-3xl mb-3"></i>
                                    <div class="font-bold">Xe du lịch</div>
                                    <div class="text-sm text-orange-100">4-45 chỗ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
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
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-2 rounded-xl">
                            <i class="fas fa-truck text-white text-2xl"></i>
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