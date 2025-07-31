
.PHONY: build up down logs composer phpunit

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f

composer:
	docker compose run --rm php composer install

phpunit:
	docker compose run --rm php vendor/bin/phpunit

install: up composer

bash:
	$(EXECPHP) /bin/bash
