<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TransCargo - Dịch Vụ Vận Tải Chuyên Nghiệp</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        /* Custom styles */
        html {
            scroll-behavior: smooth;
        }
        .hero-section {
            background-image: linear-gradient(rgb(0 0 0 / 20%), rgb(0 0 0 / 34%)), url('http://vantaihoangphulong.vn/slider/2989/slide2f.jpg');
            background-size: cover;
            background-position: center;
            min-height: 80vh;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .service-card {
            transition: all 0.3s ease;
        }
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #FCD34D;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .cta-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('/api/placeholder/1920/800');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .counter-box {
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .counter-box:hover > p {
            color: #1F2937 !important;
        }
        .counter-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #FCD34D;
            z-index: -1;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.5s ease;
        }
        .counter-box:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        .counter-box:hover {
            color: #1F2937;
        }
        .counter-box:hover .counter-icon {
            color: #1F2937;
        }
        /* Mobile menu */
        .mobile-menu {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="font-sans text-gray-800 bg-gray-50">
    <!-- Header & Navigation -->
    <header class="bg-gray-900 text-white sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <!-- <img src="/api/placeholder/60/60" alt="TransCargo Logo" class="h-12 w-auto"> -->
                    <img src="http://vantaihoangphulong.vn/logo/2989/logocuoicung.png" alt="Logo Vận Tải Hoàng Phú Long" class="h-16 w-auto">
                    <div class="ml-3">
                        <h1 class="text-x font-bold text-yellow-300">VẬN TẢI HOÀNG PHÚ LONG</h1>
                        <p class="text-xs text-gray-400">An Tâm Trên Vạn Dặm</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#home" class="nav-link text-white hover:text-yellow-300 py-2">TRANG CHỦ</a>
                    <a href="#about" class="nav-link text-white hover:text-yellow-300 py-2">GIỚI THIỆU</a>
                    <a href="#services" class="nav-link text-white hover:text-yellow-300 py-2">DỊCH VỤ VẬN TẢI</a>
                    <a href="#rental" class="nav-link text-white hover:text-yellow-300 py-2">CHO THUÊ XE</a>
                    <a href="#pricing" class="nav-link text-white hover:text-yellow-300 py-2">BẢNG GIÁ</a>
                    <a href="#contact" class="nav-link text-white hover:text-yellow-300 py-2">LIÊN HỆ</a>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden" x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-gray-800">
                <a href="#home" @click="mobileMenuOpen = false" class="block px-3 py-2 text-white hover:bg-gray-700 rounded">TRANG CHỦ</a>
                <a href="#about" @click="mobileMenuOpen = false" class="block px-3 py-2 text-white hover:bg-gray-700 rounded">GIỚI THIỆU</a>
                <a href="#services" @click="mobileMenuOpen = false" class="block px-3 py-2 text-white hover:bg-gray-700 rounded">DỊCH VỤ VẬN TẢI</a>
                <a href="#rental" @click="mobileMenuOpen = false" class="block px-3 py-2 text-white hover:bg-gray-700 rounded">CHO THUÊ XE</a>
                <a href="#pricing" @click="mobileMenuOpen = false" class="block px-3 py-2 text-white hover:bg-gray-700 rounded">BẢNG GIÁ</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="block px-3 py-2 text-white hover:bg-gray-700 rounded">LIÊN HỆ</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero-section flex items-center justify-center text-white">
        <div class="container mx-auto px-4 py-16 text-left" data-aos="fade-up">
            <h1 class="text-4xl md:text-5x font-bold mb-2 leading-tight">Giải Pháp Vận Tải <span class="text-yellow-300">Toàn Diện</span> Cho Doanh Nghiệp</h1>
            <p class="text-x md:text-3x mb-10 mx-auto">Đối tác đáng tin cậy mang đến dịch vụ vận tải an toàn, nhanh chóng và hiệu quả</p>
            <div class="flex flex-col sm:flex-row justify-start gap-4">
                <a href="#services" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300 transform hover:-translate-y-1">
                    Dịch Vụ Của Chúng Tôi
                </a>
                <a href="#contact" class="bg-transparent hover:bg-white text-white hover:text-gray-900 font-bold py-3 px-8 rounded-lg border-2 border-white transition duration-300 transform hover:-translate-y-1">
                    Liên Hệ Ngay
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Counter Section -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                <div class="counter-box bg-gray-800 text-white p-6 shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="counter-icon text-yellow-400 text-4xl mb-4">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2">150+</h3>
                    <p class="text-gray-300">Phương Tiện Vận Tải</p>
                </div>
                <div class="counter-box bg-gray-800 text-white p-6 shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="counter-icon text-yellow-400 text-4xl mb-4">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2">63</h3>
                    <p class="text-gray-300">Tỉnh Thành Phủ Sóng</p>
                </div>
                <div class="counter-box bg-gray-800 text-white p-6 shadow-lg" data-aos="fade-up" data-aos-delay="300">
                    <div class="counter-icon text-yellow-400 text-4xl mb-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2">1000+</h3>
                    <p class="text-gray-300">Khách Hàng Hài Lòng</p>
                </div>
                <div class="counter-box bg-gray-800 text-white p-6 shadow-lg" data-aos="fade-up" data-aos-delay="400">
                    <div class="counter-icon text-yellow-400 text-4xl mb-4">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-2">10</h3>
                    <p class="text-gray-300">Năm Kinh Nghiệm</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Về Chúng Tôi</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-3xl mx-auto">Đối tác vận tải đáng tin cậy với hơn 10 năm kinh nghiệm, cam kết mang đến dịch vụ chất lượng hàng đầu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <img src="http://vantaihoangphulong.vn/slider/2989/slide.jpg" alt="Về Công Ty Vận Tải" class="rounded-lg shadow-xl w-full">
                </div>
                <div data-aos="fade-left">
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Giải Pháp Vận Chuyển Toàn Diện</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">VẬN TẢI HOÀNG PHÚ LONG tự hào là đối tác vận tải đáng tin cậy của hàng nghìn doanh nghiệp trên khắp Việt Nam. Với đội ngũ nhân viên chuyên nghiệp, hệ thống phương tiện hiện đại và quy trình làm việc khoa học, chúng tôi cung cấp các giải pháp vận tải đáp ứng mọi nhu cầu của khách hàng.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="flex items-start">
                            <div class="text-yellow-500 mr-3">
                                <i class="fas fa-check-circle text-x"></i>
                            </div>
                            <div>
                                <h4 class="font-bold mb-1">An Toàn</h4>
                                <p class="text-gray-600 text-sm">Hệ thống giám sát và quy trình an toàn chặt chẽ</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="text-yellow-500 mr-3">
                                <i class="fas fa-check-circle text-x"></i>
                            </div>
                            <div>
                                <h4 class="font-bold mb-1">Tin Cậy</h4>
                                <p class="text-gray-600 text-sm">Luôn đúng hẹn với cam kết thời gian giao hàng</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="text-yellow-500 mr-3">
                                <i class="fas fa-check-circle text-x"></i>
                            </div>
                            <div>
                                <h4 class="font-bold mb-1">Hiện Đại</h4>
                                <p class="text-gray-600 text-sm">Đội xe mới, hiện đại và được bảo dưỡng thường xuyên</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="text-yellow-500 mr-3">
                                <i class="fas fa-check-circle text-x"></i>
                            </div>
                            <div>
                                <h4 class="font-bold mb-1">Chuyên Nghiệp</h4>
                                <p class="text-gray-600 text-sm">Đội ngũ nhân viên được đào tạo chuyên sâu</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#contact" class="inline-block bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-300">
                        Tìm Hiểu Thêm <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Dịch Vụ Vận Tải</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-3xl mx-auto">Đa dạng dịch vụ vận tải đáp ứng mọi nhu cầu của doanh nghiệp, từ hàng hóa nhỏ lẻ đến container</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service Card 1 -->
                <div class="service-card bg-gray-50 rounded-lg shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <img src="http://vantaihoangphulong.vn/dichvu_images/2989/van%20chuyen%20hang%20hoa%20(2).jpg" alt="Vận Chuyển Hàng Hóa" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center mb-4 -mt-14 mx-auto shadow-lg">
                            <i class="fas fa-truck-container text-gray-800 text-2xl"></i>
                        </div>
                        <h3 class="text-x font-bold mb-3 text-center">Vận Chuyển Hàng Hóa</h3>
                        <p class="text-gray-600 mb-4 text-center">Chúng tôi chuyên vận tải đường bộ với kiểu chí tiết kiệm, nhanh chóng, đảm bảo, mang lại cảm giác an tâm và tin cậy đến với khách hàng.</p>
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <ul class="text-gray-600">
                                <li class="flex items-center mb-2">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Container 20ft, 40ft</span>
                                </li>
                                <li class="flex items-center mb-2">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Vận chuyển liên tỉnh</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Theo dõi hàng hóa trực tuyến</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Service Card 2 -->
                <div class="service-card bg-gray-50 rounded-lg shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <img src="http://vantaihoangphulong.vn/dichvu_images/2989/van%20chuyen%20hang%20sieu%20truong%20(5).jpg" alt="Vận Chuyển Hàng Siêu Trường Siêu Trọng" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center mb-4 -mt-14 mx-auto shadow-lg">
                            <i class="fas fa-boxes text-gray-800 text-2xl"></i>
                        </div>
                        <h3 class="text-x font-bold mb-3 text-center">Vận Chuyển Hàng Siêu Trường Siêu Trọng</h3>
                        <p class="text-gray-600 mb-4 text-center">Hoàng Phú Long sẽ mang lại cảm giác an tâm và tin cậy đến với khách hàng. Trải nghiệm dịch vụ vận chuyển hàng chuyên nghiệp từ TPHCM.</p>
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <ul class="text-gray-600">
                                <li class="flex items-center mb-2">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Vận chuyển hàng hóa các loại</span>
                                </li>
                                <li class="flex items-center mb-2">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Dịch vụ door-to-door</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Đóng gói, bảo quản chuyên nghiệp</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Service Card 3 -->
                <div class="service-card bg-gray-50 rounded-lg shadow-lg overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <img src="http://vantaihoangphulong.vn/dichvu_images/2989/van%20chuyen%20hang%20sieu%20truong%20(9).jpg" alt="Vận Chuyển Cống, Cọc Bê Tông" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center mb-4 -mt-14 mx-auto shadow-lg">
                            <i class="fas fa-warehouse text-gray-800 text-2xl"></i>
                        </div>
                        <h3 class="text-x font-bold mb-3 text-center">Vận Chuyển Cống, Cọc Bê Tông</h3>
                        <p class="text-gray-600 mb-4 text-center">Với nhiều năm kinh nghiệm vận tải, chúng tôi đảm bảo rằng quý khách sẽ tiết kiệm được chi phí, vận chuyển an toàn, không thất thoát hàng.</p>
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <ul class="text-gray-600">
                                <li class="flex items-center mb-2">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Quản lý kho hàng</span>
                                </li>
                                <li class="flex items-center mb-2">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Dịch vụ phân phối</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-yellow-500 mr-2"></i>
                                    <span>Hệ thống quản lý chuỗi cung ứng</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#contact" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300">
                    Tư Vấn Dịch Vụ <i class="fas fa-headset ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Rental Section -->
    <section id="rental" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Dịch Vụ Cho Thuê Xe</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-3xl mx-auto">Đa dạng phương tiện cho thuê theo giờ, ngày, tháng với giá cả cạnh tranh và chất lượng hàng đầu</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Vehicle Card 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:shadow-xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                    <img src="http://vantaihoangphulong.vn/dichvu_images/2989/cho%20thue%20xe%20nang%20(5).jpg" alt="Cho Thuê Xe Cẩu" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Cho Thuê Xe Cẩu</h3>
                        <p class="text-gray-600 mb-4">Xe cẩu vốn là loại xe cần đầu tư nhiều, ngàn sách mới có thể sở hữu, vì vậy nếu quý khách thuê ngoài thì sẽ tiết kiệm chi phí hơn.</p>
                        <div class="flex items-center justify-between">
                            <div class="text-yellow-500 font-bold">
                                Từ 800.000đ/ngày
                            </div>
                            <a href="#contact" class="text-gray-800 hover:text-yellow-500 font-medium">
                                Chi tiết <i class="fas fa-angle-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Card 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:shadow-xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                    <img src="http://vantaihoangphulong.vn/dichvu_images/2989/cho%20thue%20xe%20nang%20(3).jpg" alt="Cho Thuê Cần Cẩu" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Cho Thuê Cần Cẩu</h3>
                        <p class="text-gray-600 mb-4">Ngoài dịch vụ vận chuyển hàng hóa, cẩu hàng và nâng hạ, chúng tôi còn cho thuê cần cẩu có mức với chi phí cạnh tranh nhất.</p>
                        <div class="flex items-center justify-between">
                            <div class="text-yellow-500 font-bold">
                                Từ 1.500.000đ/ngày
                            </div>
                            <a href="#contact" class="text-gray-800 hover:text-yellow-500 font-medium">
                                Chi tiết <i class="fas fa-angle-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Card 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:shadow-xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                    <img src="http://vantaihoangphulong.vn/dichvu_images/2989/9065933da6a272fc2bb3.jpg" alt="Cho Thuê Xe Nâng" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Cho Thuê Xe Nâng</h3>
                        <p class="text-gray-600 mb-4">Quý khách nếu có bất cứ vấn đề gì cần giải đáp, hãy gọi trực tiếp cho chúng tôi, Hoàng Phú Long sẽ tư vấn tận tình và nhiệt tình.</p>
                        <div class="flex items-center justify-between">
                            <div class="text-yellow-500 font-bold">
                                Từ 2.500.000đ/ngày
                            </div>
                            <a href="#contact" class="text-gray-800 hover:text-yellow-500 font-medium">
                                Chi tiết <i class="fas fa-angle-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Vehicle Card 4 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:shadow-xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="400">
                    <img src="http://vantaihoangphulong.vn/dichvu_images/2989/hoang%20phu%20long%20(15).jpg" alt="Cho Thuê Xe Du Lịch" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Cho Thuê Xe Du Lịch</h3>
                        <p class="text-gray-600 mb-4">Hoàng Phú Long là một trong những thương hiệu cho thuê xe du lịch 4, 7, 16, 29, 45 chỗ uy tín, chất lượng và có quy mô lớn tại Đồng Nai.</p>
                        <div class="flex items-center justify-between">
                            <div class="text-yellow-500 font-bold">
                                Từ 2.800.000đ/ngày
                            </div>
                            <a href="#contact" class="text-gray-800 hover:text-yellow-500 font-medium">
                                Chi tiết <i class="fas fa-angle-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Bảng Giá Dịch Vụ</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-3xl mx-auto">Giá cả cạnh tranh với nhiều gói dịch vụ phù hợp mọi nhu cầu và ngân sách</p>
            </div>

            <div class="text-center mt-10" data-aos="fade-up">
                <a href="#contact" class="inline-block bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg shadow transition duration-300">
                    Yêu Cầu Báo Giá <i class="fas fa-file-invoice-dollar ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-16 text-white">
        <div class="container mx-auto px-4 text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">VÌ SAO KHÁCH HÀNG TIN TƯỞNG CHÚNG TÔI?</h2>
            <p class="text-x mb-10 max-w-3xl mx-auto">Hơn 10 năm kinh nghiệm trong ngành vận tải, hãy để chúng tôi trở thành đối tác vận tải đáng tin cậy và giúp doanh nghiệp của bạn tối ưu chi phí logistics</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-10 gap-x-6">
                <!-- Advantage 1 -->
                <div class="flex items-start">
                    <div class="p-3 mr-2">
                        <img class="whyme_img" src="http://vantaihoangphulong.vn/whyme_images/2989/v%C3%83%C2%AC%20sao-01.png">
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold mb-2">Vận Chuyển Đa Dạng</h3>
                        <p class="text-primary-100">Chúng tôi không ngại bất cứ sản phẩm gì, kể cả trong tải nặng</p>
                    </div>
                </div>

                <!-- Advantage 2 -->
                <div class="flex items-start">
                    <div class="p-3 mr-2">
                        <img class="whyme_img" src="http://vantaihoangphulong.vn/whyme_images/2989/v%C3%83%C2%AC%20sao-02.png">
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold mb-2">Cập Nhật Liên Tục</h3>
                        <p class="text-primary-100">Áp dụng công nghệ định vị GPS mới để quý khách an tâm theo dõi</p>
                    </div>
                </div>

                <!-- Advantage 3 -->
                <div class="flex items-start">
                    <div class="p-3 mr-2">
                        <img class="whyme_img" src="http://vantaihoangphulong.vn/whyme_images/2989/v%C3%83%C2%AC%20sao-03.png">
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold mb-2">Nhanh Chóng, Chính Xác</h3>
                        <p class="text-primary-100">Vận chuyển nhanh chóng đến đúng địa điểm, không thất thoát hàng</p>
                    </div>
                </div>

                <!-- Advantage 4 -->
                <div class="flex items-start">
                    <div class="p-3 mr-2">
                        <img class="whyme_img" src="http://vantaihoangphulong.vn/whyme_images/2989/v%C3%83%C2%AC%20sao-04.png">
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold mb-2">Đội Ngũ Chuyên Nghiệp</h3>
                        <p class="text-primary-100">Đội ngũ tài xế có nhiều năm kinh nghiệm, đội xe gồm 30 xe đủ loại</p>
                    </div>
                </div>

                <!-- Advantage 5 -->
                <div class="flex items-start">
                    <div class="p-3 mr-2">
                        <img class="whyme_img" src="http://vantaihoangphulong.vn/whyme_images/2989/v%C3%83%C2%AC%20sao-05.png">
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold mb-2">Tư Vấn Tận Tâm</h3>
                        <p class="text-primary-100">Hoàng Phú Long luôn hỗ trợ nhiệt tình và tận tâm khi quý khách cần</p>
                    </div>
                </div>

                <!-- Advantage 6 -->
                <div class="flex items-start">
                    <div class="p-3 mr-2">
                        <img class="whyme_img" src="http://vantaihoangphulong.vn/whyme_images/2989/v%C3%83%C2%AC%20sao-06.png">
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-semibold mb-2">Tiết Kiệm Chi Phí</h3>
                        <p class="text-primary-100">Với kinh nghiệm 10 năm, chúng tôi cung cấp giá cả cạnh tranh nhất</p>
                    </div>
                </div>
            </div>
            {{-- <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#contact" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300">
                    Liên Hệ Ngay
                </a>
                <a href="tel:+84901234567" class="bg-transparent hover:bg-white text-white hover:text-gray-900 font-bold py-3 px-8 rounded-lg border-2 border-white transition duration-300">
                    <i class="fas fa-phone-alt mr-2"></i> 0901 234 567
                </a>
            </div> --}}
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Liên Hệ Với Chúng Tôi</h2>
                <div class="w-20 h-1 bg-yellow-400 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-3xl mx-auto">Hãy liên hệ với chúng tôi ngay hôm nay để được tư vấn và báo giá chi tiết</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-lg p-8" data-aos="fade-right">
                    <h3 class="text-2xl font-bold mb-6">Gửi Yêu Cầu</h3>
                    <form>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 mb-2" for="name">Họ và tên</label>
                                <input type="text" id="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2" for="phone">Số điện thoại</label>
                                <input type="tel" id="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="email">Email</label>
                            <input type="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="service">Dịch vụ quan tâm</label>
                            <select id="service" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Chọn dịch vụ</option>
                                <option value="container">Vận tải container</option>
                                <option value="logistics">Logistics toàn diện</option>
                                <option value="rental">Cho thuê xe</option>
                                <option value="other">Dịch vụ khác</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 mb-2" for="message">Nội dung</label>
                            <textarea id="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 px-4 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold rounded-lg transition duration-300">
                            Gửi Yêu Cầu
                        </button>
                    </form>
                </div>
                
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-2xl font-bold mb-6">Địa chỉ của chúng tôi</h3>
                    
                    <div class="mb-4 h-64 bg-gray-200 rounded-lg">
                        <!-- Replace with actual map -->
                        <!-- <img src="/api/placeholder/800/300" alt="Map" class="w-full h-full object-cover rounded-lg"> -->
                        <iframe height="256" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.8416193036705!2d107.0597019400079!3d10.82342985836832!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31751d995026b865%3A0x318e8332bf0d4913!2zVuG6rW4gdOG6o2kgSG_DoG5nIFBow7ogTG9uZw!5e0!3m2!1svi!2s!4v1696474130997!5m2!1svi!2s" style="border:0" width="100%"></iframe>
                    </div>
                    <div>
                        <h4 class="font-medium text-lg mb-2">CÔNG TY TNHH MTV VẬN TẢI HOÀNG PHÚ LONG</h4>
                        <p class="text-gray-700 mb-1"><i class="fas fa-map-marker-alt text-primary-600 mr-2"></i> Địa chỉ: Số 216, Tổ 4, Ấp 7, Xã Bình Sơn, Huyện Long Thành, Đồng Nai</p>
                        <p class="text-gray-700 mb-1"><i class="fas fa-phone-alt text-primary-600 mr-2"></i> Hotline: 0917 712 195 - 0916 712 195</p>
                        <p class="text-gray-700 mb-1"><i class="fas fa-envelope text-primary-600 mr-2"></i> Email: vantaihoangphulong86@gmail.com</p>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="bg-white rounded-lg mt-5">
                        <h4 class="font-bold">CẦN TƯ VẤN? HÃY LIÊN HỆ VỚI CHÚNG TÔI NGAY!</h4>
                        <p>Chúng tôi sẵn sàng hỗ trợ quý khách 24/7</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Contact Card 1 -->
                            <div class="text-center">
                                <div class="p-6">
                                    <h3 class="font-semibold text-lg mb-2">Hotline 1</h3>
                                    <p class="text-gray-700 text-x font-bold mb-4">0917 712 195</p>
                                    <div class="flex justify-center space-x-2">
                                        <a href="tel:+84916712195" class="">
                                            <img title="CHAT ZALO: 0916 712 195" style="width:38px; height: 38px;" src="http://vantaihoangphulong.vn/images/zalo_con.png" alt="zalo icon">
                                        </a>
                                        <a href="#" class="">
                                            <img title="Email: vantaihoangphulong86@gmail.com" style="width:38px; height: 38px;" src="http://vantaihoangphulong.vn/images/email_icon.png" alt="Email icon">
                                        </a>
                                    </div>
                                </div>
                            </div>
                
                            <!-- Contact Card 2 -->
                            <div class="text-center">
                                <div class="p-6">
                                    <h3 class="font-semibold text-lg mb-2">Hotline 2</h3>
                                    <p class="text-gray-700 text-x font-bold mb-4">0916 712 195</p>
                                    <div class="flex justify-center space-x-2">
                                        <a href="tel:+84916712195" class="">
                                            <img title="CHAT ZALO: 0916 712 195" style="width:38px; height: 38px;" src="http://vantaihoangphulong.vn/images/zalo_con.png" alt="zalo icon">
                                        </a>
                                        <a href="#" class="">
                                            <img title="Email: vantaihoangphulong86@gmail.com" style="width:38px; height: 38px;" src="http://vantaihoangphulong.vn/images/email_icon.png" alt="Email icon">
                                        </a>
                                    </div>
                                </div>
                            </div>
                
                            <!-- Contact Card 3 -->
                            <div class="text-center">
                                <div class="p-6">
                                    <h3 class="font-semibold text-lg mb-2">Hotline 3</h3>
                                    <p class="text-gray-700 text-x font-bold mb-4">0856 712 195</p>
                                    <div class="flex justify-center space-x-2">
                                        <a href="tel:+84916712195" class="">
                                            <img title="CHAT ZALO: 0916 712 195" style="width:38px; height: 38px;" src="http://vantaihoangphulong.vn/images/zalo_con.png" alt="zalo icon">
                                        </a>
                                        <a href="#" class="">
                                            <img title="Email: vantaihoangphulong86@gmail.com" style="width:38px; height: 38px;" src="http://vantaihoangphulong.vn/images/email_icon.png" alt="Email icon">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="http://vantaihoangphulong.vn/logo/2989/logocuoicung.png" alt="VẬN TẢI HOÀNG PHÚ LONG" class="h-12 w-auto">
                        <div class="ml-3">
                            <h3 class="text-x font-bold text-yellow-300">VẬN TẢI HOÀNG PHÚ LONG</h3>
                            <p class="text-xs text-gray-400">An Tâm Trên Vạn Dặm</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-4">VẬN TẢI HOÀNG PHÚ LONG tự hào cung cấp dịch vụ vận tải và logistics chất lượng cao, đáp ứng mọi nhu cầu của khách hàng.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Dịch Vụ</h4>
                    <ul class="space-y-2">
                        <li><a href="#services" class="text-gray-400 hover:text-yellow-300">Vận Tải Container</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-yellow-300">Vận Tải Hàng Hóa</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-yellow-300">Logistics Toàn Diện</a></li>
                        <li><a href="#rental" class="text-gray-400 hover:text-yellow-300">Cho Thuê Xe</a></li>
                        <li><a href="#pricing" class="text-gray-400 hover:text-yellow-300">Bảng Giá</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Liên Kết</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-yellow-300">Trang Chủ</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-yellow-300">Giới Thiệu</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-yellow-300">Liên Hệ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-yellow-300">Tin Tức</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-yellow-300">Tuyển Dụng</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-4">Liên Hệ</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-yellow-300"></i>
                            <span class="text-gray-400">Địa chỉ: Số 216, Tổ 4, Ấp 7, Xã Bình Sơn, Huyện Long Thành, Đồng Nai</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-2 text-yellow-300"></i>
                            <span class="text-gray-400"> 0917 712 195 - 0916 712 195</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2 text-yellow-300"></i>
                            <span class="text-gray-400">vantaihoangphulong86@gmail.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock mt-1 mr-2 text-yellow-300"></i>
                            <span class="text-gray-400">Thứ 2 - Thứ 7: 8:00 - 17:30</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2025 VẬN TẢI HOÀNG PHÚ LONG. Tất cả quyền được bảo lưu.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-yellow-300 text-sm">Điều khoản sử dụng</a>
                        <a href="#" class="text-gray-400 hover:text-yellow-300 text-sm">Chính sách bảo mật</a>
                        <a href="#" class="text-gray-400 hover:text-yellow-300 text-sm">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#home" class="fixed bottom-6 right-6 bg-yellow-400 hover:bg-yellow-500 text-gray-900 w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition duration-300 z-50">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Initialize AOS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
        });
    </script>
</body>
</html>