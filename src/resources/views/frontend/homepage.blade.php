<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebFolio.vn - Thiết Kế Website Chuyên Nghiệp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 25%, #1d4ed8 50%, #2563eb 75%, #3b82f6 100%);
        }
        .gradient-bg-secondary {
            background: linear-gradient(135deg, #374151 0%, #4b5563 50%, #6b7280 100%);
        }
        .glass-effect {
            background: rgba(30, 58, 138, 0.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.15);
        }
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .smooth-scroll {
            scroll-behavior: smooth;
        }
        .text-primary {
            color: #1e3a8a;
        }
        .text-secondary {
            color: #374151;
        }
        .bg-primary {
            background-color: #1e3a8a;
        }
        .bg-primary-light {
            background-color: #3b82f6;
        }
        .border-primary {
            border-color: #1e3a8a;
        }
        .trust-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #374151 100%);
        }
        .accent-gold {
            color: #d97706;
        }
        .bg-accent-gold {
            background-color: #d97706;
        }
        /* Language Switcher Styles */
        .lang-switcher {
            position: relative;
        }
        .lang-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 8px;
            min-width: 150px;
        }
        .lang-option {
            padding: 10px 16px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .lang-option:hover {
            background-color: #f3f4f6;
        }
        .lang-option.active {
            background-color: #e5e7eb;
        }
        .flag-icon {
            width: 20px;
            height: 15px;
            display: inline-block;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        .flag-vi {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 20'%3E%3Crect width='30' height='20' fill='%23da251d'/%3E%3Cpolygon points='15,4 16.5,9 21.5,9 17.5,12 19,17 15,14 11,17 12.5,12 8.5,9 13.5,9' fill='%23ffff00'/%3E%3C/svg%3E");
        }
        .flag-en {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 60 30'%3E%3CclipPath id='s'%3E%3Cpath d='M0,0 v30 h60 v-30 z'/%3E%3C/clipPath%3E%3CclipPath id='t'%3E%3Cpath d='M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z'/%3E%3C/clipPath%3E%3Cg clip-path='url(%23s)'%3E%3Cpath d='M0,0 v30 h60 v-30 z' fill='%23012169'/%3E%3Cpath d='M0,0 L60,30 M60,0 L0,30' stroke='%23fff' stroke-width='6'/%3E%3Cpath d='M0,0 L60,30 M60,0 L0,30' clip-path='url(%23t)' stroke='%23C8102E' stroke-width='4'/%3E%3Cpath d='M30,0 v30 M0,15 h60' stroke='%23fff' stroke-width='10'/%3E%3Cpath d='M30,0 v30 M0,15 h60' stroke='%23C8102E' stroke-width='6'/%3E%3C/g%3E%3C/svg%3E");
        }
        
        /* Chatbox Styles */
        #chat-window {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }
        
        #chat-window.show {
            transform: scale(1);
            opacity: 1;
        }
        
        #chat-messages {
            background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
        }
        
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        
        #chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        #chat-messages::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .chat-message {
            animation: fadeInUp 0.3s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .typing-indicator {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .typing-indicator span {
            height: 8px;
            width: 8px;
            background-color: #9ca3af;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }
        
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }
        
        @media (max-width: 640px) {
            #chat-window {
                width: calc(100vw - 2rem);
                bottom: 5rem;
                right: 1rem;
                left: 1rem;
            }
            
            #chat-toggle {
                bottom: 1rem;
                right: 1rem;
            }
            
            #back-to-top {
                bottom: 5rem;
                right: 1rem;
            }
        }
    </style>
