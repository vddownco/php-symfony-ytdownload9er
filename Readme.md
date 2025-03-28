# Небольшой сервис для скачивания видео с youtube

PHP 8, Symfony 7, docker, yt-dlp, norkunas/youtube-dl-php.

<img src="documentation/readmemd-images/1.jpg" alt="Login page" height="300">
<img src="documentation/readmemd-images/2.jpg" alt="Login page" height="300">
<img src="documentation/readmemd-images/3.jpg" alt="Login page" height="300">
<img src="documentation/readmemd-images/4.jpg" alt="Login page" height="300">
<img src="documentation/readmemd-images/5.jpg" alt="Login page" height="300">

## Полезное  
1. Запуск проекта:
``` bash
docker-compose -f docker/docker-compose.yaml up -d
```
2. Создать нового юзера:
```php
php bin/console user:add <username>
``` 
