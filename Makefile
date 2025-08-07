
EXECPHP = docker compose exec php

.PHONY: build up down logs composer phpunit lint lint-check ci-check install install-test bash reset-db

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
	@echo "Setting up test environment..."
	$(EXECPHP) bin/console doctrine:schema:drop --env=test --force --full-database || true
	$(EXECPHP) bin/console doctrine:schema:create --env=test
	$(EXECPHP) bin/console doctrine:fixtures:load --env=test --no-interaction
	@echo "Test environment ready!"

bash:
	$(EXECPHP) /bin/bash

lint:
	$(EXECPHP) vendor/bin/ecs --fix && $(EXECPHP) vendor/bin/rector process && $(EXECPHP) vendor/bin/ecs --fix && $(EXECPHP) vendor/bin/phpstan analyse --configuration=phpstan.dist.neon --memory-limit=512M

lint-check:
	$(EXECPHP) vendor/bin/ecs --config=ecs.php --no-progress-bar && $(EXECPHP) vendor/bin/rector process --config=rector.php --dry-run --no-progress-bar --memory-limit=512M && $(EXECPHP) vendor/bin/phpstan analyse --configuration=phpstan.dist.neon --memory-limit=512M

ci-check: lint-check phpunit

reset-db:
	$(EXECPHP) bin/console doctrine:database:drop --force && $(EXECPHP) bin/console doctrine:database:create && $(EXECPHP) bin/console doctrine:schema:create
