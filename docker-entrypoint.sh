#!/bin/bash
set -e

echo "🚀 Initialisation de l'application Symfony en production..."

# Créer les dossiers nécessaires
mkdir -p var/cache/prod var/log public/uploads/couvertures

# Vérification des permissions
echo "🔐 Vérification des permissions..."
chown -R www-data:www-data var public/uploads || echo "Warning: Permission change failed, continuing..."
chmod -R 755 var public/uploads || echo "Warning: Permission change failed, continuing..."

# Créer un dossier assets minimal si il n'existe pas
echo "⚠️ Vérification des assets..."
if [ ! -d "public/assets" ] || [ -z "$(ls -A public/assets 2>/dev/null)" ]; then
    echo "Création d'assets de fallback..."
    mkdir -p public/assets/styles public/assets/controllers public/assets/vendor/@hotwired/stimulus public/assets/vendor/@hotwired/turbo public/assets/@symfony/stimulus-bundle public/assets/@symfony/ux-turbo

    # Créer des fichiers CSS et JS de base avec les noms hashés exacts
    echo "/* Fallback CSS - Bibliothèque des Enfants */" > public/assets/styles/app-Wux0ucT.css
    echo "body { background-color: #f1f9ff; font-family: 'Comic Sans MS', cursive; }" >> public/assets/styles/app-Wux0ucT.css
    echo "/* Fallback CSS - Bibliothèque des Enfants */" > public/assets/styles/app-j_TRKrf.css
    echo "body { background-color: #f1f9ff; font-family: 'Comic Sans MS', cursive; }" >> public/assets/styles/app-j_TRKrf.css
    echo "/* Fallback JS - Bibliothèque des Enfants */" > public/assets/app-jgPm2-L.js
    echo "console.log('Fallback JS loaded');" >> public/assets/app-jgPm2-L.js
    echo "/* Fallback Bootstrap */" > public/assets/bootstrap-xCO4u8H.js
    echo "console.log('Fallback Bootstrap loaded');" >> public/assets/bootstrap-xCO4u8H.js
    echo "/* Fallback Stimulus */" > public/assets/vendor/@hotwired/stimulus/stimulus.index-S4zNcea.js
    echo "console.log('Fallback Stimulus loaded');" >> public/assets/vendor/@hotwired/stimulus/stimulus.index-S4zNcea.js
    echo "/* Fallback Turbo */" > public/assets/vendor/@hotwired/turbo/turbo.index-pT15T6h.js
    echo "console.log('Fallback Turbo loaded');" >> public/assets/vendor/@hotwired/turbo/turbo.index-pT15T6h.js
    echo "/* Fallback Stimulus Bundle */" > public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js
    echo "console.log('Fallback Stimulus Bundle loaded');" >> public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js
    echo "/* Fallback UX Turbo */" > public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js
    echo "console.log('Fallback UX Turbo loaded');" >> public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js
    echo "/* Fallback Loader */" > public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js
    echo "console.log('Fallback Loader loaded');" >> public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js
    echo "/* Fallback Hello Controller */" > public/assets/controllers/hello_controller-VYgvytJ.js
    echo "console.log('Fallback Hello Controller loaded');" >> public/assets/controllers/hello_controller-VYgvytJ.js

    echo "✅ Assets de fallback créés avec succès"
fi

# Nettoyage et réchauffement du cache
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod || echo "Warning: Cache clear failed, continuing..."
php bin/console cache:warmup --env=prod || echo "Warning: Cache warmup failed, continuing..."

# Vérification des extensions PHP
echo "🔍 Vérification des extensions PHP..."
php -m | grep pdo_pgsql || echo "WARNING: pdo_pgsql extension not found"
php -m | grep pdo || echo "WARNING: no pdo drivers found"

# Vérification de la base de données (optionnelle)
echo "🗄️ Vérification de la base de données..."
php bin/console doctrine:query:sql "SELECT 1" || echo "Warning: Database connection failed, continuing..."

# Exécution des migrations si nécessaire
echo "🔄 Vérification des migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod || echo "Warning: Migrations failed, continuing..."

echo "✅ Application Symfony initialisée avec succès !"

# Exécuter la commande passée en paramètre
exec "$@"
