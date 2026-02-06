# 1. Image de base stable
FROM php:8.2-fpm-alpine

# 2. Installation des dépendances système, PHP et Node.js
# On installe Node.js et NPM pour compiler les assets Vue
RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    nodejs \
    npm

# Installation des extensions PHP
RUN docker-php-ext-install pdo_pgsql pdo_mysql gd zip

# 3. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Dossier de travail
WORKDIR /var/www

# 5. Copier le projet
COPY . .

# 6. NETTOYAGE CRUCIAL : Supprimer le .env local
# Cela force Laravel à utiliser les variables définies dans l'interface de Render
RUN rm -f .env

# 7. Installation des dépendances PHP (Composer)
RUN composer install --no-dev --optimize-autoloader

# 8. COMPILATION DES ASSETS (Vite + Vue)
# On utilise --unsafe-perm pour éviter les erreurs de droits avec NPM sur Alpine
RUN npm install --unsafe-perm
RUN npm run build

# 9. GESTION DES PERMISSIONS
# On définit l'utilisateur www-data comme propriétaire APPRÈS le build
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 10. Configuration Serveur
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