</head>
<body class="smooth-scroll bg-gray-50">
    <!-- Language Data -->
    <script>
        const translations = {
            vi: {
                // Navigation
                'nav.home': 'Trang Chủ',
                'nav.services': 'Dịch Vụ',
                'nav.portfolio': 'Portfolio',
                'nav.themes': 'Kho Theme',
                'nav.pricing': 'Bảng Giá',
                'nav.about': 'Về Chúng Tôi',
                'nav.contact': 'Liên Hệ',
                
                // Hero Section
                'hero.badge': '⭐ #1 Công ty thiết kế website tại Việt Nam',
                'hero.title': 'Thiết Kế Website',
                'hero.highlight': 'Chuyên Nghiệp',
                'hero.description': 'Chúng tôi tạo ra những website hiện đại, responsive và tối ưu SEO để giúp doanh nghiệp của bạn thành công trong thời đại số.',
                'hero.feature1': 'Responsive Design',
                'hero.feature2': 'Tốc Độ Cao',
                'hero.feature3': 'SEO Tối Ưu',
                'hero.feature4': 'Bảo Mật 24/7',
                'hero.cta1': 'Bắt Đầu Ngay',
                'hero.cta2': 'Xem Portfolio',
                'hero.stats.projects': 'Dự Án',
                'hero.stats.clients': 'Khách Hàng',
                'hero.stats.experience': 'Năm Kinh Nghiệm',
                'hero.stats.support': 'Hỗ Trợ',
                
                // Services Section
                'services.title': 'Dịch Vụ Của Chúng Tôi',
                'services.subtitle': 'Chúng tôi cung cấp giải pháp toàn diện cho nhu cầu thiết kế và phát triển website của bạn',
                'services.responsive.title': 'Responsive Design',
                'services.responsive.desc': 'Website tự động thích ứng với mọi thiết bị từ mobile, tablet đến desktop, đảm bảo trải nghiệm tốt nhất cho người dùng.',
                'services.seo.title': 'SEO Tối Ưu',
                'services.seo.desc': 'Tối ưu hóa website để đạt thứ hạng cao trên Google, tăng khả năng tiếp cận khách hàng tiềm năng.',
                'services.speed.title': 'Tốc Độ Cao',
                'services.speed.desc': 'Website được tối ưu hóa để tải nhanh, cải thiện trải nghiệm người dùng và thứ hạng SEO.',
                'services.security.title': 'Bảo Mật Cao',
                'services.security.desc': 'Áp dụng các biện pháp bảo mật tiên tiến để bảo vệ website và dữ liệu khách hàng.',
                'services.management.title': 'Quản Lý Dễ Dàng',
                'services.management.desc': 'Giao diện quản trị thân thiện, dễ sử dụng, giúp bạn cập nhật nội dung một cách nhanh chóng.',
                'services.support.title': 'Hỗ Trợ 24/7',
                'services.support.desc': 'Đội ngũ hỗ trợ kỹ thuật luôn sẵn sàng giúp đỡ bạn mọi lúc, mọi nơi.',
                
                // Portfolio Section
                'portfolio.title': 'Portfolio',
                'portfolio.subtitle': 'Khám phá những dự án website chúng tôi đã thực hiện thành công',
                'portfolio.viewDetails': 'Xem Chi Tiết',
                'portfolio.completed': 'Hoàn thành',
                'portfolio.viewAll': 'Xem Tất Cả Portfolio',
                'portfolio.viewMore': 'Bạn muốn xem thêm dự án của chúng tôi?',
                
                // Themes Section
                'themes.title': 'Kho Theme HTML',
                'themes.subtitle': 'Bộ sưu tập theme HTML chuyên nghiệp, responsive và dễ tùy chỉnh cho mọi loại hình doanh nghiệp',
                'themes.filter.all': 'Tất Cả',
                'themes.filter.business': 'Doanh Nghiệp',
                'themes.filter.ecommerce': 'Bán Hàng',
                'themes.filter.portfolio': 'Portfolio',
                'themes.filter.blog': 'Blog',
                'themes.filter.landing': 'Landing Page',
                'themes.viewDemo': 'Xem Demo',
                'themes.download': 'Tải Về',
                'themes.loadMore': 'Xem Thêm Theme',
                'themes.whyChoose': 'Tại Sao Chọn Theme Của Chúng Tôi?',
                'themes.feature1.title': 'Responsive Design',
                'themes.feature1.desc': 'Tất cả theme đều tối ưu hoàn hảo trên mọi thiết bị từ mobile đến desktop.',
                'themes.feature2.title': 'Code Sạch & Tối Ưu',
                'themes.feature2.desc': 'Mã nguồn được viết theo chuẩn W3C, tải nhanh và dễ tùy chỉnh.',
                'themes.feature3.title': 'Hỗ Trợ 24/7',
                'themes.feature3.desc': 'Nhận hỗ trợ kỹ thuật và cập nhật miễn phí trọn đời cho tất cả theme.',
                'themes.new': 'MỚI',
                'themes.hot': 'HOT',
                
                // Pricing Section
                'pricing.title': 'Bảng Giá Dịch Vụ',
                'pricing.subtitle': 'Chọn gói dịch vụ phù hợp với nhu cầu và ngân sách của bạn',
                'pricing.basic.title': 'Gói Cơ Bản',
                'pricing.basic.desc': 'Phù hợp cho cá nhân và doanh nghiệp nhỏ',
                'pricing.pro.title': 'Gói Chuyên Nghiệp',
                'pricing.pro.desc': 'Lựa chọn tốt nhất cho doanh nghiệp',
                'pricing.pro.badge': 'PHỔ BIẾN NHẤT',
                'pricing.enterprise.title': 'Gói Doanh Nghiệp',
                'pricing.enterprise.desc': 'Giải pháp toàn diện cho tập đoán lớn',
                'pricing.enterprise.price': 'Liên Hệ',
                'pricing.enterprise.quote': 'Báo giá theo yêu cầu',
                'pricing.choose': 'Chọn Gói Này',
                'pricing.contact': 'Liên Hệ Ngay',
                'pricing.commitment': 'Cam Kết Của Chúng Tôi',
                'pricing.commitment1.title': 'Giao Hàng Đúng Hẹn',
                'pricing.commitment1.desc': 'Cam kết hoàn thành dự án đúng timeline đã thỏa thuận',
                'pricing.commitment2.title': 'Sửa Đổi Không Giới Hạn',
                'pricing.commitment2.desc': 'Điều chỉnh thiết kế cho đến khi bạn hoàn toàn hài lòng',
                'pricing.commitment3.title': 'Chất Lượng Đảm Bảo',
                'pricing.commitment3.desc': 'Hoàn tiền 100% nếu không hài lòng với chất lượng',
                
                // FAQ Section
                'faq.title': 'Câu Hỏi Thường Gặp',
                'faq.q1': 'Thời gian hoàn thành dự án là bao lâu?',
                'faq.a1': 'Gói Cơ Bản: 2-3 tuần, Gói Chuyên Nghiệp: 4-6 tuần, Gói Doanh Nghiệp: 8-12 tuần tùy thuộc vào độ phức tạp.',
                'faq.q2': 'Có được xem trước thiết kế trước khi thanh toán?',
                'faq.a2': 'Có, chúng tôi sẽ cung cấp mockup và wireframe để bạn xem trước và góp ý trước khi bắt đầu phát triển.',
                'faq.q3': 'Có hỗ trợ hosting và tên miền không?',
                'faq.a3': 'Chúng tôi hỗ trợ tư vấn và thiết lập hosting, tên miền. Chi phí hosting/domain sẽ được tính riêng theo nhà cung cấp.',
                'faq.q4': 'Sau khi hoàn thành có được chỉnh sửa không?',
                'faq.a4': 'Trong thời gian bảo hành, chúng tôi hỗ trợ sửa lỗi miễn phí. Các thay đổi về thiết kế/tính năng sẽ có chi phí riêng.',
                
                // About Section
                'about.title': 'Về WebFolio.vn',
                'about.desc1': 'Với hơn 5 năm kinh nghiệm trong lĩnh vực thiết kế và phát triển website, WebFolio.vn đã trở thành đối tác tin cậy của hàng trăm doanh nghiệp tại Việt Nam.',
                'about.desc2': 'Chúng tôi chuyên tạo ra những website không chỉ đẹp mắt mà còn hiệu quả, giúp khách hàng đạt được mục tiêu kinh doanh trong thời đại số.',
                'about.stats1': 'Dự Án Hoàn Thành',
                'about.stats2': 'Khách Hàng Hài Lòng',
                'about.stats3': 'Năm Kinh Nghiệm',
                'about.stats4': 'Hỗ Trợ Khách Hàng',
                'about.whyChoose': 'Tại Sao Chọn Chúng Tôi?',
                'about.reason1': 'Thiết kế responsive trên mọi thiết bị',
                'about.reason2': 'Tối ưu SEO và tốc độ tải trang',
                'about.reason3': 'Bảo hành và hỗ trợ dài hạn',
                'about.reason4': 'Giá cả cạnh tranh, minh bạch',
                'about.reason5': 'Bảo Mật Cao',
                'about.reason6': 'Quản Lý Dễ Dàng',
                
                // Contact Section
                'contact.title': 'Liên Hệ Với Chúng Tôi',
                'contact.subtitle': 'Sẵn sàng bắt đầu dự án website của bạn? Hãy liên hệ ngay để được tư vấn miễn phí',
                'contact.form.title': 'Gửi Tin Nhắn',
                'contact.form.name': 'Họ và Tên *',
                'contact.form.email': 'Email *',
                'contact.form.phone': 'Số Điện Thoại',
                'contact.form.service': 'Dịch Vụ Quan Tâm',
                'contact.form.service.choose': 'Chọn dịch vụ',
                'contact.form.service.design': 'Thiết Kế Website',
                'contact.form.service.ecommerce': 'Website Bán Hàng',
                'contact.form.service.corporate': 'Website Doanh Nghiệp',
                'contact.form.service.system': 'Website hệ thống ERP/CRM',
                'contact.form.service.maintenance': 'Bảo Trì Website',
                'contact.form.message': 'Tin Nhắn *',
                'contact.form.placeholder': 'Mô tả chi tiết về dự án của bạn...',
                'contact.form.submit': 'Gửi Tin Nhắn',
                'contact.info.title': 'Thông Tin Liên Hệ',
                'contact.info.address': 'Địa Chỉ',
                'contact.info.addressValue': '123 Đường ABC, Quận 1, TP. Hồ Chí Minh',
                'contact.info.phone': 'Điện Thoại',
                'contact.info.email': 'Email',
                'contact.info.hours': 'Giờ Làm Việc',
                'contact.info.hoursValue1': 'Thứ 2 - Thứ 6: 8:00 - 18:00',
                'contact.info.hoursValue2': 'Thứ 7: 8:00 - 12:00',
                'contact.social': 'Theo Dõi Chúng Tôi',
                
                // Footer
                'footer.desc': 'Chuyên thiết kế và phát triển website chuyên nghiệp, hiện đại và tối ưu SEO. Đồng hành cùng doanh nghiệp trong chuyển đổi số.',
                'footer.services': 'Dịch Vụ',
                'footer.services.design': 'Thiết Kế Website',
                'footer.services.ecommerce': 'Website Bán Hàng',
                'footer.services.corporate': 'Website Doanh Nghiệp',
                'footer.services.seo': 'Tối Ưu SEO',
                'footer.services.maintenance': 'Bảo Trì Website',
                'footer.contact': 'Liên Hệ',
                'footer.copyright': '© 2025 WebFolio.vn. Tất cả quyền được bảo lưu.',
                
                // Form Messages
                'form.error.name': 'Vui lòng nhập họ và tên',
                'form.error.email': 'Vui lòng nhập địa chỉ email',
                'form.error.emailInvalid': 'Vui lòng nhập địa chỉ email hợp lệ',
                'form.error.message': 'Vui lòng nhập tin nhắn',
                'form.success': 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.',
                'form.sending': 'Đang gửi...',
                
                // Pricing Features
                'pricing.feature.responsive': 'Thiết kế responsive trên mọi thiết bị',
                'pricing.feature.pages5': 'Tối đa 5 trang nội dung',
                'pricing.feature.pages15': 'Tối đa 15 trang nội dung',
                'pricing.feature.pagesUnlimited': 'Không giới hạn số trang',
                'pricing.feature.seoBasic': 'Tối ưu SEO cơ bản',
                'pricing.feature.seoAdvanced': 'Tối ưu SEO chuyên sâu',
                'pricing.feature.seoExpert': 'Tối ưu SEO chuyên gia',
                'pricing.feature.contactForm': 'Form liên hệ đơn giản',
                'pricing.feature.warranty12': 'Bảo hành 12 tháng',
                'pricing.feature.warranty24': 'Bảo hành 24 tháng',
                'pricing.feature.warrantyLifetime': 'Bảo hành trọn đời',
                'pricing.feature.supportBasic': 'Hỗ trợ kỹ thuật cơ bản',
                'pricing.feature.supportPriority': 'Hỗ trợ ưu tiên',
                'pricing.feature.supportDedicated': 'Dedicated support team',
                'pricing.feature.analytics': 'Tích hợp Google Analytics',
                'pricing.feature.cms': 'Hệ thống quản trị nội dung',
                'pricing.feature.social': 'Tích hợp mạng xã hội',
                'pricing.feature.erp': 'Tích hợp hệ thống ERP/CRM',
                'pricing.feature.multilingual': 'Đa ngôn ngữ & đa tiền tệ',
                'pricing.feature.security': 'Bảo mật cao',
                'pricing.feature.custom': 'Thiết kế tùy theo yêu cầu',
                'pricing.feature.responsivePremium': 'Thiết kế responsive cao cấp'
            },
            en: {
                // Navigation
                'nav.home': 'Home',
                'nav.services': 'Services',
                'nav.portfolio': 'Portfolio',
                'nav.themes': 'Theme Store',
                'nav.pricing': 'Pricing',
                'nav.about': 'About Us',
                'nav.contact': 'Contact',
                
                // Hero Section
                'hero.badge': '⭐ #1 Website Design Company in Vietnam',
                'hero.title': 'Professional Website',
                'hero.highlight': 'Design',
                'hero.description': 'We create modern, responsive and SEO-optimized websites to help your business succeed in the digital age.',
                'hero.feature1': 'Responsive Design',
                'hero.feature2': 'High Speed',
                'hero.feature3': 'SEO Optimized',
                'hero.feature4': '24/7 Security',
                'hero.cta1': 'Get Started',
                'hero.cta2': 'View Portfolio',
                'hero.stats.projects': 'Projects',
                'hero.stats.clients': 'Clients',
                'hero.stats.experience': 'Years Experience',
                'hero.stats.support': 'Support',
                
                // Services Section
                'services.title': 'Our Services',
                'services.subtitle': 'We provide comprehensive solutions for your website design and development needs',
                'services.responsive.title': 'Responsive Design',
                'services.responsive.desc': 'Websites automatically adapt to all devices from mobile, tablet to desktop, ensuring the best user experience.',
                'services.seo.title': 'SEO Optimization',
                'services.seo.desc': 'Optimize your website to achieve high rankings on Google, increasing reach to potential customers.',
                'services.speed.title': 'High Speed',
                'services.speed.desc': 'Websites are optimized for fast loading, improving user experience and SEO rankings.',
                'services.security.title': 'High Security',
                'services.security.desc': 'Apply advanced security measures to protect websites and customer data.',
                'services.management.title': 'Easy Management',
                'services.management.desc': 'User-friendly admin interface, easy to use, helping you update content quickly.',
                'services.support.title': '24/7 Support',
                'services.support.desc': 'Our technical support team is always ready to help you anytime, anywhere.',
                
                // Portfolio Section
                'portfolio.title': 'Portfolio',
                'portfolio.subtitle': 'Explore the website projects we have successfully completed',
                'portfolio.viewDetails': 'View Details',
                'portfolio.completed': 'Completed',
                'portfolio.viewAll': 'View All Portfolio',
                'portfolio.viewMore': 'Want to see more of our projects?',
                
                // Themes Section
                'themes.title': 'HTML Theme Store',
                'themes.subtitle': 'Collection of professional, responsive and easily customizable HTML themes for all types of businesses',
                'themes.filter.all': 'All',
                'themes.filter.business': 'Business',
                'themes.filter.ecommerce': 'E-Commerce',
                'themes.filter.portfolio': 'Portfolio',
                'themes.filter.blog': 'Blog',
                'themes.filter.landing': 'Landing Page',
                'themes.viewDemo': 'View Demo',
                'themes.download': 'Download',
                'themes.loadMore': 'Load More Themes',
                'themes.whyChoose': 'Why Choose Our Themes?',
                'themes.feature1.title': 'Responsive Design',
                'themes.feature1.desc': 'All themes are perfectly optimized on all devices from mobile to desktop.',
                'themes.feature2.title': 'Clean & Optimized Code',
                'themes.feature2.desc': 'Source code written according to W3C standards, fast loading and easy to customize.',
                'themes.feature3.title': '24/7 Support',
                'themes.feature3.desc': 'Get lifetime technical support and free updates for all themes.',
                'themes.new': 'NEW',
                'themes.hot': 'HOT',
                
                // Pricing Section
                'pricing.title': 'Service Pricing',
                'pricing.subtitle': 'Choose the service package that suits your needs and budget',
                'pricing.basic.title': 'Basic Plan',
                'pricing.basic.desc': 'Suitable for individuals and small businesses',
                'pricing.pro.title': 'Professional Plan',
                'pricing.pro.desc': 'Best choice for businesses',
                'pricing.pro.badge': 'MOST POPULAR',
                'pricing.enterprise.title': 'Enterprise Plan',
                'pricing.enterprise.desc': 'Comprehensive solution for large corporations',
                'pricing.enterprise.price': 'Contact',
                'pricing.enterprise.quote': 'Quote on request',
                'pricing.choose': 'Choose This Plan',
                'pricing.contact': 'Contact Now',
                'pricing.commitment': 'Our Commitment',
                'pricing.commitment1.title': 'On-Time Delivery',
                'pricing.commitment1.desc': 'Committed to completing projects on the agreed timeline',
                'pricing.commitment2.title': 'Unlimited Revisions',
                'pricing.commitment2.desc': 'Adjust the design until you are completely satisfied',
                'pricing.commitment3.title': 'Quality Guarantee',
                'pricing.commitment3.desc': '100% refund if not satisfied with quality',
                
                // FAQ Section
                'faq.title': 'Frequently Asked Questions',
                'faq.q1': 'How long does it take to complete a project?',
                'faq.a1': 'Basic Plan: 2-3 weeks, Professional Plan: 4-6 weeks, Enterprise Plan: 8-12 weeks depending on complexity.',
                'faq.q2': 'Can I preview the design before payment?',
                'faq.a2': 'Yes, we will provide mockups and wireframes for you to preview and provide feedback before starting development.',
                'faq.q3': 'Do you support hosting and domain?',
                'faq.a3': 'We provide consultation and setup for hosting and domain. Hosting/domain costs will be charged separately according to the provider.',
                'faq.q4': 'Can I make changes after completion?',
                'faq.a4': 'During the warranty period, we support bug fixes for free. Design/feature changes will have separate costs.',
                
                // About Section
                'about.title': 'About WebFolio.vn',
                'about.desc1': 'With over 5 years of experience in website design and development, WebFolio.vn has become a trusted partner of hundreds of businesses in Vietnam.',
                'about.desc2': 'We specialize in creating websites that are not only beautiful but also effective, helping customers achieve their business goals in the digital age.',
                'about.stats1': 'Projects Completed',
                'about.stats2': 'Happy Clients',
                'about.stats3': 'Years Experience',
                'about.stats4': 'Customer Support',
                'about.whyChoose': 'Why Choose Us?',
                'about.reason1': 'Responsive design on all devices',
                'about.reason2': 'SEO optimization and page loading speed',
                'about.reason3': 'Long-term warranty and support',
                'about.reason4': 'Competitive, transparent pricing',
                'about.reason5': 'High Security',
                'about.reason6': 'Easy Management',
                
                // Contact Section
                'contact.title': 'Contact Us',
                'contact.subtitle': 'Ready to start your website project? Contact us now for a free consultation',
                'contact.form.title': 'Send Message',
                'contact.form.name': 'Full Name *',
                'contact.form.email': 'Email *',
                'contact.form.phone': 'Phone Number',
                'contact.form.service': 'Service of Interest',
                'contact.form.service.choose': 'Choose service',
                'contact.form.service.design': 'Website Design',
                'contact.form.service.ecommerce': 'E-Commerce Website',
                'contact.form.service.corporate': 'Corporate Website',
                'contact.form.service.system': 'ERP/CRM System Website',
                'contact.form.service.maintenance': 'Website Maintenance',
                'contact.form.message': 'Message *',
                'contact.form.placeholder': 'Describe your project in detail...',
                'contact.form.submit': 'Send Message',
                'contact.info.title': 'Contact Information',
                'contact.info.address': 'Address',
                'contact.info.addressValue': '123 ABC Street, District 1, Ho Chi Minh City',
                'contact.info.phone': 'Phone',
                'contact.info.email': 'Email',
                'contact.info.hours': 'Working Hours',
                'contact.info.hoursValue1': 'Monday - Friday: 8:00 AM - 6:00 PM',
                'contact.info.hoursValue2': 'Saturday: 8:00 AM - 12:00 PM',
                'contact.social': 'Follow Us',
                
                // Footer
                'footer.desc': 'Specializing in professional, modern and SEO-optimized website design and development. Accompanying businesses in digital transformation.',
                'footer.services': 'Services',
                'footer.services.design': 'Website Design',
                'footer.services.ecommerce': 'E-Commerce Website',
                'footer.services.corporate': 'Corporate Website',
                'footer.services.seo': 'SEO Optimization',
                'footer.services.maintenance': 'Website Maintenance',
                'footer.contact': 'Contact',
                'footer.copyright': '© 2025 WebFolio.vn. All rights reserved.',
                
                // Form Messages
                'form.error.name': 'Please enter your name',
                'form.error.email': 'Please enter your email',
                'form.error.emailInvalid': 'Please enter a valid email address',
                'form.error.message': 'Please enter a message',
                'form.success': 'Thank you for contacting us! We will respond as soon as possible.',
                'form.sending': 'Sending...',
                
                // Pricing Features
                'pricing.feature.responsive': 'Responsive design on all devices',
                'pricing.feature.pages5': 'Up to 5 content pages',
                'pricing.feature.pages15': 'Up to 15 content pages',
                'pricing.feature.pagesUnlimited': 'Unlimited pages',
                'pricing.feature.seoBasic': 'Basic SEO optimization',
                'pricing.feature.seoAdvanced': 'Advanced SEO optimization',
                'pricing.feature.seoExpert': 'Expert SEO optimization',
                'pricing.feature.contactForm': 'Simple contact form',
                'pricing.feature.warranty12': '12-month warranty',
                'pricing.feature.warranty24': '24-month warranty',
                'pricing.feature.warrantyLifetime': 'Lifetime warranty',
                'pricing.feature.supportBasic': 'Basic technical support',
                'pricing.feature.supportPriority': 'Priority support',
                'pricing.feature.supportDedicated': 'Dedicated support team',
                'pricing.feature.analytics': 'Google Analytics integration',
                'pricing.feature.cms': 'Content management system',
                'pricing.feature.social': 'Social media integration',
                'pricing.feature.erp': 'ERP/CRM system integration',
                'pricing.feature.multilingual': 'Multi-language & multi-currency',
                'pricing.feature.security': 'High security',
                'pricing.feature.custom': 'Custom design on request',
                'pricing.feature.responsivePremium': 'Premium responsive design'
            }
        };
        
        // Current language
        let currentLang = localStorage.getItem('selectedLanguage') || 'vi';
        
        // Translation function
        function t(key) {
            return translations[currentLang][key] || translations['vi'][key] || key;
        }
        
        // Update text function
        function updateTexts() {
            $('[data-i18n]').each(function() {
                const key = $(this).data('i18n');
                $(this).text(t(key));
            });
            
            $('[data-i18n-placeholder]').each(function() {
                const key = $(this).data('i18n-placeholder');
                $(this).attr('placeholder', t(key));
            });
            
            // Update HTML lang attribute
            $('html').attr('lang', currentLang);
            
            // Update page title
            document.title = currentLang === 'vi' 
                ? 'WebFolio.vn - Thiết Kế Website Chuyên Nghiệp' 
                : 'WebFolio.vn - Professional Website Design';
        }
    </script>

    <!-- Header -->
    <header class="fixed w-full top-0 z-50 glass-effect shadow-lg">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="#home" class="text-2xl font-bold text-white">
                        <i class="fas fa-code mr-2 text-amber-300"></i>
                        WebFolio.vn
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-white hover:text-amber-300 transition-colors duration-300 font-medium" data-i18n="nav.home">Trang Chủ</a>
                    <a href="#services" class="text-white hover:text-amber-300 transition-colors duration-300 font-medium" data-i18n="nav.services">Dịch Vụ</a>
                    <a href="#portfolio" class="text-white hover:text-amber-300 transition-colors duration-300 font-medium" data-i18n="nav.portfolio">Portfolio</a>
                    <a href="#themes" class="text-white hover:text-amber-300 transition-colors duration-300 font-medium" data-i18n="nav.themes">Kho Theme</a>
                    <a href="#pricing" class="text-white hover:text-amber-300 transition-colors duration-300 font-medium" data-i18n="nav.pricing">Bảng Giá</a>
                    <a href="#about" class="text-white hover:text-amber-300 transition-colors duration-300 font-medium" data-i18n="nav.about">Về Chúng Tôi</a>
                    <a href="#contact" class="text-white hover:text-amber-300 transition-colors duration-300 font-medium" data-i18n="nav.contact">Liên Hệ</a>
                    
                    <!-- Language Switcher -->
                    <div class="lang-switcher">
                        <a id="lang-toggle" class="flex items-center space-x-2 text-white hover:text-amber-300 transition-colors duration-300">
                            <span class="flag-icon flag-vi" id="current-flag"></span>
                            <span id="current-lang">VI</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <div id="lang-dropdown" class="lang-dropdown hidden">
                            <a href="{{ route('locale.switch', ['locale' => 'vi']) }}" class="lang-option" data-lang="vi">
                                <span class="flag-icon flag-vi"></span>
                                <span>Tiếng Việt</span>
                            </a>
                            <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="lang-option" data-lang="en">
                                <span class="flag-icon flag-en"></span>
                                <span>English</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-4">
                    <!-- Mobile Language Switcher -->
                    <div class="lang-switcher">
                        <button id="mobile-lang-toggle" class="text-white hover:text-amber-300">
                            <span class="flag-icon flag-vi" id="mobile-current-flag"></span>
                        </button>
                        <div id="mobile-lang-dropdown" class="lang-dropdown hidden">
                            <div class="lang-option" data-lang="vi">
                                <span class="flag-icon flag-vi"></span>
                                <span>VI</span>
                            </div>
                            <div class="lang-option" data-lang="en">
                                <span class="flag-icon flag-en"></span>
                                <span>EN</span>
                            </div>
                        </div>
                    </div>
                    
                    <button id="mobile-menu-btn" class="text-white hover:text-amber-300 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="#home" class="text-white hover:text-amber-300 transition-colors duration-300 mobile-link font-medium" data-i18n="nav.home">Trang Chủ</a>
                    <a href="#services" class="text-white hover:text-amber-300 transition-colors duration-300 mobile-link font-medium" data-i18n="nav.services">Dịch Vụ</a>
                    <a href="#portfolio" class="text-white hover:text-amber-300 transition-colors duration-300 mobile-link font-medium" data-i18n="nav.portfolio">Portfolio</a>
                    <a href="#themes" class="text-white hover:text-amber-300 transition-colors duration-300 mobile-link font-medium" data-i18n="nav.themes">Kho Theme</a>
                    <a href="#pricing" class="text-white hover:text-amber-300 transition-colors duration-300 mobile-link font-medium" data-i18n="nav.pricing">Bảng Giá</a>
                    <a href="#about" class="text-white hover:text-amber-300 transition-colors duration-300 mobile-link font-medium" data-i18n="nav.about">Về Chúng Tôi</a>
                    <a href="#contact" class="text-white hover:text-amber-300 transition-colors duration-300 mobile-link font-medium" data-i18n="nav.contact">Liên Hệ</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="gradient-bg min-h-screen flex items-center pt-20 relative overflow-hidden">
        <!-- Background Banner -->
        <div class="absolute inset-0 opacity-10">
            <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" 
                 alt="Technology Background" 
                 class="w-full h-full object-cover">
        </div>
        
        <!-- Floating Geometric Shapes -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-amber-400 bg-opacity-20 rounded-full animate-pulse"></div>
        <div class="absolute top-40 right-20 w-16 h-16 bg-gray-400 bg-opacity-20 rotate-45 animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-40 left-20 w-12 h-12 bg-amber-300 bg-opacity-20 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-40 w-24 h-24 bg-slate-400 bg-opacity-20 rotate-12 animate-pulse" style="animation-delay: 0.5s;"></div>
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white fade-in">
                    <div class="mb-4">
                        <span class="bg-amber-600 bg-opacity-20 text-amber-200 px-4 py-2 rounded-full text-sm font-semibold backdrop-blur-sm border border-amber-400 border-opacity-30" data-i18n="hero.badge">
                            ⭐ #1 Công ty thiết kế website tại Việt Nam
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        <span data-i18n="hero.title">Thiết Kế Website</span>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-yellow-300" data-i18n="hero.highlight">Chuyên Nghiệp</span>
                    </h1>
                    <p class="text-xl mb-8 text-gray-200 leading-relaxed" data-i18n="hero.description">
                        Chúng tôi tạo ra những website hiện đại, responsive và tối ưu SEO để giúp doanh nghiệp của bạn thành công trong thời đại số.
                    </p>
                    
                    <!-- Features Preview -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-gray-200" data-i18n="hero.feature1">Responsive Design</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-rocket text-white text-sm"></i>
                            </div>
                            <span class="text-gray-200" data-i18n="hero.feature2">Tốc Độ Cao</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-amber-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-white text-sm"></i>
                            </div>
                            <span class="text-gray-200" data-i18n="hero.feature3">SEO Tối Ưu</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-slate-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-shield-alt text-white text-sm"></i>
                            </div>
                            <span class="text-gray-200" data-i18n="hero.feature4">Bảo Mật 24/7</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="trust-gradient hover:shadow-lg hover:shadow-blue-900/25 text-white px-8 py-4 rounded-lg font-semibold transition-all duration-300 hover-scale">
                            <i class="fas fa-rocket mr-2"></i>
                            <span data-i18n="hero.cta1">Bắt Đầu Ngay</span>
                        </button>
                        <button class="border-2 border-white text-white hover:bg-white hover:text-slate-800 px-8 py-4 rounded-lg font-semibold transition-all duration-300 backdrop-blur-sm">
                            <i class="fas fa-play mr-2"></i>
                            <span data-i18n="hero.cta2">Xem Portfolio</span>
                        </button>
                    </div>
                </div>
                <div class="fade-in">
                    <div class="relative">
                        <!-- Main Showcase -->
                        <div class="bg-white p-8 rounded-2xl shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                            <div class="bg-gradient-to-br from-blue-50 to-purple-50 h-64 rounded-lg overflow-hidden relative">
                                <img src="https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2274&q=80" 
                                     alt="Professional Website Design Dashboard" 
                                     class="w-full h-full object-cover rounded-lg">
                                <div class="absolute inset-0 bg-gradient-to-t from-blue-600/30 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 text-white">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-code text-xl"></i>
                                        <span class="font-semibold">Modern Dashboard</span>
                                    </div>
                                </div>
                                <div class="absolute top-4 right-4 bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold">
                                    Live
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="h-4 bg-gradient-to-r from-blue-200 to-purple-200 rounded mb-2"></div>
                                <div class="h-4 bg-gradient-to-r from-blue-200 to-purple-200 rounded w-3/4"></div>
                            </div>
                        </div>
                        
                        <!-- Floating Device Mockups -->
                        <div class="absolute -top-8 -right-8 bg-white p-4 rounded-xl shadow-lg transform rotate-12 hover:rotate-6 transition-transform duration-300">
                            <div class="w-20 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-white text-lg"></i>
                            </div>
                            <div class="text-xs text-gray-600 mt-1 text-center">Mobile</div>
                        </div>
                        
                        <div class="absolute -bottom-8 -left-8 bg-white p-4 rounded-xl shadow-lg transform -rotate-12 hover:-rotate-6 transition-transform duration-300">
                            <div class="w-20 h-12 bg-gradient-to-r from-purple-400 to-pink-500 rounded flex items-center justify-center">
                                <i class="fas fa-tablet-alt text-white text-lg"></i>
                            </div>
                            <div class="text-xs text-gray-600 mt-1 text-center">Tablet</div>
                        </div>
                        
                        <!-- Floating Elements -->
                        <div class="absolute -top-4 -right-4 w-8 h-8 bg-blue-500 rounded-full animate-pulse"></div>
                        <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-purple-500 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                        <div class="absolute top-1/2 -right-6 w-4 h-4 bg-pink-500 rotate-45 animate-pulse" style="animation-delay: 1s;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Banner -->
        <div class="absolute bottom-0 left-0 right-0 bg-white bg-opacity-10 backdrop-blur-md border-t border-white border-opacity-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-white">
                    <div>
                        <div class="text-2xl font-bold">100+</div>
                        <div class="text-sm text-gray-300" data-i18n="hero.stats.projects">Dự Án</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">50+</div>
                        <div class="text-sm text-gray-300" data-i18n="hero.stats.clients">Khách Hàng</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">5+</div>
                        <div class="text-sm text-gray-300" data-i18n="hero.stats.experience">Năm Kinh Nghiệm</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">24/7</div>
                        <div class="text-sm text-gray-300" data-i18n="hero.stats.support">Hỗ Trợ</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl font-bold text-gray-800 mb-4" data-i18n="services.title">Dịch Vụ Của Chúng Tôi</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto" data-i18n="services.subtitle">
                    Chúng tôi cung cấp giải pháp toàn diện cho nhu cầu thiết kế và phát triển website của bạn
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-2xl hover-scale fade-in border border-gray-100">
                    <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4" data-i18n="services.responsive.title">Responsive Design</h3>
                    <p class="text-gray-600 leading-relaxed" data-i18n="services.responsive.desc">
                        Website tự động thích ứng với mọi thiết bị từ mobile, tablet đến desktop, đảm bảo trải nghiệm tốt nhất cho người dùng.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover-scale fade-in border border-gray-100">
                    <div class="bg-emerald-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-search text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4" data-i18n="services.seo.title">SEO Tối Ưu</h3>
                    <p class="text-gray-600 leading-relaxed" data-i18n="services.seo.desc">
                        Tối ưu hóa website để đạt thứ hạng cao trên Google, tăng khả năng tiếp cận khách hàng tiềm năng.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover-scale fade-in border border-gray-100">
                    <div class="bg-amber-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-bolt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4" data-i18n="services.speed.title">Tốc Độ Cao</h3>
                    <p class="text-gray-600 leading-relaxed" data-i18n="services.speed.desc">
                        Website được tối ưu hóa để tải nhanh, cải thiện trải nghiệm người dùng và thứ hạng SEO.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover-scale fade-in border border-gray-100">
                    <div class="bg-slate-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4" data-i18n="services.security.title">Bảo Mật Cao</h3>
                    <p class="text-gray-600 leading-relaxed" data-i18n="services.security.desc">
                        Áp dụng các biện pháp bảo mật tiên tiến để bảo vệ website và dữ liệu khách hàng.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover-scale fade-in border border-gray-100">
                    <div class="bg-orange-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-cogs text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4" data-i18n="services.management.title">Quản Lý Dễ Dàng</h3>
                    <p class="text-gray-600 leading-relaxed" data-i18n="services.management.desc">
                        Giao diện quản trị thân thiện, dễ sử dụng, giúp bạn cập nhật nội dung một cách nhanh chóng.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover-scale fade-in border border-gray-100">
                    <div class="bg-indigo-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-headset text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4" data-i18n="services.support.title">Hỗ Trợ 24/7</h3>
                    <p class="text-gray-600 leading-relaxed" data-i18n="services.support.desc">
                        Đội ngũ hỗ trợ kỹ thuật luôn sẵn sàng giúp đỡ bạn mọi lúc, mọi nơi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="py-20 bg-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl font-bold text-gray-800 mb-4" data-i18n="portfolio.title">Portfolio</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto" data-i18n="portfolio.subtitle">
                    Khám phá những dự án website chúng tôi đã thực hiện thành công
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" 
                             alt="E-Commerce Website" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 right-4 bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            E-Commerce
                        </div>
                        <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Fashion Store Online</h3>
                        <p class="text-gray-600 mb-4">Website bán hàng thời trang với giao diện hiện đại, tích hợp thanh toán trực tuyến và quản lý đơn hàng thông minh.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">React</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Laravel</span>
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">MySQL</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500"><span data-i18n="portfolio.completed">Hoàn thành</span>: 03/2024</span>
                            <button class="text-blue-500 hover:text-blue-700 font-semibold" data-i18n="portfolio.viewDetails">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2274&q=80" 
                             alt="TechCorp Solutions Dashboard" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Corporate
                        </div>
                        <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">TechCorp Solutions</h3>
                        <p class="text-gray-600 mb-4">Website doanh nghiệp công nghệ với thiết kế chuyên nghiệp, tích hợp CRM và hệ thống quản lý dự án nội bộ.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Vue.js</span>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Laravel</span>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">MySQL</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500"><span data-i18n="portfolio.completed">Hoàn thành</span>: 01/2024</span>
                            <button class="text-blue-500 hover:text-blue-700 font-semibold" data-i18n="portfolio.viewDetails">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2274&q=80" 
                             alt="Educational Platform" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 right-4 bg-purple-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Education
                        </div>
                        <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">EduLearn Platform</h3>
                        <p class="text-gray-600 mb-4">Nền tảng học trực tuyến với video streaming, quiz tương tác và hệ thống theo dõi tiến độ học tập.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Next.js</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Express</span>
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs">PostgreSQL</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500"><span data-i18n="portfolio.completed">Hoàn thành</span>: 12/2023</span>
                            <button class="text-blue-500 hover:text-blue-700 font-semibold" data-i18n="portfolio.viewDetails">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1556761175-b413da4baf72?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2274&q=80" 
                             alt="Restaurant Website" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Restaurant
                        </div>
                        <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Golden Spoon Restaurant</h3>
                        <p class="text-gray-600 mb-4">Website nhà hàng cao cấp với menu trực tuyến, đặt bàn tự động và tích hợp thanh toán đa dạng.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">WordPress</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">WooCommerce</span>
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Custom PHP</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500"><span data-i18n="portfolio.completed">Hoàn thành</span>: 11/2023</span>
                            <button class="text-blue-500 hover:text-blue-700 font-semibold" data-i18n="portfolio.viewDetails">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" 
                             alt="Healthcare Website" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 right-4 bg-teal-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Healthcare
                        </div>
                        <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">MediCare Clinic</h3>
                        <p class="text-gray-600 mb-4">Hệ thống quản lý phòng khám với đặt lịch online, quản lý bệnh án và telemedicine tích hợp.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">NextJS</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Laravel</span>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">MySQL</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500"><span data-i18n="portfolio.completed">Hoàn thành</span>: 10/2023</span>
                            <button class="text-blue-500 hover:text-blue-700 font-semibold" data-i18n="portfolio.viewDetails">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2015&q=80" 
                             alt="Fintech App" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 right-4 bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Fintech
                        </div>
                        <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-external-link-alt"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">PayFast Digital Wallet</h3>
                        <p class="text-gray-600 mb-4">Ứng dụng ví điện tử với bảo mật cao, chuyển tiền nhanh và tích hợp đa dạng dịch vụ tài chính.</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">React Native</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Laravel</span>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Redis</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500"><span data-i18n="portfolio.completed">Hoàn thành</span>: 09/2023</span>
                            <button class="text-blue-500 hover:text-blue-700 font-semibold" data-i18n="portfolio.viewDetails">Xem Chi Tiết</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolio CTA -->
            <div class="text-center mt-12 fade-in">
                <p class="text-gray-600 mb-6" data-i18n="portfolio.viewMore">Bạn muốn xem thêm dự án của chúng tôi?</p>
                <button class="trust-gradient hover:shadow-lg hover:shadow-slate-900/25 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-300 hover-scale" data-i18n="portfolio.viewAll">
                    Xem Tất Cả Portfolio
                </button>
            </div>
        </div>
    </section>

    <!-- Theme Store Section -->
    <section id="themes" class="py-20 bg-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl font-bold text-slate-800 mb-4" data-i18n="themes.title">Kho Theme HTML</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto" data-i18n="themes.subtitle">
                    Bộ sưu tập theme HTML chuyên nghiệp, responsive và dễ tùy chỉnh cho mọi loại hình doanh nghiệp
                </p>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap justify-center gap-4 mb-12 fade-in">
                <button class="theme-filter active bg-slate-600 text-white px-6 py-2 rounded-full font-medium transition-all duration-300" data-filter="all" data-i18n="themes.filter.all">
                    Tất Cả
                </button>
                <button class="theme-filter bg-white text-slate-600 hover:bg-slate-600 hover:text-white px-6 py-2 rounded-full font-medium transition-all duration-300 border border-slate-200" data-filter="business" data-i18n="themes.filter.business">
                    Doanh Nghiệp
                </button>
                <button class="theme-filter bg-white text-slate-600 hover:bg-slate-600 hover:text-white px-6 py-2 rounded-full font-medium transition-all duration-300 border border-slate-200" data-filter="ecommerce" data-i18n="themes.filter.ecommerce">
                    Bán Hàng
                </button>
                <button class="theme-filter bg-white text-slate-600 hover:bg-slate-600 hover:text-white px-6 py-2 rounded-full font-medium transition-all duration-300 border border-slate-200" data-filter="portfolio" data-i18n="themes.filter.portfolio">
                    Portfolio
                </button>
                <button class="theme-filter bg-white text-slate-600 hover:bg-slate-600 hover:text-white px-6 py-2 rounded-full font-medium transition-all duration-300 border border-slate-200" data-filter="blog" data-i18n="themes.filter.blog">
                    Blog
                </button>
                <button class="theme-filter bg-white text-slate-600 hover:bg-slate-600 hover:text-white px-6 py-2 rounded-full font-medium transition-all duration-300 border border-slate-200" data-filter="landing" data-i18n="themes.filter.landing">
                    Landing Page
                </button>
            </div>

            <!-- Themes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="themes-grid">
                <!-- Business Theme 1 -->
                <div class="theme-item bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group" data-category="business">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2015&q=80" 
                             alt="Corporate Pro Theme" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold" data-i18n="themes.filter.business">
                            Doanh Nghiệp
                        </div>
                        <div class="absolute top-4 right-4 bg-amber-500 text-white px-2 py-1 rounded text-xs font-bold" data-i18n="themes.hot">
                            HOT
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="flex space-x-2">
                                <button class="bg-white text-slate-800 px-4 py-2 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors duration-300 flex-1">
                                    <i class="fas fa-eye mr-1"></i> <span data-i18n="themes.viewDemo">Xem Demo</span>
                                </button>
                                <button class="trust-gradient text-white px-4 py-2 rounded-lg font-medium text-sm hover:shadow-lg transition-all duration-300 flex-1">
                                    <i class="fas fa-download mr-1"></i> <span data-i18n="themes.download">Tải Về</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Corporate Pro</h3>
                        <p class="text-gray-600 mb-4 text-sm">Theme doanh nghiệp chuyên nghiệp với thiết kế hiện đại và đầy đủ tính năng.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-1">
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <span class="text-sm text-gray-500 ml-2">(4.9)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-slate-800">2.500.000₫</span>
                                <div class="text-sm text-gray-500 line-through">3.500.000₫</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- E-commerce Theme -->
                <div class="theme-item bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group" data-category="ecommerce">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" 
                             alt="ShopMax E-commerce Theme" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 left-4 bg-emerald-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            E-Commerce
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="flex space-x-2">
                                <button class="bg-white text-slate-800 px-4 py-2 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors duration-300 flex-1">
                                    <i class="fas fa-eye mr-1"></i> <span data-i18n="themes.viewDemo">Xem Demo</span>
                                </button>
                                <button class="trust-gradient text-white px-4 py-2 rounded-lg font-medium text-sm hover:shadow-lg transition-all duration-300 flex-1">
                                    <i class="fas fa-download mr-1"></i> <span data-i18n="themes.download">Tải Về</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">ShopMax</h3>
                        <p class="text-gray-600 mb-4 text-sm">Template bán hàng online với giỏ hàng, thanh toán và quản lý đơn hàng.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-1">
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-gray-300"></i>
                                <span class="text-sm text-gray-500 ml-2">(4.7)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-slate-800">3.200.000₫</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Theme -->
                <div class="theme-item bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group" data-category="portfolio">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1558655146-9f40138edfeb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2064&q=80" 
                             alt="Creative Portfolio Theme" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 left-4 bg-purple-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Portfolio
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="flex space-x-2">
                                <button class="bg-white text-slate-800 px-4 py-2 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors duration-300 flex-1">
                                    <i class="fas fa-eye mr-1"></i> <span data-i18n="themes.viewDemo">Xem Demo</span>
                                </button>
                                <button class="trust-gradient text-white px-4 py-2 rounded-lg font-medium text-sm hover:shadow-lg transition-all duration-300 flex-1">
                                    <i class="fas fa-download mr-1"></i> <span data-i18n="themes.download">Tải Về</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Creative Portfolio</h3>
                        <p class="text-gray-600 mb-4 text-sm">Theme portfolio sáng tạo cho designer, photographer và nghệ sĩ.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-1">
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <span class="text-sm text-gray-500 ml-2">(5.0)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-slate-800">1.800.000₫</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blog Theme -->
                <div class="theme-item bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group" data-category="blog">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" 
                             alt="BlogMaster Theme" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 left-4 bg-orange-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Blog
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="flex space-x-2">
                                <button class="bg-white text-slate-800 px-4 py-2 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors duration-300 flex-1">
                                    <i class="fas fa-eye mr-1"></i> <span data-i18n="themes.viewDemo">Xem Demo</span>
                                </button>
                                <button class="trust-gradient text-white px-4 py-2 rounded-lg font-medium text-sm hover:shadow-lg transition-all duration-300 flex-1">
                                    <i class="fas fa-download mr-1"></i> <span data-i18n="themes.download">Tải Về</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">BlogMaster</h3>
                        <p class="text-gray-600 mb-4 text-sm">Template blog hiện đại với tính năng SEO và social sharing tối ưu.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-1">
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-gray-300"></i>
                                <span class="text-sm text-gray-500 ml-2">(4.6)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-slate-800">1.200.000₫</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Landing Page Theme -->
                <div class="theme-item bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group" data-category="landing">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2015&q=80" 
                             alt="LandingPro Theme" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 left-4 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Landing Page
                        </div>
                        <div class="absolute top-4 right-4 bg-green-500 text-white px-2 py-1 rounded text-xs font-bold" data-i18n="themes.new">
                            MỚI
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="flex space-x-2">
                                <button class="bg-white text-slate-800 px-4 py-2 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors duration-300 flex-1">
                                    <i class="fas fa-eye mr-1"></i> <span data-i18n="themes.viewDemo">Xem Demo</span>
                                </button>
                                <button class="trust-gradient text-white px-4 py-2 rounded-lg font-medium text-sm hover:shadow-lg transition-all duration-300 flex-1">
                                    <i class="fas fa-download mr-1"></i> <span data-i18n="themes.download">Tải Về</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">LandingPro</h3>
                        <p class="text-gray-600 mb-4 text-sm">Landing page conversion cao với A/B testing và analytics tích hợp.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-1">
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <span class="text-sm text-gray-500 ml-2">(4.8)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-slate-800">2.800.000₫</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Theme 2 -->
                <div class="theme-item bg-white rounded-2xl overflow-hidden shadow-lg hover-scale fade-in group" data-category="business">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2340&q=80" 
                             alt="Medical Pro Theme" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="absolute top-4 left-4 bg-teal-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Y Tế
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="flex space-x-2">
                                <button class="bg-white text-slate-800 px-4 py-2 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors duration-300 flex-1">
                                    <i class="fas fa-eye mr-1"></i> <span data-i18n="themes.viewDemo">Xem Demo</span>
                                </button>
                                <button class="trust-gradient text-white px-4 py-2 rounded-lg font-medium text-sm hover:shadow-lg transition-all duration-300 flex-1">
                                    <i class="fas fa-download mr-1"></i> <span data-i18n="themes.download">Tải Về</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Medical Pro</h3>
                        <p class="text-gray-600 mb-4 text-sm">Template chuyên dụng cho phòng khám, bệnh viện với đặt lịch online.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-1">
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-amber-400"></i>
                                <i class="fas fa-star text-gray-300"></i>
                                <span class="text-sm text-gray-500 ml-2">(4.5)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-slate-800">2.200.000₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-12 fade-in">
                <button class="trust-gradient hover:shadow-lg hover:shadow-slate-900/25 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-300 hover-scale">
                    <i class="fas fa-plus mr-2"></i>
                    <span data-i18n="themes.loadMore">Xem Thêm Theme</span>
                </button>
            </div>

            <!-- Theme Features -->
            <div class="mt-20 fade-in">
                <div class="bg-gradient-to-r from-slate-50 to-blue-50 rounded-2xl p-8">
                    <h3 class="text-3xl font-bold text-slate-800 text-center mb-8" data-i18n="themes.whyChoose">Tại Sao Chọn Theme Của Chúng Tôi?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-mobile-alt text-2xl text-white"></i>
                            </div>
                            <h4 class="text-xl font-bold text-slate-800 mb-3" data-i18n="themes.feature1.title">100% Responsive</h4>
                            <p class="text-gray-600" data-i18n="themes.feature1.desc">Tất cả theme đều tối ưu hoàn hảo trên mọi thiết bị từ mobile đến desktop.</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-emerald-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-code text-2xl text-white"></i>
                            </div>
                            <h4 class="text-xl font-bold text-slate-800 mb-3" data-i18n="themes.feature2.title">Code Sạch & Tối Ưu</h4>
                            <p class="text-gray-600" data-i18n="themes.feature2.desc">Mã nguồn được viết theo chuẩn W3C, tải nhanh và dễ tùy chỉnh.</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-amber-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-headset text-2xl text-white"></i>
                            </div>
                            <h4 class="text-xl font-bold text-slate-800 mb-3" data-i18n="themes.feature3.title">Hỗ Trợ 24/7</h4>
                            <p class="text-gray-600" data-i18n="themes.feature3.desc">Nhận hỗ trợ kỹ thuật và cập nhật miễn phí trọn đời cho tất cả theme.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl font-bold text-slate-800 mb-4" data-i18n="pricing.title">Bảng Giá Dịch Vụ</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto" data-i18n="pricing.subtitle">
                    Chọn gói dịch vụ phù hợp với nhu cầu và ngân sách của bạn
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Basic Plan -->
                <div class="bg-gray-50 rounded-2xl p-8 hover-scale fade-in border border-gray-200 relative">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-slate-800 mb-2" data-i18n="pricing.basic.title">Gói Cơ Bản</h3>
                        <p class="text-gray-600 mb-6" data-i18n="pricing.basic.desc">Phù hợp cho cá nhân và doanh nghiệp nhỏ</p>
                        <div class="mb-8">
                            <span class="text-4xl font-bold text-slate-800">5.990.000₫</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.responsive">Thiết kế responsive trên mọi thiết bị</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.pages5">Tối đa 5 trang nội dung</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.seoBasic">Tối ưu SEO cơ bản</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.contactForm">Form liên hệ đơn giản</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.warranty12">Bảo hành 12 tháng</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.supportBasic">Hỗ trợ kỹ thuật cơ bản</span>
                        </div>
                    </div>
                    
                    <button class="w-full border-2 border-slate-600 text-slate-600 hover:bg-slate-600 hover:text-white py-3 px-6 rounded-lg font-semibold transition-all duration-300" data-i18n="pricing.choose">
                        Chọn Gói Này
                    </button>
                </div>

                <!-- Professional Plan -->
                <div class="trust-gradient rounded-2xl p-8 hover-scale fade-in relative transform scale-105 shadow-xl">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-amber-500 text-white px-4 py-2 rounded-full text-sm font-bold" data-i18n="pricing.pro.badge">PHỔ BIẾN NHẤT</span>
                    </div>
                    
                    <div class="text-center text-white">
                        <h3 class="text-2xl font-bold mb-2" data-i18n="pricing.pro.title">Gói Chuyên Nghiệp</h3>
                        <p class="text-gray-200 mb-6" data-i18n="pricing.pro.desc">Lựa chọn tốt nhất cho doanh nghiệp</p>
                        <div class="mb-8">
                            <span class="text-4xl font-bold">12.990.000₫</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 mb-8 text-white">
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.responsivePremium">Thiết kế responsive cao cấp</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.pages15">Tối đa 15 trang nội dung</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.seoAdvanced">Tối ưu SEO chuyên sâu</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.analytics">Tích hợp Google Analytics</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.cms">Hệ thống quản trị nội dung</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.social">Tích hợp mạng xã hội</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.warranty24">Bảo hành 24 tháng</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-amber-300 mr-3 mt-1"></i>
                            <span data-i18n="pricing.feature.supportPriority">Hỗ trợ ưu tiên</span>
                        </div>
                    </div>
                    
                    <button class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 px-6 rounded-lg font-semibold transition-all duration-300 shadow-lg" data-i18n="pricing.choose">
                        Chọn Gói Này
                    </button>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-gray-50 rounded-2xl p-8 hover-scale fade-in border border-gray-200 relative">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-slate-800 mb-2" data-i18n="pricing.enterprise.title">Gói Doanh Nghiệp</h3>
                        <p class="text-gray-600 mb-6" data-i18n="pricing.enterprise.desc">Giải pháp toàn diện cho tập đoán lớn</p>
                        <div class="mb-8">
                            <span class="text-4xl font-bold text-slate-800" data-i18n="pricing.enterprise.price">Liên Hệ</span>
                            <span class="text-gray-500 block mt-1" data-i18n="pricing.enterprise.quote">Báo giá theo yêu cầu</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.custom">Thiết kế tùy theo yêu cầu</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.pagesUnlimited">Không giới hạn số trang</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.seoExpert">Tối ưu SEO chuyên gia</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.erp">Tích hợp hệ thống ERP/CRM</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.multilingual">Đa ngôn ngữ & đa tiền tệ</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.security">Bảo mật cao</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.warrantyLifetime">Bảo hành trọn đời</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check text-emerald-500 mr-3 mt-1"></i>
                            <span class="text-gray-700" data-i18n="pricing.feature.supportDedicated">Dedicated support team</span>
                        </div>
                    </div>
                    
                    <button class="w-full trust-gradient hover:shadow-lg hover:shadow-slate-900/25 text-white py-3 px-6 rounded-lg font-semibold transition-all duration-300" data-i18n="pricing.contact">
                        Liên Hệ Ngay
                    </button>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-16 text-center fade-in">
                <div class="bg-gradient-to-r from-blue-50 to-amber-50 rounded-2xl p-8 max-w-4xl mx-auto">
                    <h3 class="text-2xl font-bold text-slate-800 mb-4" data-i18n="pricing.commitment">Cam Kết Của Chúng Tôi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div>
                            <i class="fas fa-clock text-3xl text-amber-600 mb-3"></i>
                            <h4 class="font-semibold text-slate-800 mb-2" data-i18n="pricing.commitment1.title">Giao Hàng Đúng Hẹn</h4>
                            <p class="text-gray-600 text-sm" data-i18n="pricing.commitment1.desc">Cam kết hoàn thành dự án đúng timeline đã thỏa thuận</p>
                        </div>
                        <div>
                            <i class="fas fa-redo text-3xl text-emerald-600 mb-3"></i>
                            <h4 class="font-semibold text-slate-800 mb-2" data-i18n="pricing.commitment2.title">Sửa Đổi Không Giới Hạn</h4>
                            <p class="text-gray-600 text-sm" data-i18n="pricing.commitment2.desc">Điều chỉnh thiết kế cho đến khi bạn hoàn toàn hài lòng</p>
                        </div>
                        <div>
                            <i class="fas fa-medal text-3xl text-blue-600 mb-3"></i>
                            <h4 class="font-semibold text-slate-800 mb-2" data-i18n="pricing.commitment3.title">Chất Lượng Đảm Bảo</h4>
                            <p class="text-gray-600 text-sm" data-i18n="pricing.commitment3.desc">Hoàn tiền 100% nếu không hài lòng với chất lượng</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-16 max-w-4xl mx-auto fade-in">
                <h3 class="text-3xl font-bold text-slate-800 text-center mb-8" data-i18n="faq.title">Câu Hỏi Thường Gặp</h3>
                <div class="space-y-4">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-semibold text-slate-800 mb-2" data-i18n="faq.q1">Thời gian hoàn thành dự án là bao lâu?</h4>
                        <p class="text-gray-600" data-i18n="faq.a1">Gói Cơ Bản: 2-3 tuần, Gói Chuyên Nghiệp: 4-6 tuần, Gói Doanh Nghiệp: 8-12 tuần tùy thuộc vào độ phức tạp.</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-semibold text-slate-800 mb-2" data-i18n="faq.q2">Có được xem trước thiết kế trước khi thanh toán?</h4>
                        <p class="text-gray-600" data-i18n="faq.a2">Có, chúng tôi sẽ cung cấp mockup và wireframe để bạn xem trước và góp ý trước khi bắt đầu phát triển.</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-semibold text-slate-800 mb-2" data-i18n="faq.q3">Có hỗ trợ hosting và tên miền không?</h4>
                        <p class="text-gray-600" data-i18n="faq.a3">Chúng tôi hỗ trợ tư vấn và thiết lập hosting, tên miền. Chi phí hosting/domain sẽ được tính riêng theo nhà cung cấp.</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-semibold text-slate-800 mb-2" data-i18n="faq.q4">Sau khi hoàn thành có được chỉnh sửa không?</h4>
                        <p class="text-gray-600" data-i18n="faq.a4">Trong thời gian bảo hành, chúng tôi hỗ trợ sửa lỗi miễn phí. Các thay đổi về thiết kế/tính năng sẽ có chi phí riêng.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="fade-in">
                    <h2 class="text-4xl font-bold text-gray-800 mb-6" data-i18n="about.title">Về WebFolio.vn</h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed" data-i18n="about.desc1">
                        Với hơn 5 năm kinh nghiệm trong lĩnh vực thiết kế và phát triển website, WebFolio.vn đã trở thành đối tác tin cậy của hàng trăm doanh nghiệp tại Việt Nam.
                    </p>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed" data-i18n="about.desc2">
                        Chúng tôi chuyên tạo ra những website không chỉ đẹp mắt mà còn hiệu quả, giúp khách hàng đạt được mục tiêu kinh doanh trong thời đại số.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-500 mb-2">100+</div>
                            <div class="text-gray-600" data-i18n="about.stats1">Dự Án Hoàn Thành</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-500 mb-2">50+</div>
                            <div class="text-gray-600" data-i18n="about.stats2">Khách Hàng Hài Lòng</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-500 mb-2">5+</div>
                            <div class="text-gray-600" data-i18n="about.stats3">Năm Kinh Nghiệm</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-500 mb-2">24/7</div>
                            <div class="text-gray-600" data-i18n="about.stats4">Hỗ Trợ Khách Hàng</div>
                        </div>
                    </div>
                </div>
                
                <div class="fade-in">
                    <div class="trust-gradient p-8 rounded-2xl text-white">
                        <h3 class="text-2xl font-bold mb-6" data-i18n="about.whyChoose">Tại Sao Chọn Chúng Tôi?</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-emerald-400 mr-3 mt-1"></i>
                                <span data-i18n="about.reason1">Thiết kế responsive trên mọi thiết bị</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-emerald-400 mr-3 mt-1"></i>
                                <span data-i18n="about.reason2">Tối ưu SEO và tốc độ tải trang</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-emerald-400 mr-3 mt-1"></i>
                                <span data-i18n="about.reason3">Bảo hành và hỗ trợ dài hạn</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-emerald-400 mr-3 mt-1"></i>
                                <span data-i18n="about.reason4">Giá cả cạnh tranh, minh bạch</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-emerald-400 mr-3 mt-1"></i>
                                <span data-i18n="about.reason5">Bảo mật cao</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-emerald-400 mr-3 mt-1"></i>
                                <span data-i18n="about.reason6">Quản lý dễ dàng</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl font-bold text-gray-800 mb-4" data-i18n="contact.title">Liên Hệ Với Chúng Tôi</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto" data-i18n="contact.subtitle">
                    Sẵn sàng bắt đầu dự án website của bạn? Hãy liên hệ ngay để được tư vấn miễn phí
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="fade-in">
                    <div class="bg-white p-8 rounded-2xl shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6" data-i18n="contact.form.title">Gửi Tin Nhắn</h3>
                        <form id="contact-form" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="contact.form.name">Họ và Tên *</label>
                                <input type="text" name="name" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="contact.form.email">Email *</label>
                                <input type="email" name="email" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="contact.form.phone">Số Điện Thoại</label>
                                <input type="tel" name="phone" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="contact.form.service">Dịch Vụ Quan Tâm</label>
                                <select name="service" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="" data-i18n="contact.form.service.choose">Chọn dịch vụ</option>
                                    <option value="website-design" data-i18n="contact.form.service.design">Thiết Kế Website</option>
                                    <option value="ecommerce" data-i18n="contact.form.service.ecommerce">Website Bán Hàng</option>
                                    <option value="corporate" data-i18n="contact.form.service.corporate">Website Doanh Nghiệp</option>
                                    <option value="system" data-i18n="contact.form.service.system">Website hệ thống ERP/CRM</option>
                                    <option value="maintenance" data-i18n="contact.form.service.maintenance">Bảo Trì Website</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" data-i18n="contact.form.message">Tin Nhắn *</label>
                                <textarea name="message" rows="4" required 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          data-i18n-placeholder="contact.form.placeholder"
                                          placeholder="Mô tả chi tiết về dự án của bạn..."></textarea>
                            </div>
                            <button type="submit" 
                                    class="w-full trust-gradient hover:shadow-lg hover:shadow-slate-900/25 text-white py-3 px-6 rounded-lg font-semibold transition-colors duration-300"
                                    data-i18n="contact.form.submit">
                                Gửi Tin Nhắn
                            </button>
                        </form>
                    </div>
                </div>

                <div class="fade-in">
                    <div class="trust-gradient p-8 rounded-2xl text-white h-full">
                        <h3 class="text-2xl font-bold mb-8" data-i18n="contact.info.title">Thông Tin Liên Hệ</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-2xl mr-4 mt-1 text-amber-300"></i>
                                <div>
                                    <h4 class="font-semibold mb-1" data-i18n="contact.info.address">Địa Chỉ</h4>
                                    <p class="text-gray-200" data-i18n="contact.info.addressValue">123 Đường ABC, Quận 1, TP. Hồ Chí Minh</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-phone text-2xl mr-4 mt-1 text-amber-300"></i>
                                <div>
                                    <h4 class="font-semibold mb-1" data-i18n="contact.info.phone">Điện Thoại</h4>
                                    <p class="text-gray-200">+84 123 456 789</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-envelope text-2xl mr-4 mt-1 text-amber-300"></i>
                                <div>
                                    <h4 class="font-semibold mb-1" data-i18n="contact.info.email">Email</h4>
                                    <p class="text-gray-200">info@WebFolio.vn</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-clock text-2xl mr-4 mt-1 text-amber-300"></i>
                                <div>
                                    <h4 class="font-semibold mb-1" data-i18n="contact.info.hours">Giờ Làm Việc</h4>
                                    <p class="text-gray-200">
                                        <span data-i18n="contact.info.hoursValue1">Thứ 2 - Thứ 6: 8:00 - 18:00</span><br>
                                        <span data-i18n="contact.info.hoursValue2">Thứ 7: 8:00 - 12:00</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-500">
                            <h4 class="font-semibold mb-4" data-i18n="contact.social">Theo Dõi Chúng Tôi</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-code mr-2 text-amber-400"></i>
                        <span class="text-2xl font-bold">WebFolio.vn</span>
                    </div>
                    <p class="text-gray-400 mb-4 leading-relaxed" data-i18n="footer.desc">
                        Chuyên thiết kế và phát triển website chuyên nghiệp, hiện đại và tối ưu SEO. 
                        Đồng hành cùng doanh nghiệp trong chuyển đổi số.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors duration-300">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors duration-300">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors duration-300">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-amber-400 transition-colors duration-300">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4" data-i18n="footer.services">Dịch Vụ</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300" data-i18n="footer.services.design">Thiết Kế Website</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300" data-i18n="footer.services.ecommerce">Website Bán Hàng</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300" data-i18n="footer.services.corporate">Website Doanh Nghiệp</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300" data-i18n="footer.services.seo">Tối Ưu SEO</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300" data-i18n="footer.services.maintenance">Bảo Trì Website</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4" data-i18n="footer.contact">Liên Hệ</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>123 Đường ABC, Q.1, TP.HCM</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>+84 123 456 789</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>info@WebFolio.vn</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400" data-i18n="footer.copyright">
                    &copy; 2025 WebFolio.vn. Tất cả quyền được bảo lưu.
                </p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 trust-gradient hover:shadow-lg hover:shadow-slate-900/25 text-white w-12 h-12 rounded-full shadow-lg hidden transition-all duration-300 z-40">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- AI Chatbox -->
    <div id="ai-chatbox-container" class="fixed bottom-8 right-8 z-50">
        <!-- Chat Button -->
        <button id="chat-toggle" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
            <i class="fas fa-comments text-xl"></i>
            <span class="absolute top-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white animate-pulse"></span>
        </button>
        
        <!-- Chat Window -->
        <div id="chat-window" class="hidden absolute bottom-20 right-0 w-96 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl overflow-hidden transition-all duration-300 transform scale-95 opacity-0">
            <!-- Chat Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-blue-600 text-lg"></i>
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></span>
                    </div>
                    <div>
                        <h3 class="font-semibold" data-i18n="chat.title">AI Assistant</h3>
                        <p class="text-xs opacity-90" data-i18n="chat.subtitle">Chúng tôi sẵn sàng hỗ trợ bạn</p>
                    </div>
                </div>
                <button id="chat-close" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Chat Messages -->
            <div id="chat-messages" class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50">
                <!-- Welcome Message -->
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white text-sm"></i>
                    </div>
                    <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                        <p class="text-gray-800 text-sm" data-i18n="chat.welcome">Xin chào! Tôi là AI Assistant của WebFolio. Tôi có thể giúp bạn tìm hiểu về dịch vụ thiết kế website, báo giá, hoặc trả lời bất kỳ câu hỏi nào của bạn. Bạn cần hỗ trợ gì?</p>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <button class="quick-action bg-white hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full text-sm border border-gray-200 transition-colors duration-200" data-action="pricing">
                        <i class="fas fa-tag mr-1"></i> <span data-i18n="chat.quick.pricing">Xem bảng giá</span>
                    </button>
                    <button class="quick-action bg-white hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full text-sm border border-gray-200 transition-colors duration-200" data-action="services">
                        <i class="fas fa-briefcase mr-1"></i> <span data-i18n="chat.quick.services">Dịch vụ</span>
                    </button>
                    <button class="quick-action bg-white hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full text-sm border border-gray-200 transition-colors duration-200" data-action="contact">
                        <i class="fas fa-phone mr-1"></i> <span data-i18n="chat.quick.contact">Liên hệ tư vấn</span>
                    </button>
                    <button class="quick-action bg-white hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full text-sm border border-gray-200 transition-colors duration-200" data-action="portfolio">
                        <i class="fas fa-images mr-1"></i> <span data-i18n="chat.quick.portfolio">Xem portfolio</span>
                    </button>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form id="chat-form" class="flex items-center space-x-2">
                    <input type="text" 
                           id="chat-input" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Nhập tin nhắn..."
                           data-i18n-placeholder="chat.input.placeholder">
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white p-2 rounded-full transition-colors duration-200 w-10 h-10 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2 text-center" data-i18n="chat.powered">Powered by AI</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize language
            updateTexts();
            updateLanguageUI();
            
            // Language switcher functionality
            function updateLanguageUI() {
                if (currentLang === 'vi') {
                    $('#current-flag, #mobile-current-flag').removeClass('flag-en').addClass('flag-vi');
                    $('#current-lang').text('VI');
                } else {
                    $('#current-flag, #mobile-current-flag').removeClass('flag-vi').addClass('flag-en');
                    $('#current-lang').text('EN');
                }
                
                $('.lang-option').removeClass('active');
                $(`.lang-option[data-lang="${currentLang}"]`).addClass('active');
            }
            
            // Desktop language toggle
            $('#lang-toggle').click(function(e) {
                e.stopPropagation();
                $('#lang-dropdown').toggleClass('hidden');
            });
            
            // Mobile language toggle
            $('#mobile-lang-toggle').click(function(e) {
                e.stopPropagation();
                $('#mobile-lang-dropdown').toggleClass('hidden');
            });
            
            // Language selection
            $('.lang-option').click(function() {
                currentLang = $(this).data('lang');
                localStorage.setItem('selectedLanguage', currentLang);
                updateTexts();
                updateLanguageUI();
                $('#lang-dropdown, #mobile-lang-dropdown').addClass('hidden');
            });
            
            // Close language dropdown when clicking outside
            $(document).click(function() {
                $('#lang-dropdown, #mobile-lang-dropdown').addClass('hidden');
            });
            
            // Mobile menu toggle
            $('#mobile-menu-btn').click(function() {
                $('#mobile-menu').toggleClass('hidden');
            });

            // Close mobile menu when clicking on links
            $('.mobile-link').click(function() {
                $('#mobile-menu').addClass('hidden');
            });

            // Smooth scrolling for navigation links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 1000);
                }
            });

            // Fade in animation on scroll
            function fadeInOnScroll() {
                $('.fade-in').each(function() {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();

                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        $(this).addClass('visible');
                    }
                });
            }

            // Initial check for visible elements
            fadeInOnScroll();

            // Check on scroll
            $(window).scroll(function() {
                fadeInOnScroll();

                // Show/hide back to top button
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').removeClass('hidden');
                } else {
                    $('#back-to-top').addClass('hidden');
                }
            });

            // Back to top functionality
            $('#back-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });

            // Form validation and submission
            $('#contact-form').submit(function(e) {
                e.preventDefault();
                
                var name = $('input[name="name"]').val().trim();
                var email = $('input[name="email"]').val().trim();
                var message = $('textarea[name="message"]').val().trim();
                
                // Simple validation
                if (!name) {
                    alert(t('form.error.name'));
                    $('input[name="name"]').focus();
                    return false;
                }
                
                if (!email) {
                    alert(t('form.error.email'));
                    $('input[name="email"]').focus();
                    return false;
                }
                
                if (!isValidEmail(email)) {
                    alert(t('form.error.emailInvalid'));
                    $('input[name="email"]').focus();
                    return false;
                }
                
                if (!message) {
                    alert(t('form.error.message'));
                    $('textarea[name="message"]').focus();
                    return false;
                }
                
                // Simulate form submission
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.text();
                
                submitBtn.text(t('form.sending')).prop('disabled', true);
                
                setTimeout(function() {
                    alert(t('form.success'));
                    $('#contact-form')[0].reset();
                    submitBtn.text(originalText).prop('disabled', false);
                }, 2000);
            });

            // Email validation function
            function isValidEmail(email) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Header background on scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('header').addClass('backdrop-blur-md bg-opacity-90');
                } else {
                    $('header').removeClass('backdrop-blur-md bg-opacity-90');
                }
            });

            // Add some interactive hover effects
            $('.hover-scale').hover(
                function() {
                    $(this).addClass('transform scale-105');
                },
                function() {
                    $(this).removeClass('transform scale-105');
                }
            );

            // Theme filtering functionality
            $('.theme-filter').click(function() {
                var filter = $(this).data('filter');
                
                // Update active state
                $('.theme-filter').removeClass('active bg-slate-600 text-white').addClass('bg-white text-slate-600 border border-slate-200');
                $(this).removeClass('bg-white text-slate-600 border border-slate-200').addClass('active bg-slate-600 text-white');
                
                // Filter themes
                if (filter === 'all') {
                    $('.theme-item').fadeIn(300);
                } else {
                    $('.theme-item').hide();
                    $('.theme-item[data-category="' + filter + '"]').fadeIn(300);
                }
            });

            // Add loading effect for portfolio items
            $('.portfolio-item').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            // Add counter animation for statistics
            function animateCounters() {
                $('.counter').each(function() {
                    var $this = $(this);
                    var countTo = $this.attr('data-count');
                    
                    $({ countNum: $this.text() }).animate({
                        countNum: countTo
                    }, {
                        duration: 2000,
                        easing: 'linear',
                        step: function() {
                            $this.text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $this.text(this.countNum);
                        }
                    });
                });
            }

            // Trigger counter animation when about section is visible
            var aboutSection = $('#about');
            var hasAnimated = false;
            
            $(window).scroll(function() {
                if (!hasAnimated && aboutSection.length) {
                    var sectionTop = aboutSection.offset().top;
                    var sectionHeight = aboutSection.outerHeight();
                    var windowHeight = $(window).height();
                    var scrollTop = $(window).scrollTop();
                    
                    if (scrollTop + windowHeight > sectionTop + sectionHeight / 2) {
                        animateCounters();
                        hasAnimated = true;
                    }
                }
            });
            
            // Chatbox functionality
            var chatOpen = false;
            var chatMessages = $('#chat-messages');
            
            // Toggle chat window
            $('#chat-toggle').click(function() {
                if (!chatOpen) {
                    $('#chat-window').removeClass('hidden');
                    setTimeout(function() {
                        $('#chat-window').addClass('show');
                    }, 10);
                    chatOpen = true;
                    $('#chat-input').focus();
                } else {
                    $('#chat-window').removeClass('show');
                    setTimeout(function() {
                        $('#chat-window').addClass('hidden');
                    }, 300);
                    chatOpen = false;
                }
            });
            
            // Close chat window
            $('#chat-close').click(function() {
                $('#chat-window').removeClass('show');
                setTimeout(function() {
                    $('#chat-window').addClass('hidden');
                }, 300);
                chatOpen = false;
            });
            
            // Quick action buttons
            $('.quick-action').click(function() {
                var action = $(this).data('action');
                var userMessage = $(this).text().trim();
                addUserMessage(userMessage);
                
                // Show typing indicator
                showTypingIndicator();
                
                // Simulate AI response
                setTimeout(function() {
                    removeTypingIndicator();
                    var response = '';
                    switch(action) {
                        case 'pricing':
                            response = t('chat.responses.pricing');
                            break;
                        case 'services':
                            response = t('chat.responses.services');
                            break;
                        case 'contact':
                            response = t('chat.responses.contact');
                            break;
                        case 'portfolio':
                            response = t('chat.responses.portfolio');
                            break;
                        default:
                            response = t('chat.responses.default');
                    }
                    addBotMessage(response);
                }, 1000);
            });
            
            // Chat form submission
            $('#chat-form').submit(function(e) {
                e.preventDefault();
                var message = $('#chat-input').val().trim();
                if (!message) return;
                
                addUserMessage(message);
                $('#chat-input').val('');
                
                // Show typing indicator
                showTypingIndicator();
                
                // Simulate AI response
                setTimeout(function() {
                    removeTypingIndicator();
                    
                    // Simple keyword matching for demo
                    var response = '';
                    var lowerMessage = message.toLowerCase();
                    
                    if (lowerMessage.includes('giá') || lowerMessage.includes('price') || lowerMessage.includes('cost')) {
                        response = t('chat.responses.pricing');
                    } else if (lowerMessage.includes('dịch vụ') || lowerMessage.includes('service')) {
                        response = t('chat.responses.services');
                    } else if (lowerMessage.includes('liên hệ') || lowerMessage.includes('contact') || lowerMessage.includes('phone')) {
                        response = t('chat.responses.contact');
                    } else if (lowerMessage.includes('portfolio') || lowerMessage.includes('dự án') || lowerMessage.includes('project')) {
                        response = t('chat.responses.portfolio');
                    } else {
                        response = t('chat.responses.default');
                    }
                    
                    addBotMessage(response);
                }, 1500);
            });
            
            // Helper functions for chat
            function addUserMessage(message) {
                var messageHtml = `
                    <div class="flex items-start space-x-2 justify-end chat-message">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-3 rounded-lg shadow-sm max-w-[80%]">
                            <p class="text-sm">${escapeHtml(message)}</p>
                        </div>
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-gray-600 text-sm"></i>
                        </div>
                    </div>
                `;
                chatMessages.append(messageHtml);
                scrollToBottom();
            }
            
            function addBotMessage(message) {
                var formattedMessage = message.replace(/\n/g, '<br>');
                var messageHtml = `
                    <div class="flex items-start space-x-2 chat-message">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white text-sm"></i>
                        </div>
                        <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                            <p class="text-gray-800 text-sm">${formattedMessage}</p>
                        </div>
                    </div>
                `;
                chatMessages.append(messageHtml);
                scrollToBottom();
            }
            
            function showTypingIndicator() {
                var typingHtml = `
                    <div class="flex items-start space-x-2 typing-message">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white text-sm"></i>
                        </div>
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                `;
                chatMessages.append(typingHtml);
                scrollToBottom();
            }
            
            function removeTypingIndicator() {
                $('.typing-message').remove();
            }
            
            function scrollToBottom() {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }
            
            function escapeHtml(text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }
            
            // Update chatbox texts when language changes
            $('.lang-option').click(function() {
                currentLang = $(this).data('lang');
                localStorage.setItem('selectedLanguage', currentLang);
                updateTexts();
                updateLanguageUI();
                $('#lang-dropdown, #mobile-lang-dropdown').addClass('hidden');
                
                // Clear chat messages and show welcome message again
                chatMessages.empty();
                var welcomeHtml = `
                    <div class="flex items-start space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white text-sm"></i>
                        </div>
                        <div class="bg-white p-3 rounded-lg shadow-sm max-w-[80%]">
                            <p class="text-gray-800 text-sm">${t('chat.welcome')}</p>
                        </div>
                    </div>
                `;
                chatMessages.append(welcomeHtml);
            });
        });
    </script>
</body>
</html>