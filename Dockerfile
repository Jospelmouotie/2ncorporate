# 1. Image de base avec PHP 8.2 et FPM
FROM php:8.2-fpm-alpine

# 2. Installer les extensions nécessaires pour Laravel (DB, Zip, etc.)
RUN apk add --no-cache nginx libpng-dev libzip-dev oniguruma-dev postgresql-dev
RUN docker-php-ext-install pdo_pgsql pdo_mysql gd zip

# 3. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Définir le dossier de travail
WORKDIR /var/www

# 5. Copier les fichiers du projet
COPY . .

# 6. Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# 7. Donner les permissions aux dossiers de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# 8. Copier notre config Nginx et notre script de démarrage
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 9. Exposer le port 80 pour Render
EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
