#!/bin/bash
set -e

# Attendre que la base de données soit prête (si nécessaire)
echo "Initialisation de l'application Symfony..."

# Compilation des assets si pas encore fait
if [ ! -f "var/cache/prod/.assets_compiled" ]; then
    echo "Compilation des assets..."
    php bin/console asset-map:compile --env=prod || echo "Warning: Assets compilation failed, continuing..."
    touch var/cache/prod/.assets_compiled
fi

# Nettoyage du cache
echo "Nettoyage du cache..."
php bin/console cache:clear --env=prod || echo "Warning: Cache clear failed, continuing..."
php bin/console cache:warmup --env=prod || echo "Warning: Cache warmup failed, continuing..."

# Vérification des permissions
echo "Vérification des permissions..."
chown -R www-data:www-data var public/uploads || echo "Warning: Permission change failed, continuing..."
chmod -R 755 var public/uploads || echo "Warning: Permission change failed, continuing..."

echo "Application Symfony initialisée !"

# Exécuter la commande passée en paramètre
exec "$@"
