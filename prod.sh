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

    # php artisan db:seed
    php artisan import:pos positions_prod.xlsx

    npm install
    npm run prod
}

deploy()
{
    cp .env.prod .env
    composer install
    composer du
    php artisan cache:clear

    npm install
    npm run prod
}

ACTION=$1
if [[ "$ACTION" == "init" ]]; then
    echo "initializing prod ..."
    init
else
    echo "deploying prod ..."
    deploy
fi
