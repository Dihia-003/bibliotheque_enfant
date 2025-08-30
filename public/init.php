<?php
/**
 * Script d'initialisation pour l'application Symfony
 * Appelez ce script une fois après le déploiement pour initialiser la base de données
 */

// Vérifier si c'est un appel d'initialisation
if (!isset($_GET['init']) || $_GET['init'] !== 'true') {
    http_response_code(403);
    exit('Accès refusé');
}

echo "🚀 Initialisation de l'application Symfony...\n";

try {
    // Inclure l'autoloader de Composer
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Créer le kernel Symfony
    $kernel = new \App\Kernel('prod', false);
    $kernel->boot();
    
    // Créer les dossiers nécessaires
    $varDir = __DIR__ . '/../var';
    if (!is_dir($varDir . '/cache')) mkdir($varDir . '/cache', 0755, true);
    if (!is_dir($varDir . '/log')) mkdir($varDir . '/log', 0755, true);
    if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0755, true);
    
    // Créer la base SQLite si elle n'existe pas
    $dbFile = $varDir . '/database.sqlite';
    if (!file_exists($dbFile)) {
        touch($dbFile);
        chmod($dbFile, 0664);
        echo "📊 Base de données SQLite créée\n";
    }
    
    // Vider le cache
    $kernel->getContainer()->get('cache.app')->clear();
    echo "🧹 Cache vidé\n";
    
    // Exécuter les migrations
    $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    $application->setAutoExit(false);
    
    $input = new \Symfony\Component\Console\Input\ArrayInput(['command' => 'doctrine:migrations:migrate', '--no-interaction' => true]);
    $application->run($input, new \Symfony\Component\Console\Output\ConsoleOutput());
    
    echo "📋 Migrations exécutées\n";
    echo "✅ Application initialisée avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de l'initialisation : " . $e->getMessage() . "\n";
    http_response_code(500);
}
