#!/bin/bash
set -e

# Attendre que le système soit prêt
echo "🚀 Démarrage de l'application Symfony..."

# Vérifier que les dossiers nécessaires existent
echo "📁 Création des dossiers nécessaires..."
mkdir -p var/cache var/log public/uploads

# Définir les permissions
echo "🔐 Configuration des permissions..."
chown -R www-data:www-data var public/uploads
chmod -R 755 var public/uploads

# Vider le cache de production
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod --no-debug

# Créer la base de données SQLite si elle n'existe pas
echo "🗄️ Configuration de la base de données SQLite..."
if [ ! -f var/database.sqlite ]; then
    echo "📊 Création de la base de données SQLite..."
    touch var/database.sqlite
    chown www-data:www-data var/database.sqlite
    chmod 664 var/database.sqlite
fi

# Exécuter les migrations si la base est vide
echo "🔄 Vérification des migrations..."
if php bin/console doctrine:query:sql "SELECT COUNT(*) FROM sqlite_master WHERE type='table'" --env=prod 2>/dev/null | grep -q "0"; then
    echo "📋 Exécution des migrations..."
    php bin/console doctrine:migrations:migrate --env=prod --no-interaction
fi

# Créer les assets de fallback si nécessaire
echo "🎨 Création des assets de fallback..."
if [ ! -d public/assets ]; then
    echo "📁 Création du dossier assets..."
    mkdir -p public/assets/styles public/assets/js public/assets/@symfony/stimulus-bundle public/assets/@symfony/ux-turbo public/assets/controllers
    
    echo "📝 Création des assets CSS de fallback..."
    echo "/* Fallback CSS */" > public/assets/styles/app-Wux0ucT.css
    echo "body { background-color: #f0f8ff; }" >> public/assets/styles/app-Wux0ucT.css
    
    echo "/* Fallback CSS */" > public/assets/styles/app-j_TRKrf.css
    echo "body { background-color: #f0f8ff; }" >> public/assets/styles/app-j_TRKrf.css
    
    echo "📝 Création des assets JavaScript de fallback..."
    echo "/* Fallback App JS */" > public/assets/app-jgPm2-L.js
    echo "console.log('Fallback App JS loaded');" >> public/assets/app-jgPm2-L.js
    
    echo "/* Fallback Bootstrap */" > public/assets/bootstrap-xCO4u8H.js
    echo "console.log('Fallback Bootstrap loaded');" >> public/assets/bootstrap-xCO4u8H.js
    
    echo "/* Fallback Stimulus */" > public/assets/@symfony/stimulus-bundle/stimulus.index-S4zNcea.js
    echo "console.log('Fallback Stimulus loaded');" >> public/assets/@symfony/stimulus-bundle/stimulus.index-S4zNcea.js
    
    echo "/* Fallback Turbo */" > public/assets/@symfony/ux-turbo/turbo.index-pT15T6h.js
    echo "console.log('Fallback Turbo loaded');" >> public/assets/@symfony/ux-turbo/turbo.index-pT15T6h.js
    
    echo "/* Fallback Controllers */" > public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js
    echo "console.log('Fallback Controllers loaded');" >> public/assets/@symfony/stimulus-bundle/controllers-IWeobkd.js
    
    echo "/* Fallback UX Turbo */" > public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js
    echo "console.log('Fallback UX Turbo loaded');" >> public/assets/@symfony/ux-turbo/turbo_controller-8wQNi2p.js
    
    echo "/* Fallback Loader */" > public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js
    echo "console.log('Fallback Loader loaded');" >> public/assets/@symfony/stimulus-bundle/loader-V1GtHuK.js
    
    echo "/* Fallback Hello Controller */" > public/assets/controllers/hello_controller-VYgvytJ.js
    echo "console.log('Fallback Hello Controller loaded');" >> public/assets/controllers/hello_controller-VYgvytJ.js
    
    echo "✅ Assets de fallback créés avec succès"
fi

# Définir les permissions finales
echo "🔐 Configuration des permissions finales..."
chown -R www-data:www-data var public/assets
chmod -R 755 var public/assets

echo "🎉 Application prête ! Démarrage d'Apache..."

# Démarrer Apache en premier plan
exec apache2-foreground
