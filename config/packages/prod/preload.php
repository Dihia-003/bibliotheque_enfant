<?php

// Configuration de production pour le preload
return [
    // Classes à précharger en production
    'classes' => [
        // Classes Symfony
        'Symfony\Component\HttpFoundation\Request',
        'Symfony\Component\HttpFoundation\Response',
        'Symfony\Component\HttpKernel\HttpKernel',
        'Symfony\Component\Routing\Router',
        'Symfony\Component\DependencyInjection\ContainerBuilder',
        
        // Classes de l'application
        'App\Controller\HomeController',
        'App\Controller\LivreController',
        'App\Entity\Livre',
        'App\Entity\User',
    ],
    
    // Fichiers à précharger en production
    'files' => [
        // Fichiers de configuration
        __DIR__ . '/../../config/bundles.php',
        __DIR__ . '/../../config/packages/prod/framework.yaml',
        __DIR__ . '/../../config/packages/prod/doctrine.yaml',
    ],
];
