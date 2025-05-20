# 🎬 YouTube, Rutube, VK Video Downloader  

*A lightweight service for downloading videos from YouTube, Rutube, and Vkontakte*  

**🛠 Tech Stack**:  
- PHP 8 🐘  
- Symfony 7 🎼  
- Docker 🐳  
- PostgreSQL 🐘  
- yt-dlp ⚡  
- norkunas/youtube-dl-php 📦  

## 📸 Preview  
<img src="documentation/readme-img/1.jpg" alt="Login page" height="300"> <img src="documentation/readme-img/2.jpg" alt="Login page" height="300"> <img src="documentation/readme-img/3.jpg" alt="Login page" height="300">  

## 🚀 Quick Start  

### ⚡ Run the Project:  
1. **Start containers**:  
   ```bash
   cd docker/
   docker-compose up -d
   ```

2. **Install dependencies**:  
   ```bash
   docker exec ytdownloader-php-fpm composer install
   docker exec ytdownloader-php-fpm composer update
   ```

3. **Setup database**:  
   ```bash
   docker exec ytdownloader-php-fpm php bin/console doctrine:database:create --if-not-exists
   docker exec ytdownloader-php-fpm php bin/console doctrine:migrations:migrate
   ```
   > 📝 **Note**: Create `.env.local` with DB config (host must be `ytdownloader-pgsql`)

4. **Start queue worker**:  
   ```bash
   docker exec ytdownloader-php-fpm /etc/init.d/supervisor start
   ```

5. **Create user**:  
   ```bash
   docker exec ytdownloader-php-fpm php bin/console user:add <username>
   ```

6. **Run tests**:  
   ```bash
   docker exec ytdownloader-php-fpm sh bin/test.sh
   ```

7. **Health check**:  
   ```
   GET http://host.tld/health
   ```

## 📝 Todo Roadmap  

✅ ~~Background video downloads (queues)~~  
✅ ~~Download status notifications~~  
✅ ~~Playlist special characters fix~~  
✅ ~~Tests coverage~~  
✅ ~~Refactor to services~~  
✅ ~~Health check endpoint~~  
🔳 YouTube cache optimization (avoid bot detection)  
🔳 Download statistics counter  
🔳 REST API implementation  
🔳 Telegram bot integration  
🔳 Setup automation script  
