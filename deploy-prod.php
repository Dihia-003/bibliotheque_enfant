<?php

/**
 * Script de déploiement en production pour Symfony
 * 
 * Ce script contourne les problèmes liés au chargement des bundles
 * de développement lors du déploiement en production.
 */

// Définir l'environnement de production
$_SERVER['APP_ENV'] = 'prod';
$_SERVER['APP_DEBUG'] = '0';
putenv('APP_ENV=prod');
putenv('APP_DEBUG=0');

echo "Préparation du projet Symfony pour le déploiement en production...\n\n";

// 1. Supprimer le cache existant
echo "1. Suppression du cache existant...\n";
if (is_dir(__DIR__ . '/var/cache/prod')) {
    removeDirectory(__DIR__ . '/var/cache/prod');
}
mkdir(__DIR__ . '/var/cache/prod', 0777, true);
echo "✓ Cache supprimé\n\n";

// 2. Compilation des assets en mode production
echo "2. Compilation des assets...\n";
$assetMapCommand = 'php -d memory_limit=-1 ' . __DIR__ . '/bin/console asset-map:compile --env=prod --no-debug';
passthru($assetMapCommand, $assetResult);
echo $assetResult === 0 ? "✓ Assets compilés\n\n" : "✗ Erreur lors de la compilation des assets\n\n";

// 3. Optimisation du chargement automatique
echo "3. Optimisation des performances...\n";
$command = 'composer dump-autoload --optimize --no-dev --classmap-authoritative';
passthru($command, $result);
echo $result === 0 ? "✓ Autoloader optimisé\n\n" : "✗ Erreur lors de l'optimisation de l'autoloader\n\n";

echo "Déploiement terminé !\n";
echo "Votre site est prêt à être utilisé en production.\n";
echo "Vérifiez que le fichier .env.local contient bien:\n";
echo "APP_ENV=prod\n";
echo "APP_DEBUG=0\n";

/**
 * Fonction récursive pour supprimer un dossier et son contenu
 */
function removeDirectory($path) {
    $files = glob($path . '/*');
    foreach ($files as $file) {
        is_dir($file) ? removeDirectory($file) : unlink($file);
    }
    rmdir($path);
    return;
} 