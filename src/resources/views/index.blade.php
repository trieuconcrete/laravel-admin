<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nguyen Trieu - IT Professional Portfolio</title>
    
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
                <p class="text-gray-600 mb-4" data-vi="Web Developer" data-en="Web Developer">Web Developer</p>
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
                        <div class="text-2xl font-bold text-blue-600">5+</div>
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
        <div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
            
            <!-- About Section -->
            <section id="about" class="post-card bg-white rounded-xl shadow-sm p-6 fade-in">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold" data-vi="Giới thiệu về tôi" data-en="About Me">Giới thiệu về tôi</h3>
                        <p class="text-sm text-gray-600" data-vi="Cập nhật lúc 10:30" data-en="Updated at 10:30">Cập nhật lúc 10:30</p>
                    </div>
                </div>
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed" data-vi="Xin chào! Tôi là Nguyen Trieu, một Web Developer với đam mê xây dựng các giải pháp công nghệ sáng tạo và hiệu quả. Với hơn 8 năm kinh nghiệm trong lĩnh vực phát triển phần mềm, tôi đã tham gia và dẫn dắt nhiều dự án từ ý tưởng đến sản phẩm hoàn thiện." data-en="Hello! I'm Nguyen Trieu, a Web Developer passionate about building innovative and efficient technology solutions. With over 8 years of experience in software development, I have participated in and led many projects from concept to finished product.">
                        Xin chào! Tôi là Nguyen Trieu, một Web Developer với đam mê xây dựng các giải pháp công nghệ sáng tạo và hiệu quả. 
                        Với hơn 8 năm kinh nghiệm trong lĩnh vực phát triển phần mềm, tôi đã tham gia nhiều dự án từ ý tưởng đến sản phẩm hoàn thiện.
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4" data-vi="Tôi luôn tìm kiếm cơ hội để học hỏi công nghệ mới và áp dụng chúng vào thực tế. Hiện tại, tôi đang tập trung vào việc xây dựng các ứng dụng web quy mô lớn với hiệu suất cao và trải nghiệm người dùng tối ưu." data-en="I am always looking for opportunities to learn new technologies and apply them in practice. Currently, I am focusing on building large-scale web applications with high performance and optimal user experience.">
                        Tôi luôn tìm kiếm cơ hội để học hỏi công nghệ mới và áp dụng chúng vào thực tế. 
                        Hiện tại, tôi đang tập trung vào việc xây dựng các ứng dụng web quy mô lớn với hiệu suất cao và trải nghiệm người dùng tối ưu.
                    </p>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-600 hover:text-blue-600 transition-colors">
                            <i class="far fa-heart mr-1"></i> 42
                        </button>
                        <button class="text-gray-600 hover:text-blue-600 transition-colors">
                            <i class="far fa-comment mr-1"></i> 12
                        </button>
                    </div>
                    <button class="text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="far fa-share-square"></i>
                    </button>
                </div>
            </section>
            
            <!-- Skills Section -->
            <section id="skills" class="post-card bg-white rounded-xl shadow-sm p-6 fade-in">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-3">
                        <i class="fas fa-code text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold" data-vi="Kỹ năng chuyên môn" data-en="Professional Skills">Kỹ năng chuyên môn</h3>
                        <p class="text-sm text-gray-600" data-vi="Kỹ năng & Công nghệ" data-en="Skills & Technologies">Skills & Technologies</p>
                    </div>
                </div>
                
                <!-- Main Skills -->
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Backend Development</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-tag px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">PHP</span>
                        <span class="skill-tag px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Laravel</span>
                        <span class="skill-tag px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Python</span>
                        <span class="skill-tag px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Django</span>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Frontend Development</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-tag px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium">HTML5</span>
                        <span class="skill-tag px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">CSS3</span>
                        <span class="skill-tag px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">Bootstrap</span>
                        <span class="skill-tag px-3 py-1 bg-cyan-100 text-cyan-700 rounded-full text-sm font-medium">Tailwind CSS</span>
                        <span class="skill-tag px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">JavaScript</span>
                        <span class="skill-tag px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">jQuery</span>
                        <span class="skill-tag px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                            <i class="fab fa-vuejs mr-1"></i>NuxtJS
                        </span>
                        <span class="skill-tag px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm font-medium">
                            <i class="fab fa-react mr-1"></i>NextJS
                        </span>
                    </div>
                </div>

                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">CMS & E-Commerce</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-tag px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                            <i class="fab fa-wordpress mr-1"></i>WordPress
                        </span>
                        <span class="skill-tag px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                            <i class="fas fa-shopping-cart mr-1"></i>EC-CUBE
                        </span>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Cloud & DevOps</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-tag px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                            <i class="fab fa-aws mr-1"></i>AWS Certified SAA-C03
                        </span>
                        <span class="skill-tag px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-medium">
                            <i class="fab fa-aws mr-1"></i>AWS Certified DVA-C02 (Learning)
                        </span>
                        <span class="skill-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">Server Management</span>
                        <span class="skill-tag px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">DevOps</span>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">AI Agents & Tools</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="skill-tag px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
                            <i class="fas fa-robot mr-1"></i>ChatGPT
                        </span>
                        <span class="skill-tag px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                            <i class="fas fa-brain mr-1"></i>Claude
                        </span>
                        <span class="skill-tag px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                            <i class="fas fa-wind mr-1"></i>Windsurf
                        </span>
                        <span class="skill-tag px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm font-medium">
                            <i class="fas fa-code-branch mr-1"></i>GitHub Copilot
                        </span>
                        <span class="skill-tag px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-sm font-medium">
                            <i class="fas fa-magic mr-1"></i>Prompt Engineering
                        </span>
                    </div>
                </div>
            </section>
            
            <!-- Experience Section -->
            <section id="experience" class="post-card bg-white rounded-xl shadow-sm p-6 fade-in">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                        <i class="fas fa-briefcase text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold" data-vi="Kinh nghiệm làm việc" data-en="Work Experience">Kinh nghiệm làm việc</h3>
                        <p class="text-sm text-gray-600" data-vi="Hành trình nghề nghiệp" data-en="Professional Journey">Professional Journey</p>
                    </div>
                </div>
                
                <div class="relative">
                    <!-- Timeline -->
                    <div class="absolute left-6 top-12 bottom-0 w-0.5 timeline-line"></div>
                    
                    <!-- Experience Items -->
                    <div class="space-y-6">
                        <div class="relative flex items-start">
                            <div class="absolute left-6 w-3 h-3 bg-blue-600 rounded-full -translate-x-1/2"></div>
                            <div class="ml-12">
                                <h4 class="font-semibold text-gray-800">Team Lead & Senior Web Developer</h4>
                                <p class="text-sm text-gray-600 mb-2">CONCRETE Corp. • 2019 - Hiện tại</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Thiết kế và duy trì hệ thống thương mại điện tử, SNS có hơn 1 triệu người dùng.</li>
                                    <li>• Lead team 10 developers, áp dụng Agile/Scrum</li>
                                    <li>• Tối ưu hiệu suất, giảm 40% thời gian tải trang.</li>
                                    <li>• Thiết lập hạ tầng triển khai sử dụng AWS và CI/CD.</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="relative flex items-start">
                            <div class="absolute left-6 w-3 h-3 bg-blue-600 rounded-full -translate-x-1/2"></div>
                            <div class="ml-12">
                                <h4 class="font-semibold text-gray-800">Full Stack Developer</h4>
                                <p class="text-sm text-gray-600 mb-2">ANS Asia • 2017 - 2019</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Developed and maintained ERP/CRM system with 1M+ users.</li>
                                    <li>• Tích hợp các dịch vụ bên thứ ba (thanh toán, phân tích,...)</li>
                                    <li>• Hướng dẫn và hỗ trợ phát triển kỹ năng cho lập trình viên junior.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="absolute left-6 w-3 h-3 bg-blue-600 rounded-full -translate-x-1/2"></div>
                            <div class="ml-12">
                                <h4 class="font-semibold text-gray-800">Full Stack Developer</h4>
                                <p class="text-sm text-gray-600 mb-2">BAP Software • 2016 - 2017</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Phát triển backend với Laravel và frontend với React.</li>
                                    <li>• Tham gia phân tích, phát triển các tính năng của dự án.</li>
                                    <li>• Tham gia review code, đảm bảo chất lượng dự án.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="absolute left-6 w-3 h-3 bg-blue-600 rounded-full -translate-x-1/2"></div>
                            <div class="ml-12">
                                <h4 class="font-semibold text-gray-800">Frontend Developer</h4>
                                <p class="text-sm text-gray-600 mb-2">DIGITAL SHIP CORP • 2014 - 2016</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Xây dựng giao diện người dùng cho hệ thống logistics và vận chuyển với HTML5, CSS3, JavaScript.</li>
                                    <li>• Phối hợp với nhóm thiết kế UX/UI để hiện thực hoá wireframe.</li>
                                    <li>• Xây dựng component frontend dạng module, giúp giảm 50% chi phí bảo trì.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Projects Section -->
            <section id="projects" class="post-card bg-white rounded-xl shadow-sm p-6 fade-in">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                        <i class="fas fa-project-diagram text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold" data-vi="Dự án nổi bật" data-en="Featured Projects">Dự án nổi bật</h3>
                        <p class="text-sm text-gray-600" data-vi="Dự án nổi bật" data-en="Featured Projects">Featured Projects</p>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                        <h4 class="font-semibold text-gray-800 mb-2">E-Commerce Platform</h4>
                        <p class="text-sm text-gray-600 mb-3">Nền tảng thương mại điện tử B2B với 50k+ sản phẩm</p>
                        <div class="flex flex-wrap gap-1 mb-3">
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">Laravel</span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">Vue.js</span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">AWS</span>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Xem chi tiết →</a>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                        <h4 class="font-semibold text-gray-800 mb-2">Multichannel Sales Platform</h4>
                        <p class="text-sm text-gray-600 mb-3">Quản lý Bán hàng Đa kênh Shopee, TikTok, Facebook</p>
                        <div class="flex flex-wrap gap-1 mb-3">
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">NestJS</span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">NextJS</span>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">PostgreSQL</span>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Xem chi tiết →</a>
                    </div>
                </div>
            </section>
            
            <!-- Blog Section -->
            <section id="blog" class="post-card bg-white rounded-xl shadow-sm p-6 fade-in">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                        <i class="fas fa-pen-fancy text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold" data-vi="Bài viết gần đây" data-en="Recent Articles">Bài viết gần đây</h3>
                        <p class="text-sm text-gray-600" data-vi="Chia sẻ kiến thức & kinh nghiệm" data-en="Sharing knowledge & experience">Chia sẻ kiến thức & kinh nghiệm</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <!-- Blog Post 1 -->
                    <article class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full">AWS</span>
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">DevOps</span>
                                    <span class="text-xs text-gray-500">• 2 ngày trước</span>
                                </div>
                                <h4 class="font-semibold text-gray-800 mb-2 hover:text-blue-600 transition-colors cursor-pointer" 
                                    data-vi="Tối ưu hóa chi phí AWS: Từ $500 xuống $150/tháng" 
                                    data-en="AWS Cost Optimization: From $500 to $150/month">
                                    Tối ưu hóa chi phí AWS: Từ $500 xuống $150/tháng
                                </h4>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2"
                                   data-vi="Chia sẻ kinh nghiệm giảm 70% chi phí AWS cho startup thông qua việc tối ưu hóa EC2, RDS và sử dụng Spot Instances hiệu quả..."
                                   data-en="Sharing experience on reducing AWS costs by 70% for startups through EC2 optimization, RDS tuning, and effective Spot Instances usage...">
                                    Chia sẻ kinh nghiệm giảm 70% chi phí AWS cho startup thông qua việc tối ưu hóa EC2, RDS và sử dụng Spot Instances hiệu quả...
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span><i class="far fa-eye"></i> 1.2k</span>
                                        <span><i class="far fa-heart"></i> 89</span>
                                        <span><i class="far fa-comment"></i> 12</span>
                                    </div>
                                    <a href="#" class="text-sm text-blue-600 hover:underline flex items-center" data-vi="Đọc tiếp →" data-en="Read more →">
                                        <span>Đọc tiếp</span>
                                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-24 h-20 rounded-lg ml-4 hidden sm:block bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                                <i class="fab fa-aws text-white text-2xl"></i>
                            </div>
                        </div>
                    </article>
                    
                    <!-- Blog Post 2 -->
                    <article class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded-full">Laravel</span>
                                    <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded-full">Performance</span>
                                    <span class="text-xs text-gray-500">• 1 tuần trước</span>
                                </div>
                                <h4 class="font-semibold text-gray-800 mb-2 hover:text-blue-600 transition-colors cursor-pointer"
                                    data-vi="Laravel Performance Tuning: Xử lý 1 triệu requests/ngày" 
                                    data-en="Laravel Performance Tuning: Handling 1M requests/day">
                                    Laravel Performance Tuning: Xử lý 1 triệu requests/ngày
                                </h4>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2"
                                   data-vi="Hướng dẫn chi tiết cách tối ưu Laravel application để handle traffic lớn: caching strategies, queue optimization, database indexing..."
                                   data-en="Detailed guide on optimizing Laravel applications for high traffic: caching strategies, queue optimization, database indexing...">
                                    Hướng dẫn chi tiết cách tối ưu Laravel application để handle traffic lớn: caching strategies, queue optimization, database indexing...
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span><i class="far fa-eye"></i> 856</span>
                                        <span><i class="far fa-heart"></i> 67</span>
                                        <span><i class="far fa-comment"></i> 8</span>
                                    </div>
                                    <a href="#" class="text-sm text-blue-600 hover:underline flex items-center" data-vi="Đọc tiếp →" data-en="Read more →">
                                        <span>Đọc tiếp</span>
                                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-24 h-20 rounded-lg ml-4 hidden sm:flex bg-gradient-to-br from-red-500 to-orange-500 items-center justify-center">
                                <i class="fab fa-laravel text-white text-2xl"></i>
                            </div>
                        </div>
                    </article>
                    
                    <!-- Blog Post 3 -->
                    <article class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full">Python</span>
                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full">AI/ML</span>
                                    <span class="text-xs text-gray-500">• 2 tuần trước</span>
                                </div>
                                <h4 class="font-semibold text-gray-800 mb-2 hover:text-blue-600 transition-colors cursor-pointer"
                                    data-vi="Xây dựng Chatbot AI với Python và OpenAI API" 
                                    data-en="Building AI Chatbot with Python and OpenAI API">
                                    Xây dựng Chatbot AI với Python và OpenAI API
                                </h4>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2"
                                   data-vi="Step-by-step tutorial xây dựng chatbot thông minh sử dụng Python, FastAPI và OpenAI GPT-4. Include source code và best practices..."
                                   data-en="Step-by-step tutorial on building smart chatbot using Python, FastAPI and OpenAI GPT-4. Includes source code and best practices...">
                                    Step-by-step tutorial xây dựng chatbot thông minh sử dụng Python, FastAPI và OpenAI GPT-4. Include source code và best practices...
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span><i class="far fa-eye"></i> 2.3k</span>
                                        <span><i class="far fa-heart"></i> 156</span>
                                        <span><i class="far fa-comment"></i> 23</span>
                                    </div>
                                    <a href="#" class="text-sm text-blue-600 hover:underline flex items-center" data-vi="Đọc tiếp →" data-en="Read more →">
                                        <span>Đọc tiếp</span>
                                        <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="w-24 h-20 rounded-lg ml-4 hidden sm:flex bg-gradient-to-br from-purple-500 to-indigo-600 items-center justify-center">
                                <i class="fas fa-robot text-white text-2xl"></i>
                            </div>
                        </div>
                    </article>
                </div>
                
                <div class="mt-6 text-center">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-book-open mr-2"></i>
                        <span data-vi="Xem tất cả bài viết" data-en="View all articles">Xem tất cả bài viết</span>
                    </a>
                </div>
            </section>
            
            <!-- Contact Section -->
            <section id="contact" class="post-card bg-white rounded-xl shadow-sm p-6 fade-in">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-3">
                        <i class="fas fa-envelope text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold" data-vi="Liên hệ với tôi" data-en="Contact Me">Liên hệ với tôi</h3>
                        <p class="text-sm text-gray-600" data-vi="Hãy làm việc cùng nhau" data-en="Let's work together">Let's work together</p>
                    </div>
                </div>
                
                <form id="contact-form" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" data-vi="Họ tên" data-en="Full Name">Họ tên</label>
                            <input type="text" name="name" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                            <span class="error-message hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" data-vi="Email" data-en="Email">Email</label>
                            <input type="email" name="email" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                            <span class="error-message hidden"></span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" data-vi="Chủ đề" data-en="Subject">Chủ đề</label>
                        <input type="text" name="subject" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        <span class="error-message hidden"></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" data-vi="Tin nhắn" data-en="Message">Tin nhắn</label>
                        <textarea name="message" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all resize-none"></textarea>
                        <span class="error-message hidden"></span>
                    </div>
                    
                    <button type="submit" id="submit-btn"
                        class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <span data-vi="Gửi tin nhắn" data-en="Send Message">Gửi tin nhắn</span>
                    </button>
                </form>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-3">Hoặc liên hệ trực tiếp:</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="mailto:trieunb@concrete-corp.com" target="_blank" class="text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>trieunb@concrete-corp.com
                        </a>
                        <a href="https://www.linkedin.com/in/nguy%E1%BB%85n-b%C3%A1-tri%E1%BB%81u-0b545079/" target="_blank" class="text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fab fa-linkedin mr-2"></i>LinkedIn
                        </a>
                        <a href="https://github.com/trieuconcrete" target="_blank" class="text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fab fa-github mr-2"></i>GitHub
                        </a>
                    </div>
                </div>
            </section>
            
        </div>
    </main>
    
    <!-- Back to top button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hidden hover:bg-blue-700 transition-colors z-30">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('#mobile-menu-btn').click(function() {
                $('#sidebar').addClass('active');
                $('#mobile-overlay').removeClass('hidden');
            });
            
            $('#mobile-overlay').click(function() {
                $('#sidebar').removeClass('active');
                $(this).addClass('hidden');
            });
            
            // Smooth scroll for navigation links
            $('.nav-link').click(function(e) {
                e.preventDefault();
                const target = $(this).attr('href');
                const offset = $(target).offset().top - 80;
                
                $('html, body').animate({
                    scrollTop: offset
                }, 600);
                
                // Close mobile menu if open
                if ($(window).width() < 1024) {
                    $('#sidebar').removeClass('active');
                    $('#mobile-overlay').addClass('hidden');
                }
            });
            
            // Active navigation highlight
            $(window).scroll(function() {
                const scrollPos = $(window).scrollTop() + 100;
                
                $('section').each(function() {
                    const top = $(this).offset().top;
                    const bottom = top + $(this).height();
                    const id = $(this).attr('id');
                    
                    if (scrollPos >= top && scrollPos <= bottom) {
                        $('.nav-link').removeClass('bg-blue-50 text-blue-600');
                        $('.nav-link[href="#' + id + '"]').addClass('bg-blue-50 text-blue-600');
                    }
                });
                
                // Show/hide back to top button
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });
            
            // Language switcher
            const translations = {
                vi: {
                    'portfolio': 'Danh mục đầu tư',
                    'skills_tech': 'Kỹ năng & Công nghệ',
                    'pro_journey': 'Hành trình nghề nghiệp',
                    'featured': 'Dự án nổi bật',
                    'work_together': 'Hãy làm việc cùng nhau',
                    'backend_dev': 'Phát triển Backend',
                    'frontend_dev': 'Phát triển Frontend',
                    'cloud_devops': 'Cloud & DevOps',
                    'senior_dev': 'Lập trình viên cao cấp',
                    'tech_company': 'Công ty công nghệ ABC',
                    'current': 'Hiện tại',
                    'fullstack_dev': 'Lập trình viên Full Stack',
                    'startup': 'ANS Asia',
                    'name_label': 'Họ tên',
                    'email_label': 'Email',
                    'subject_label': 'Chủ đề',
                    'message_label': 'Tin nhắn',
                    'send_message': 'Gửi tin nhắn',
                    'or_contact': 'Hoặc liên hệ trực tiếp:',
                    'view_details': 'Xem chi tiết →',
                    'ecommerce_desc': 'Nền tảng thương mại điện tử B2B với 50k+ sản phẩm',
                    'analytics_desc': 'Dashboard phân tích dữ liệu real-time cho logistics',
                    'lead_team': '• Lead team 10 developers, áp dụng Agile/Scrum',
                    'optimize_perf': '• Optimize performance, giảm 40% thời gian load',
                    'build_mvp': '• Xây dựng MVP cho 3 sản phẩm startup',
                    'implement_ci': '• Implement CI/CD pipeline với AWS',
                    'mentor': '• Mentor junior developers',
                    'develop_maintain': '• Phát triển và maintain hệ thống e-commerce với 1M+ users'
                },
                en: {
                    'portfolio': 'Portfolio',
                    'skills_tech': 'Skills & Technologies',
                    'pro_journey': 'Professional Journey',
                    'featured': 'Featured Projects',
                    'work_together': "Let's work together",
                    'backend_dev': 'Backend Development',
                    'frontend_dev': 'Frontend Development',
                    'cloud_devops': 'Cloud & DevOps',
                    'senior_dev': 'Team Lead & Senior Web Developer',
                    'tech_company': 'CONCRETE Corp.',
                    'current': 'Present',
                    'fullstack_dev': 'Web Developer',
                    'startup': 'ANS Asia',
                    'name_label': 'Full Name',
                    'email_label': 'Email',
                    'subject_label': 'Subject',
                    'message_label': 'Message',
                    'send_message': 'Send Message',
                    'or_contact': 'Or contact directly:',
                    'view_details': 'View Details →',
                    'ecommerce_desc': 'B2B e-commerce platform with 50k+ products',
                    'analytics_desc': 'Multichannel Sales Management Platform for Shopee, TikTok, and Facebook',
                    'lead_team': '• Lead team of 10 developers, implementing Agile/Scrum',
                    'optimize_perf': '• Optimized performance, reduced load time by 40%',
                    'build_mvp': '• Built MVP for 3 startup products',
                    'implement_ci': '• Implemented CI/CD pipeline with AWS',
                    'mentor': '• Mentored junior developers',
                    'develop_maintain': '• Developed and maintained e-commerce system with 1M+ users'
                }
            };
            
            function switchLanguage(lang) {
                // Update all elements with data-vi and data-en attributes
                $('[data-vi][data-en]').each(function() {
                    $(this).text($(this).data(lang));
                });
                
                // Update specific translated content
                if (lang === 'en') {
                    $('#skills h4:contains("Backend Development")').text('Backend Development');
                    $('#skills h4:contains("Frontend Development")').text('Frontend Development');
                    $('#skills h4:contains("Cloud & DevOps")').text('Cloud & DevOps');
                    $('#skills p:contains("Skills & Technologies")').text('Skills & Technologies');
                    $('#experience p:contains("Professional Journey")').text('Professional Journey');
                    $('#projects p:contains("Featured Projects")').text('Featured Projects');
                    $('#contact p:contains("Let\'s work together")').text("Let's work together");
                    
                    // Update experience section
                    $('#experience h4:first').text('Team Lead & Senior Web Developer');
                    $('#experience p:contains("CONCRETE Corp.")').html('CONCRETE Corp. • 2019 - Present');
                    $('#experience h4:eq(1)').text('Full Stack Developer');
                    $('#experience p:contains("ANS Asia")').html('ANS Asia • 2017 - 2019');
                    
                    // Update experience bullets
                    $('#experience li:contains("Phát triển")').text(translations.en.develop_maintain);
                    $('#experience li:contains("Lead team")').text(translations.en.lead_team);
                    $('#experience li:contains("Optimize")').text(translations.en.optimize_perf);
                    $('#experience li:contains("Xây dựng MVP")').text(translations.en.build_mvp);
                    $('#experience li:contains("Implement CI/CD")').text(translations.en.implement_ci);
                    $('#experience li:contains("Mentor")').text(translations.en.mentor);
                    
                    // Update projects
                    $('#projects .border:first p:first').next().text(translations.en.ecommerce_desc);
                    $('#projects .border:last p:first').next().text(translations.en.analytics_desc);
                    $('#projects a:contains("Xem chi tiết")').text(translations.en.view_details);
                    
                    // Update blog section
                    $('#blog h3').text('Recent Articles');
                    $('#blog p:contains("Chia sẻ kiến thức")').text('Sharing knowledge & experience');
                    $('.text-xs:contains("ngày trước")').each(function() {
                        $(this).text($(this).text().replace('ngày trước', 'days ago'));
                    });
                    $('.text-xs:contains("tuần trước")').each(function() {
                        $(this).text($(this).text().replace('tuần trước', 'weeks ago'));
                    });
                    $('a:contains("Đọc tiếp")').text('Read more →');
                    $('span:contains("Xem tất cả bài viết")').text('View all articles');
                    
                    // Update form labels
                    $('label:contains("Họ tên")').text(translations.en.name_label);
                    $('label:contains("Email")').text(translations.en.email_label);
                    $('label:contains("Chủ đề")').text(translations.en.subject_label);
                    $('label:contains("Tin nhắn")').text(translations.en.message_label);
                    $('button:contains("Gửi tin nhắn")').text(translations.en.send_message);
                    $('#contact p:contains("Hoặc liên hệ")').text(translations.en.or_contact);
                } else {
                    // Reset to Vietnamese
                    location.reload(); // Simple reload to reset to Vietnamese
                }
                
                // Save language preference
                localStorage.setItem('preferred-language', lang);
            }
            
            // Initialize language
            const savedLang = localStorage.getItem('preferred-language') || 'vi';
            $('#language-select').val(savedLang);
            if (savedLang === 'en') {
                switchLanguage('en');
            }
            
            // Language change handler
            $('#language-select').change(function() {
                const selectedLang = $(this).val();
                switchLanguage(selectedLang);
            });
            
            // Back to top
            $('#back-to-top').click(function() {
                $('html, body').animate({ scrollTop: 0 }, 600);
            });
            
            // Fade in animation on scroll
            function checkFadeIn() {
                $('.fade-in').each(function() {
                    const top = $(this).offset().top;
                    const bottom = top + $(this).height();
                    const scrollTop = $(window).scrollTop();
                    const scrollBottom = scrollTop + $(window).height();
                    
                    if (scrollBottom > top + 50) {
                        $(this).addClass('visible');
                    }
                });
            }
            
            checkFadeIn();
            $(window).scroll(checkFadeIn);
            
            // Form validation and submission
            $('#contact-form').submit(function(e) {
                e.preventDefault();
                
                // Clear previous errors
                $('.form-error').removeClass('form-error');
                $('.error-message').hide().text('');
                
                // Get form data
                const formData = {
                    name: $('[name="name"]').val().trim(),
                    email: $('[name="email"]').val().trim(),
                    subject: $('[name="subject"]').val().trim(),
                    message: $('[name="message"]').val().trim()
                };
                
                // Validation rules
                let isValid = true;
                const errors = {};
                
                // Name validation
                if (formData.name.length < 2) {
                    errors.name = $('#language-select').val() === 'en' 
                        ? 'Name must be at least 2 characters' 
                        : 'Tên phải có ít nhất 2 ký tự';
                    isValid = false;
                } else if (!/^[a-zA-ZÀ-ỹ\s]+$/.test(formData.name)) {
                    errors.name = $('#language-select').val() === 'en'
                        ? 'Name can only contain letters'
                        : 'Tên chỉ được chứa chữ cái';
                    isValid = false;
                }
                
                // Email validation
                if (!isValidEmail(formData.email)) {
                    errors.email = $('#language-select').val() === 'en'
                        ? 'Please enter a valid email address'
                        : 'Vui lòng nhập email hợp lệ';
                    isValid = false;
                }
                
                // Subject validation
                if (formData.subject.length < 3) {
                    errors.subject = $('#language-select').val() === 'en'
                        ? 'Subject must be at least 3 characters'
                        : 'Chủ đề phải có ít nhất 3 ký tự';
                    isValid = false;
                } else if (formData.subject.length > 100) {
                    errors.subject = $('#language-select').val() === 'en'
                        ? 'Subject must be less than 100 characters'
                        : 'Chủ đề phải ít hơn 100 ký tự';
                    isValid = false;
                }
                
                // Message validation
                if (formData.message.length < 10) {
                    errors.message = $('#language-select').val() === 'en'
                        ? 'Message must be at least 10 characters'
                        : 'Tin nhắn phải có ít nhất 10 ký tự';
                    isValid = false;
                } else if (formData.message.length > 1000) {
                    errors.message = $('#language-select').val() === 'en'
                        ? 'Message must be less than 1000 characters'
                        : 'Tin nhắn phải ít hơn 1000 ký tự';
                    isValid = false;
                }
                
                // Show errors
                if (!isValid) {
                    Object.keys(errors).forEach(field => {
                        const input = $(`[name="${field}"]`);
                        input.addClass('form-error');
                        input.next('.error-message').text(errors[field]).show();
                    });
                    return;
                }
                
                // Show loading state
                const submitBtn = $('#submit-btn');
                const originalHtml = submitBtn.html();
                const loadingText = $('#language-select').val() === 'en' 
                    ? 'Sending...' 
                    : 'Đang gửi...';
                submitBtn.addClass('btn-loading').html(loadingText + '<span class="spinner"></span>');
                
                // Disable form inputs
                $('#contact-form input, #contact-form textarea').prop('disabled', true);
                
                // Simulate API call (in real app, would send to server)
                setTimeout(() => {
                    // Reset form
                    this.reset();
                    
                    // Restore button and enable inputs
                    submitBtn.removeClass('btn-loading').html(originalHtml);
                    $('#contact-form input, #contact-form textarea').prop('disabled', false);
                    
                    // Show success message
                    const successMsg = $('#language-select').val() === 'en'
                        ? 'Message sent successfully! I will get back to you soon.'
                        : 'Tin nhắn đã được gửi thành công! Tôi sẽ phản hồi sớm nhất.';
                    showNotification(successMsg, 'success');
                }, 2000);
            });
            
            // Real-time validation
            $('input, textarea').on('blur', function() {
                const field = $(this);
                const fieldName = field.attr('name');
                const value = field.val().trim();
                
                // Clear previous error
                field.removeClass('form-error');
                field.next('.error-message').hide().text('');
                
                // Validate based on field
                let error = '';
                
                switch(fieldName) {
                    case 'name':
                        if (value.length > 0 && value.length < 2) {
                            error = $('#language-select').val() === 'en'
                                ? 'Name must be at least 2 characters'
                                : 'Tên phải có ít nhất 2 ký tự';
                        }
                        break;
                    case 'email':
                        if (value.length > 0 && !isValidEmail(value)) {
                            error = $('#language-select').val() === 'en'
                                ? 'Please enter a valid email address'
                                : 'Vui lòng nhập email hợp lệ';
                        }
                        break;
                    case 'subject':
                        if (value.length > 0 && value.length < 3) {
                            error = $('#language-select').val() === 'en'
                                ? 'Subject must be at least 3 characters'
                                : 'Chủ đề phải có ít nhất 3 ký tự';
                        }
                        break;
                    case 'message':
                        if (value.length > 0 && value.length < 10) {
                            error = $('#language-select').val() === 'en'
                                ? 'Message must be at least 10 characters'
                                : 'Tin nhắn phải có ít nhất 10 ký tự';
                        }
                        break;
                }
                
                if (error) {
                    field.addClass('form-error');
                    field.next('.error-message').text(error).show();
                }
            });
            
            // Email validation helper
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            // Notification helper
            function showNotification(message, type) {
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                const notification = $(`
                    <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 notification">
                        ${message}
                    </div>
                `);
                
                $('body').append(notification);
                
                setTimeout(function() {
                    notification.fadeOut(function() {
                        $(this).remove();
                    });
                }, 3000);
            }
            
            // Typing animation for name
            const name = "Nguyen Trieu";
            let i = 0;
            function typeWriter() {
                if (i < name.length) {
                    // This would animate the name typing, but keeping it simple for now
                    i++;
                    setTimeout(typeWriter, 100);
                }
            }
            
            // Initialize
            typeWriter();
        });
    </script>
</body>
</html>