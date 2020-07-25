
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

## Unit Test

#### run PHPUnit

```bash
# run PHPUnit all test cases
vendor/bin/phpunit
# or Feature test only
vendor/bin/phpunit --testsuite Feature
```

#### Code Coverage Report

```bash
# reports is a directory name
vendor/bin/phpunit --coverage-html reports/
```
A `reports` directory has been created for code coverage report. Open the dashboard.html.
