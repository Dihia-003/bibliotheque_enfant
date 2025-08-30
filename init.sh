#!/bin/bash
set -e

echo "🚀 Initialisation de l'application Symfony..."

# Créer les dossiers nécessaires
mkdir -p var/cache var/log public/uploads

# Définir les permissions
chown -R www-data:www-data var public/uploads
chmod -R 755 var public/uploads

# Vider le cache
php bin/console cache:clear --env=prod --no-debug

# Créer la base SQLite si elle n'existe pas
if [ ! -f var/database.sqlite ]; then
    echo "📊 Création de la base SQLite..."
    touch var/database.sqlite
    chown www-data:www-data var/database.sqlite
    chmod 664 var/database.sqlite
fi

# Exécuter les migrations si nécessaire
echo "🔄 Vérification des migrations..."
if php bin/console doctrine:query:sql "SELECT COUNT(*) FROM sqlite_master WHERE type='table'" --env=prod 2>/dev/null | grep -q "0"; then
    echo "📋 Exécution des migrations..."
    php bin/console doctrine:migrations:migrate --env=prod --no-interaction
fi

echo "✅ Application initialisée !"
