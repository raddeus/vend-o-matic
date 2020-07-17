# Coin Vendor

This is a demo laravel app

## Running Locally

This app comes with a docker-compose setup that should work out of the box.

On the host machine:

`docker-compose up -d`

You should now have app, web, redis, and mariadb containers running.

Now enter the app container

`docker-compose exec app bash`

Run the following commands inside of the app container

`composer install` - Install dependencies

`php artisan key:generate` - Generate an app key

`php artisan migrate` - Run database migrations

`vendor/bin/phpunit` - Run tests

Assuming tests pass you should have access to the api at `http://localhost/api`

## Postman

There is a postman collection bundled with the application at `coin-vendor.postman_collection.json`
