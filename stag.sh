#!/usr/bin/env sh
# cp .env.stag .env
git pull pmc master
composer du
npm run dev
php artisan cache:clear
php artisan view:clear
