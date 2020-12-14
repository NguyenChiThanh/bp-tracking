#!/usr/bin/env sh
init()
{
    cp .env.prod .env
    composer install
    composer du
    php artisan cache:clear

    php artisan mig:fre
    php artisan sync:location
    php artisan sync:store
    php artisan sync:brand

    php artisan db:seed

    npm install
    npm run prod
}

deploy()
{
    # cp .env.stag .env
    git pull pmc master
    composer du
    npm run prod
    php artisan cache:clear
    php artisan view:clear
}

ACTION=$1
if [ "$ACTION" = "init" ]; then
    echo "initializing prod ..."
    init
else
    echo "deploying prod ..."
    deploy
fi
