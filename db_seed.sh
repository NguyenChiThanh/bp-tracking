#!/usr/bin/env sh
php artisan mig:fre
php artisan sync:location
php artisan sync:store
php artisan sync:brand
php artisan db:seed

