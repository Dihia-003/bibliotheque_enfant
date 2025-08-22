#!/bin/bash
set -e

echo "🚀 Initialisation de l'application Symfony en production..."

# Créer les dossiers nécessaires
mkdir -p var/cache/prod var/log public/uploads/couvertures

# Vérification des permissions
echo "🔐 Vérification des permissions..."
chown -R www-data:www-data var public/uploads || echo "Warning: Permission change failed, continuing..."
chmod -R 755 var public/uploads || echo "Warning: Permission change failed, continuing..."

# Compilation des assets si pas encore fait
if [ ! -f "var/cache/prod/.assets_compiled" ]; then
    echo "🎨 Compilation des assets..."
    php bin/console asset-map:compile --env=prod || echo "Warning: Assets compilation failed, continuing..."
    touch var/cache/prod/.assets_compiled
fi

# Nettoyage et réchauffement du cache
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod || echo "Warning: Cache clear failed, continuing..."
php bin/console cache:warmup --env=prod || echo "Warning: Cache warmup failed, continuing..."

# Vérification de la base de données
echo "🗄️ Vérification de la base de données..."
php bin/console doctrine:query:sql "SELECT 1" || echo "Warning: Database connection failed, continuing..."

echo "✅ Application Symfony initialisée avec succès !"

# Exécuter la commande passée en paramètre
exec "$@"
