#!/bin/sh

# 1. Création du lien symbolique pour le stockage (indispensable pour les images)
# On utilise --force pour s'assurer que le lien est créé même s'il existe déjà un dossier fantôme
echo "Création du lien symbolique storage..."
php artisan storage:link --force

# 2. Nettoyage du cache
echo "Nettoyage du cache..."
rm -f /var/www/bootstrap/cache/config.php
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. Lancement de PHP-FPM en arrière-plan
echo "Lancement de PHP-FPM..."
php-fpm -D

# 4. Exécution des migration
# On le fait avant Nginx pour que la base soit prête avant l'arrivée du trafic
echo "Tentative de migration sur : $DB_CONNECTION"
php artisan migrate --force
php artisan db:seed

# 5. Démarrage de Nginx
echo "Démarrage de Nginx..."
nginx -g "daemon off;"
