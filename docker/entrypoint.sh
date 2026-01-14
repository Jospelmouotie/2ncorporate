#!/bin/sh

# Démarrer PHP-FPM en arrière-plan
php-fpm -D

# ATTENTION : On ajoute la ligne de migration ici
# Elle s'exécutera automatiquement à chaque déploiement
echo "Lancement des migrations..."
php artisan migrate --force

# Optionnel : Si tu veux aussi ajouter tes produits de test (si tu as des Seeders)
# php artisan db:seed --force

# Lancer Nginx au premier plan
echo "Démarrage de Nginx..."
nginx -g "daemon off;"
