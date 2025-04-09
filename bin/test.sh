#!/bin/bash

php bin/console doctrine:database:drop --if-exists --force --env=test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --no-interaction --env=test
php bin/console doctrine:fixtures:load --no-interaction --group=all --env=test
php bin/phpunit
