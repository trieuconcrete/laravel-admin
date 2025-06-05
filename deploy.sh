#!/bin/bash

cd /var/www/webroot/nguyentrieu || exit
git pull origin portfolio
cd src
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache