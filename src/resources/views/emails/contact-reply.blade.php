<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re: {{ $subject }}</title>
    <style>
        /* Reset styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        
        /* Base styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f4f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        
        /* Container styles */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        /* Header styles */
        .email-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
        }
        
        /* Content styles */
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 20px;
        }
        
        .message-box {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .original-message {
            background-color: #f1f5f9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #64748b;
        }
        
        /* Footer styles */
        .email-footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            text-decoration: none;
        }
        
        /* Responsive styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-content {
                padding: 20px 15px !important;
            }
        }
    </style>
</head>
<body>
    <div style="background-color: #f4f7fa; padding: 20px 0;">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <a href="{{ config('app.url') }}" class="logo">
                    Nguyen Trieu
                </a>
                <p style="color: #e0e7ff; margin-top: 10px; font-size: 16px;">
                    Full Stack Developer
                </p>
            </div>
            
            <!-- Content -->
            <div class="email-content">
                <h2 class="greeting">Xin chào {{ $contactName }}!</h2>
                
                <p style="color: #4a5568; line-height: 1.6; margin-bottom: 20px;">
                    Cảm ơn bạn đã liên hệ với tôi. Tôi đã nhận được tin nhắn của bạn và xin phản hồi như sau:
                </p>
                
                <!-- Reply Message -->
                <div class="message-box">
                    <p style="margin: 0; color: #2d3748; line-height: 1.8;">
                        {!! nl2br(e($replyMessage)) !!}
                    </p>
                </div>
                
                <!-- Original Message Reference -->
                <div style="margin-top: 30px;">
                    <p style="color: #718096; font-size: 14px; margin-bottom: 10px;">
                        <strong>Tin nhắn gốc của bạn:</strong>
                    </p>
                    <div class="original-message">
                        <p style="margin: 0 0 10px 0;">
                            <strong>Chủ đề:</strong> {{ $subject }}
                        </p>
                        <p style="margin: 0;">
                            {{ Str::limit($originalMessage, 200) }}
                        </p>
                    </div>
                </div>
                
                <!-- Call to Action -->
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ config('app.url') }}" 
                       style="display: inline-block; padding: 12px 30px; background-color: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600;">
                        Ghé thăm website
                    </a>
                </div>
                
                <!-- Additional Info -->
                <p style="color: #718096; font-size: 14px; line-height: 1.6; margin-top: 30px;">
                    Nếu bạn có bất kỳ câu hỏi nào khác, đừng ngần ngại liên hệ lại với tôi. 
                    Tôi luôn sẵn sàng hỗ trợ bạn!
                </p>
                
                <p style="color: #4a5568; margin-top: 30px;">
                    Trân trọng,<br>
                    <strong>{{ $adminName }}</strong>
                </p>
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <div class="social-links">
                    <a href="#" style="color: #718096;">
                        <img src="https://img.icons8.com/ios-filled/24/718096/github.png" alt="GitHub" width="24" height="24">
                    </a>
                    <a href="#" style="color: #718096;">
                        <img src="https://img.icons8.com/ios-filled/24/718096/linkedin.png" alt="LinkedIn" width="24" height="24">
                    </a>
                    <a href="#" style="color: #718096;">
                        <img src="https://img.icons8.com/ios-filled/24/718096/email.png" alt="Email" width="24" height="24">
                    </a>
                </div>
                
                <p style="color: #a0aec0; font-size: 12px; margin: 10px 0;">
                    © {{ date('Y') }} Nguyen Trieu. All rights reserved.
                </p>
                
                <p style="color: #a0aec0; font-size: 12px; margin: 10px 0;">
                    Bạn nhận được email này vì đã gửi tin nhắn qua form liên hệ trên website nguyentrieu.site
                </p>
                
                <p style="color: #cbd5e0; font-size: 11px; margin-top: 20px;">
                    Email này được gửi tự động. Vui lòng không reply trực tiếp vào email này.
                </p>
            </div>
        </div>
    </div>
</body>
</html>