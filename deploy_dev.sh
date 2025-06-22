#!/bin/bash

cd /var/www/webroot/vantai_hoangphulong_dev || exit
git pull origin project/vantai_hpl
cd src
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache