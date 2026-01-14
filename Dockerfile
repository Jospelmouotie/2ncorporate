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
# ... (après COPY . .)
# Supprimer le fichier .env s'il existe dans le dossier pour forcer Render
RUN rm -f .env

# S'assurer que les permissions sont totales sur le stockage
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache
# 6. Supprimer le fichier .env s'il a été copié par erreur
# Cela force Laravel à utiliser les variables de Render
RUN rm -f .env

# 7. Installer les dépendances
RUN composer install --no-dev --optimize-autoloader

# 8. Permissions CRUCIALES pour www-data
# On donne les droits sur TOUT le dossier pour éviter l'erreur laravel.log
RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www/storage

# 9. Config Nginx et Entrypoint
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
