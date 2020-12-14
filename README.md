
# BP Tracking

## Tech Specification

- Laravel 6.2
- Vue 2 + VueRouter + vue-progressbar + sweetalert2 + laravel-vue-pagination
- Laravel Passport
- Admin LTE 3 + Bootstrap 4 + Font Awesome 5
- PHPUnit Test Case/Test Coverage

## Installation

- `composer install`
- `cp .env.example .env`
- Update `.env` and set your database credentials
- `php artisan migrate:fresh`
- `php artisan sync:store`
- `php artisan db:seed`
- `php artisan passport:install`
- `php artisan storage:link`
- `npm install`
- `npm run dev`
- `php artisan serve`


## Start local dev
```
# start docker
docker-compose up -d
# start FE
docker exec -it bptracking_app npm run watch
# start BE
docker exec -it bptracking_app php artisan serve --host 0.0.0.0
```

#### Deployment
```
./deploy.sh &

# dont run db migrate on production
# if we need to change the db structure on prod
# dump a backup of data of prod
# get new structure from staging, edit dump file of prod, run mysql import to take the effect on new structure of prod db
```
