#!/usr/bin/env sh
cp .env.stg .env
composer install
composer du
php artisan cache:clear

php artisan mig:fre
php artisan sync:location
php artisan sync:store
php artisan db:seed

npm run dev
