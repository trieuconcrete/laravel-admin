APP_NAME=LaravelAmin

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

start:
	docker-compose start

stop:
	docker-compose stop

restart:
	docker-compose down && docker-compose up -d

logs:
	docker-compose logs -f

bash:
	docker-compose exec app bash

artisan:
	docker-compose exec app php artisan $(cmd)

composer:
	docker-compose run --rm app composer $(cmd)

npm:
	docker-compose run --rm node npm run $(cmd)

migrate:
	docker-compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan db:seed

fresh:
	docker-compose exec app php artisan migrate:fresh --seed

key-generate:
	docker-compose exec app php artisan key:generate

permissions:
	sudo chown -R $$(id -u):$$(id -g) src/storage src/bootstrap/cache

mailhog:
	open http://localhost:8025

.PHONY: up down build restart logs bash artisan composer npm migrate fresh key-generate permissions mailhog
