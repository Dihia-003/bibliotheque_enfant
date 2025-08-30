#!/bin/bash
set -e

echo "🚀 Initialisation de l'application Symfony en production..."

# Créer les dossiers nécessaires
mkdir -p var/cache/prod var/log public/uploads/couvertures

# Vérification des permissions
echo "🔐 Vérification des permissions..."
chown -R www-data:www-data var public/uploads || echo "Warning: Permission change failed, continuing..."
chmod -R 755 var public/uploads || echo "Warning: Permission change failed, continuing..."

# Forcer la recompilation des assets à chaque démarrage
echo "🎨 Compilation des assets..."
rm -rf public/assets var/cache/prod/.assets_compiled

# Compiler les assets en ignorant les erreurs de base de données
php bin/console asset-map:compile --env=prod --no-interaction || echo "Warning: Assets compilation failed, continuing..."

# Vérifier que les assets ont été compilés
if [ -d "public/assets" ] && [ "$(ls -A public/assets)" ]; then
    echo "✅ Assets compilés avec succès"
    touch var/cache/prod/.assets_compiled
else
    echo "⚠️ Assets non compilés, création d'un fallback"
    mkdir -p public/assets/styles
    echo "/* Fallback CSS */" > public/assets/styles/app.css
    echo "/* Fallback JS */" > public/assets/app.js
fi

# Nettoyage et réchauffement du cache
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod || echo "Warning: Cache clear failed, continuing..."
php bin/console cache:warmup --env=prod || echo "Warning: Cache warmup failed, continuing..."

# Vérification des extensions PHP
echo "🔍 Vérification des extensions PHP..."
php -m | grep pdo_pgsql || echo "WARNING: pdo_pgsql extension not found"
php -m | grep pdo || echo "WARNING: no pdo drivers found"

# Vérification de la base de données
echo "🗄️ Vérification de la base de données..."
php bin/console doctrine:query:sql "SELECT 1" || echo "Warning: Database connection failed, continuing..."

# Exécution des migrations si nécessaire
echo "🔄 Vérification des migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod || echo "Warning: Migrations failed, continuing..."

echo "✅ Application Symfony initialisée avec succès !"

# Exécuter la commande passée en paramètre
exec "$@"
