#!/bin/sh

# Démarrer PHP
php-fpm -D

# Installer/Mettre à jour les dépendances si nécessaire (optionnel)
# php artisan migrate --force

# Démarrer le serveur web Nginx
nginx -g "daemon off;"
