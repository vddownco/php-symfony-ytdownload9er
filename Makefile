install:
	composer install

update:
	composer update

db-setup:
	docker exec ytdownloader-php-fpm php bin/console doctrine:database:create --if-not-exists
	docker exec ytdownloader-php-fpm php bin/console doctrine:migrations:migrate

start: install update db-setup
	docker exec ytdownloader-php-fpm /etc/init.d/supervisor start

cs-check:
	vendor/bin/php-cs-fixer fix --dry-run --diff --allow-risky=yes

cs-fix:
	vendor/bin/php-cs-fixer fix --allow-risky=yes

test:
	php bin/console doctrine:database:drop --if-exists --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:migrations:migrate --no-interaction --env=test
	php bin/console doctrine:fixtures:load --no-interaction --group=all --env=test
	php bin/phpunit

psalm:
	vendor/bin/psalm

docker-php:
	docker exec ytdownloader-php-fpm -it bash
