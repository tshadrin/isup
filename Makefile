up: docker-up
init: docker-down docker-pull docker-build docker-up
down: docker-down

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

cli:
	docker-compose run --rm isup-php-cli php bin/console

isup-init: isup-composer-install

isup-composer-install:
	docker-compose run --rm isup-php-cli composer install


init-prod: env-init yarn-init composer-init
init-dev: env-init-dev yarn-init-dev composer-init-dev

env-init:
	cp .env.prod .env

yarn-init:
	yarn encore production
	yarn install --production

composer-init:
	composer install --no-dev

env-init-dev:
	cp .env.dev .env

yarn-init-dev:
	yarn install
	yarn encore dev

composer-init-dev:
	composer install
