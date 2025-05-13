#!/bin/bash

# Script de déploiement pour une application Symfony

# Étape 1: Installation des dépendances sans les outils de développement
echo "Installation des dépendances de production..."
composer install --no-dev --optimize-autoloader

# Étape 2: Vider le cache
echo "Vidage du cache..."
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Étape 3: Compilation des assets
echo "Compilation des assets..."
php bin/console asset-map:compile

# Étape 4: Application des migrations de base de données
echo "Application des migrations de base de données..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "Déploiement terminé avec succès!" 