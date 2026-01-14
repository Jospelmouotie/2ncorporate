# 1. Image de base
FROM php:8.2-fpm-alpine

# 2. Installer les extensions PHP ET Node.js + NPM
RUN apk add --no-cache nginx libpng-dev libzip-dev oniguruma-dev postgresql-dev nodejs npm

RUN docker-php-ext-install pdo_pgsql pdo_mysql gd zip

# 3. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Copier les fichiers
COPY . .

# 5. Supprimer le .env pour forcer les variables Render
RUN rm -f .env

# 6. Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# 7. --- ÉTAPE MANQUANTE : COMPILER VUE.JS ---
RUN npm install
RUN npm run build

# 8. Permissions (On le fait APPRÈS le build pour inclure le dossier public/build)
RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 9. Config Nginx et Entrypoint
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
