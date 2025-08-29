<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

final class DatabaseTestController extends AbstractController
{
    #[Route('/db-test', name: 'app_db_test')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $results = [];
        
        try {
            // Test 1: Vérifier la connexion
            $connection = $doctrine->getConnection();
            $results['connection_ok'] = true;
            
            // Test 2: Vérifier que c'est bien PostgreSQL
            $platform = $connection->getDatabasePlatform();
            $results['database_type'] = get_class($platform);
            
            // Test 3: Requête simple
            $stmt = $connection->executeQuery('SELECT 1 as test');
            $result = $stmt->fetchAssociative();
            $results['query_ok'] = $result['test'] == 1;
            
            // Test 4: Vérifier les tables
            $tables = $connection->executeQuery("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAllAssociative();
            $results['tables'] = array_column($tables, 'table_name');
            
        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
            $results['connection_ok'] = false;
        }
        
        $html = '<h1>Test Base de Données</h1>';
        $html .= '<pre>' . print_r($results, true) . '</pre>';
        
        return new Response($html, Response::HTTP_OK, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}
