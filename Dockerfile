# Etapa 1: construir assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js .
RUN npm run build

# Etapa 2: PHP + Composer
FROM php:8.3-fpm-alpine AS backend
WORKDIR /app
RUN apk add --no-cache git zip unzip libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql intl
# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader
COPY . .
# Copia assets compilados
COPY --from=frontend /app/resources ./resources
COPY --from=frontend /app/node_modules ./node_modules
COPY --from=frontend /app/public/build ./public/build
# Caches de Laravel
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache
# Servir la app (usa PHP built-in; en prod real pondrías nginx+fpm o FrankenPHP)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
EXPOSE 8000