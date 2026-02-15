# Hướng dẫn Update Nginx Config trên Server

## Thông tin cấu hình

- **Domain**: webfolio.nguyentrieu.dev
- **Server IP**: 103.82.132.130
- **Port**: 8080
- **Document Root**: /var/www/webroot/webfolio/src/public

## Bước 1: SSH vào server

```bash
ssh user@103.82.132.130
```

## Bước 2: Backup config hiện tại

```bash
sudo cp /etc/nginx/sites-available/webfolio /etc/nginx/sites-available/webfolio.backup
```

## Bước 3: Chỉnh sửa config

```bash
sudo nano /etc/nginx/sites-available/webfolio
```

Nội dung config:

```nginx
server {
    listen 8080;
    server_name webfolio.nguyentrieu.dev;

    root /var/www/webroot/webfolio/src/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # HTML sites
    
    # Redirect /lmd → /lmd/
    location = /lmd {
        return 301 /lmd/;
    }
    
    location /lmd/ {
        root /var/www/html/;
        index index.html;
        try_files $uri $uri/ =404;
    }
}
```

## Bước 4: Kiểm tra cú pháp config

```bash
sudo nginx -t
```

## Bước 5: Reload Nginx

Nếu test thành công:

```bash
sudo systemctl reload nginx
```

hoặc

```bash
sudo service nginx reload
```

## Bước 6: Kiểm tra trạng thái

```bash
sudo systemctl status nginx
```

## Bước 7: Cấu hình DNS

Đảm bảo domain `webfolio.nguyentrieu.dev` đã được trỏ về IP `103.82.132.130`

## Bước 8: Test truy cập

```bash
curl -I http://webfolio.nguyentrieu.dev:8080
```

## Bước 9: Cài đặt SSL Certificate (Optional)

### 9.1. Cài đặt Certbot

```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx -y
```

### 9.2. Tạo SSL Certificate

```bash
sudo certbot --nginx -d webfolio.nguyentrieu.dev
```

Làm theo hướng dẫn:
- Nhập email
- Đồng ý Terms of Service
- Chọn redirect HTTP sang HTTPS (option 2)

### 9.3. Config HTTPS thủ công (nếu cần)

Hoặc tự cấu hình HTTPS:

```nginx
server {
    listen 80;
    server_name webfolio.nguyentrieu.dev;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name webfolio.nguyentrieu.dev;

    ssl_certificate /etc/letsencrypt/live/webfolio.nguyentrieu.dev/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/webfolio.nguyentrieu.dev/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    root /var/www/webroot/webfolio/src/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # HTML sites
    location = /lmd {
        return 301 /lmd/;
    }
    
    location /lmd/ {
        root /var/www/html/;
        index index.html;
        try_files $uri $uri/ =404;
    }
}
```

### 9.4. Test và Reload

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 9.5. Auto-renewal SSL

Kiểm tra auto-renewal:

```bash
sudo certbot renew --dry-run
```

Certbot sẽ tự động gia hạn certificate trước khi hết hạn.

### 9.6. Kiểm tra SSL

```bash
curl -I https://webfolio.nguyentrieu.dev
```

## Lưu ý

- Config đang listen port 8080 thay vì port 80 mặc định
- Nếu dùng SSL, nên chuyển sang port 80 và 443
- PHP-FPM socket: `/run/php/php8.4-fpm.sock`
- Có thêm static HTML site tại `/lmd/`
- SSL certificate tự động gia hạn mỗi 90 ngày

## Rollback nếu có lỗi

```bash
sudo cp /etc/nginx/sites-available/webfolio.backup /etc/nginx/sites-available/webfolio
sudo systemctl reload nginx
```
