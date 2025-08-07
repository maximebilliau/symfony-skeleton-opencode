
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
	$(EXECPHP) vendor/bin/phpunit

install: up composer

install-test:
	$(EXECPHP) bin/console doctrine:database:drop --env=test --force && $(EXECPHP) bin/console doctrine:database:create --env=test && $(EXECPHP) bin/console doctrine:schema:create --env=test

bash:
	$(EXECPHP) /bin/bash

lint:
	$(EXECPHP) vendor/bin/ecs --fix && vendor/bin/rector process && $(EXECPHP) vendor/bin/ecs --fix && $(EXECPHP) vendor/bin/phpstan.phar analyse src tests --memory-limit=-1

reset-db:
	$(EXECPHP) bin/console doctrine:database:drop --force && $(EXECPHP) bin/console doctrine:database:create && $(EXECPHP) bin/console doctrine:schema:create
