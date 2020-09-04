#!/usr/bin/env sh
cp .env.prod .env
composer install
composer du
php artisan cache:clear

php artisan mig:fre
php artisan sync:location
php artisan sync:store
php artisan sync:brand

# php artisan db:seed
php artisan impo:posi positions_prod.xlsx

npm install
npm run build
