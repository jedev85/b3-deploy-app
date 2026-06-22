IMAGE ?= deploylab-local
PORT ?= 8000
UID := $(shell id -u)
GID := $(shell id -g)
DOCKER_RUN = docker run --rm --user $(UID):$(GID) --env COMPOSER_HOME=/tmp/composer --volume $(PWD):/app --workdir /app

.PHONY: build install composer console migrate fixtures test serve shell clean

build:
	docker build -t $(IMAGE) .

install: build
	$(DOCKER_RUN) $(IMAGE) composer install

composer: build
	$(DOCKER_RUN) $(IMAGE) composer $(ARGS)

console: build
	$(DOCKER_RUN) $(IMAGE) php bin/console $(ARGS)

migrate: build
	$(DOCKER_RUN) $(IMAGE) php bin/console doctrine:migrations:migrate --no-interaction

fixtures: build
	$(DOCKER_RUN) $(IMAGE) php bin/console doctrine:fixtures:load --no-interaction

test: build
	$(DOCKER_RUN) $(IMAGE) php bin/phpunit

serve: build
	docker run --rm -it --user $(UID):$(GID) --env COMPOSER_HOME=/tmp/composer --volume $(PWD):/app --workdir /app --publish $(PORT):8000 $(IMAGE)

shell: build
	docker run --rm -it --user $(UID):$(GID) --env COMPOSER_HOME=/tmp/composer --volume $(PWD):/app --workdir /app $(IMAGE) bash

clean:
	docker image rm $(IMAGE)
