up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear isup-clear docker-pull docker-build docker-up isup-init
test: isup-test
test-unit: isup-test-unit

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

isup-clear:
    docker run --rm -v ${PWD}/:/app --workdir=/app alpine rm -f .ready

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

isup-init: isup-composer-install isup-assets-install isup-ready

isup-composer-install:
	docker-compose run --rm isup-php-cli composer install

isup-assets-install:
	docker-compose run --rm isup-node yarn install
	docker-compose run --rm isup-node npm rebuild node-sass

isup-ready:
	docker run --rm -v ${PWD}/:/app --workdir=/app alpine touch .ready

isup-test:
	docker-compose run --rm isup-php-cli php bin/phpunit

isup-test-unit:
	docker-compose run --rm isup-php-cli php bin/phpunit --testsuite=unit

isup-assets-dev:
	docker-compose run --rm isup-node yarn encore dev

cli:
	docker-compose run --rm isup-php-cli php -v

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
