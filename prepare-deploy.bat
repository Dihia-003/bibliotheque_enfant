@echo off
echo Préparation du projet Symfony pour le déploiement en production...

echo.
echo 1. Installation des dépendances optimisées pour la production...
composer install --no-dev --optimize-autoloader

echo.
echo 2. Nettoyage du cache...
php bin/console-prod cache:clear

echo.
echo 3. Compilation des assets...
php bin/console-prod asset-map:compile

echo.
echo 4. Préparation terminée !
echo.
echo Votre site est prêt à être déployé en production.
echo Assurez-vous de configurer votre fichier .env.local sur le serveur avec:
echo   - APP_ENV=prod
echo   - APP_DEBUG=0
echo   - DATABASE_URL avec les informations de connexion à votre base de données
echo.
echo Pour plus d'informations, consultez le fichier README-deploy.md

pause 