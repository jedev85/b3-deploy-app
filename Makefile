DOCKER_COMPOSE ?= docker compose

.PHONY: install start stop migrate fixtures test

install:
	composer install

start:
	$(DOCKER_COMPOSE) up --build

stop:
	$(DOCKER_COMPOSE) down

migrate:
	php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	php bin/console doctrine:fixtures:load --no-interaction

test:
	php bin/phpunit
