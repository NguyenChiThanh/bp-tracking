#!/usr/bin/env sh
cp .env.dev .env
composer install
composer du
php artisan cache:clear

php artisan mig:fre
php artisan sync:location
php artisan sync:store
php artisan sync:brand
php artisan db:seed
php artisan passport:install

npm install
npm run dev

