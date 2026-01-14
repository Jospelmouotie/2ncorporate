#!/bin/sh

echo "Nettoyage du cache..."
# Supprime les fichiers de cache physique
rm -f /var/www/bootstrap/cache/config.php
php artisan config:clear
php artisan cache:clear

echo "Lancement de PHP-FPM..."
php-fpm -D

echo "Tentative de migration sur : $DB_CONNECTION"
# Ici on force la migration
php artisan migrate --force

echo "Démarrage de Nginx..."
nginx -g "daemon off;"
