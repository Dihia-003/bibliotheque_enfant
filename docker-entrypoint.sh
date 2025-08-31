#!/bin/bash
set -e

echo "🚀 Démarrage de l'application Symfony..."

# Vérification de l'autoload
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "⚠️  Fichier autoload.php manquant, régénération..."
    cd /var/www
    composer dump-autoload --optimize --no-dev --classmap-authoritative --no-interaction
fi

if [ ! -f "/var/www/vendor/autoload_runtime.php" ]; then
    echo "⚠️  Fichier autoload_runtime.php manquant, régénération..."
    cd /var/www
    composer dump-autoload --optimize --no-dev --classmap-authoritative --no-interaction
fi

# Vérification des fichiers d'autoload
echo "📁 Vérification des fichiers d'autoload..."
ls -la /var/www/vendor/autoload* || echo "❌ Aucun fichier autoload trouvé"

# Nettoyage du cache
echo "🧹 Nettoyage du cache..."
rm -rf /var/www/var/cache/*

# Définition des permissions
echo "🔐 Définition des permissions..."
chown -R www-data:www-data /var/www

echo "✅ Application prête, démarrage d'Apache..."
exec apache2-foreground
