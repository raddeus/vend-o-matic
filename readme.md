# Vend-O-Matic

This is a demo laravel app

https://vend-o-matic.raddeus.com/

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

You will need to `npm install` and `npm run dev` if you want the front-end to work locally, as the docker workspace doesn't have npm (yet)

## Postman

There is a postman collection bundled with the application at `Vend-O-Matic.postman_collection.json`

Also https://www.getpostman.com/collections/3bcf08a19522f60d90c0
