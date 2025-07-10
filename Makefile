start: dc-up composer-install composer-update db-setup
	docker exec ytdownloader-php-fpm /etc/init.d/supervisor start

restart: dc-down dc-up cache-clear 
	docker exec ytdownloader-php-fpm /etc/init.d/supervisor start

dc-up:
	docker-compose -f docker/docker-compose.yml up -d

dc-down:
	docker-compose -f docker/docker-compose.yml down

composer-install:
	docker exec ytdownloader-php-fpm composer install

composer-update:
	docker exec ytdownloader-php-fpm composer update

db-setup:
	docker exec ytdownloader-php-fpm php bin/console doctrine:database:create --if-not-exists
	docker exec ytdownloader-php-fpm php bin/console doctrine:migrations:migrate

cs-check:
	docker exec ytdownloader-php-fpm vendor/bin/php-cs-fixer fix --dry-run --diff --allow-risky=yes

cs-fix:
	docker exec ytdownloader-php-fpm vendor/bin/php-cs-fixer fix --allow-risky=yes

test:
	docker exec ytdownloader-php-fpm php bin/console doctrine:database:drop --if-exists --force --env=test
	docker exec ytdownloader-php-fpm php bin/console doctrine:database:create --env=test
	docker exec ytdownloader-php-fpm php bin/console doctrine:migrations:migrate --no-interaction --env=test
	docker exec ytdownloader-php-fpm php bin/console doctrine:fixtures:load --no-interaction --group=all --env=test
	docker exec ytdownloader-php-fpm php bin/phpunit

psalm:
	docker exec ytdownloader-php-fpm vendor/bin/psalm

docker-php:
	docker exec -it ytdownloader-php-fpm bash

docker-pgsql:
	docker exec -it ytdownloader-pgsql bash

cache-clear:
	rm -rf ./var/cache/

lint: cs-fix psalm
