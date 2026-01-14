#!/bin/sh

# Création des dossiers nécessaires pour éviter les erreurs de permissions
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache

# Ajustement des permissions au démarrage
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage

# Démarrer PHP-FPM
php-fpm -D

echo "Lancement des migrations..."
# On attend un peu que la DB soit prête si nécessaire
php artisan config:clear
php artisan migrate --force
php artisan migrate --force

echo "Démarrage de Nginx..."
nginx -g "daemon off;"
