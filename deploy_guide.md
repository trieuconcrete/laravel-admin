
# Triển khai Laravel 12 trên Ubuntu 22.04 với Nginx, MySQL, PHP 8.4, Node.js

## ✅ Bước 1: Cấu hình môi trường ban đầu

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install ufw curl zip unzip git software-properties-common -y

# Cấu hình firewall (nếu cần)
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

## ✅ Bước 2: Cài PHP 8.4, MySQL, Nginx

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# PHP 8.4 và các extension cần thiết
sudo apt install php8.4 php8.4-fpm php8.4-mysql php8.4-curl php8.4-mbstring php8.4-xml php8.4-bcmath php8.4-zip php8.4-gd php8.4-intl -y

# MySQL và Nginx
sudo apt install mysql-server nginx -y
```

## ✅ Bước 3: Cài Composer và Node.js

```bash
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js (LTS)
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
```

## ✅ Bước 4: Clone và cài đặt Laravel project

```bash
cd /var/www/webroot
sudo git clone git@github.com:trieuconcrete/laravel-admin.git nguyentrieu
cd nguyentrieu/src
composer install
cp .env.example .env
php artisan key:generate
```

## ✅ Bước 5: Tạo Database và cấu hình `.env`

```bash
sudo mysql -u root -p
CREATE DATABASE nguyentrieu_db;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'password@123';
GRANT ALL PRIVILEGES ON nguyentrieu_db.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Cập nhật file `.env`:

```
DB_DATABASE=nguyentrieu_db
DB_USERNAME=laravel_user
DB_PASSWORD=password@123
```

## ✅ Bước 6: Set quyền thư mục

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## ✅ Bước 7: Build frontend với Vite

```bash
npm install
npm run build
```

## ✅ Bước 8: Cấu hình Nginx

```bash
sudo nano /etc/nginx/sites-available/nguyentrieu.config
```

Nội dung cấu hình:

```nginx
server {
    server_name nguyentrieu.dev;

    root /var/www/webroot/nguyentrieu/src/public;
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

    # Redirect /multichannel-sales-platform → /multichannel-sales-platform/
    location = /multichannel-sales-platform {
        return 301 /multichannel-sales-platform/;
    }
    location /multichannel-sales-platform/ {
        root /var/www/html/;
        index index.html;
        try_files $uri $uri/ =404;
    }

    # Redirect /prompt → /prompt/
    location = /prompt {
        return 301 /prompt/;
    }
    location /prompt/ {
        root /var/www/html/;
        index index.html;
        try_files $uri $uri/ =404;
    }
}
```

Kích hoạt site:

```bash
sudo ln -s /etc/nginx/sites-available/nguyentrieu.config /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## ✅ Bước 9: Cài HTTPS với Let's Encrypt (nếu có domain)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com
```

## ✅ Bước 10: Tạo systemd service cho queue worker (nếu cần)

```bash
sudo nano /etc/systemd/system/laravel-worker.service
```

Nội dung:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reexec
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
```

## ✅ Bước 11: Tạo script `deploy.sh`

```bash
nano /var/www/webroot/nguyentrieu/deploy.sh
```

Nội dung:

```bash
#!/bin/bash

cd /var/www/webroot/nguyentrieu || exit
git pull origin portfolio
cd src
composer install --no-dev
php artisan migrate --force
# npm install && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
```

Phân quyền:

```bash
chmod +x /var/www/webroot/nguyentrieu/deploy.sh
```

## ✅ Bước 12: Restart services khi cần

```bash
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx
sudo systemctl restart laravel-worker
```

---