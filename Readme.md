# Небольшой сервис для скачивания видео с youtube, rutube, vkontakte.

PHP 8, Symfony 7, docker, yt-dlp, norkunas/youtube-dl-php.

## Preview  
<img src="documentation/readmemd-images/1.jpg" alt="Login page" height="300"> <img src="documentation/readmemd-images/2.jpg" alt="Login page" height="300"> <img src="documentation/readmemd-images/3.jpg" alt="Login page" height="300"> <img src="documentation/readmemd-images/4.jpg" alt="Login page" height="300"> <img src="documentation/readmemd-images/5.jpg" alt="Login page" height="300">

## Полезное  
1. Запуск проекта:
``` bash
docker-compose -f docker/docker-compose.yaml up -d
docker exec ytdownloader-php-fpm composer install
docker exec ytdownloader-php-fpm composer update
```
Добавить файл .env.local с настройками mysql: login and password, hostname должен быть по названию контейнера с базой - 'ytdownloader-mysql'
``` bash
docker exec ytdownloader-php-fpm php bin/console doctrine:database:create
docker exec ytdownloader-php-fpm php bin/console doctrine:migrations:migrate
```
2. Создать нового юзера:
```php
php bin/console user:add <username>
``` 
3. Запуск тестов:
```bash
sh test.sh
```

## Todo:
1. ~Сделать скачивание видео с ютуба в фоновом режиме (с помощью очередей).~
2. ~Добавить инфу о скачивании видео в фоне.~
3. Использование кеша ютуба из браузера для избежания блокировки (ютуб может думать что сервис является ботом).
4. ~Пофиксить баг с плейлистами - если в имени плейлиста есть спец. символы, то может возникнуть проблема со скачиванием этого плейлиста.~
5. Добавить счетчик скачаных видео, статистику.
6. ~Написать тесты.~
7. ~Вынести логику из контроллеров в сервисы.~
8. Добавить health check
9. Добавить Api
10. Добавить телеграм бота
11. Добавить bash скрипт для автоматизации первоначальных настроек, создания базы, запуска мираций, запуска очередей.
