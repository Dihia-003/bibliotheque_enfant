<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(KernelInterface $kernel, ManagerRegistry $doctrine): JsonResponse
    {
        $results = [];
        
        try {
            // Test 1: Vérifier si le fichier JSON existe
            $jsonPath = $kernel->getProjectDir() . '/public/data/children-books.json';
            $results['json_file_exists'] = file_exists($jsonPath);
            $results['json_path'] = $jsonPath;
            
            if (file_exists($jsonPath)) {
                $jsonContent = file_get_contents($jsonPath);
                $data = json_decode($jsonContent, true);
                $results['json_books_count'] = count($data['books'] ?? []);
            }
            
            // Test 2: Vérifier la connexion à la base de données
            try {
                $connection = $doctrine->getConnection();
                $results['database_connected'] = true;
                
                // Test simple de requête
                $stmt = $connection->executeQuery('SELECT 1 as test');
                $result = $stmt->fetchAssociative();
                $results['database_query_success'] = $result['test'] == 1;
                
            } catch (\Exception $e) {
                $results['database_connected'] = false;
                $results['database_error'] = $e->getMessage();
            }
            
            // Test 3: Vérifier les entités
            try {
                $em = $doctrine->getManager();
                $results['entity_manager_ok'] = true;
                
                // Test de récupération des livres
                $livreRepo = $em->getRepository(\App\Entity\Livre::class);
                $livres = $livreRepo->findAll();
                $results['livres_count'] = count($livres);
                
            } catch (\Exception $e) {
                $results['entity_manager_ok'] = false;
                $results['entity_error'] = $e->getMessage();
            }
            
        } catch (\Exception $e) {
            $results['general_error'] = $e->getMessage();
        }
        
        return new JsonResponse($results);
    }
}
