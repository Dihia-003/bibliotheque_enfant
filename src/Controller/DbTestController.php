<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PDO;
use PDOException;

final class DbTestController extends AbstractController
{
    #[Route('/db-test', name: 'app_db_test')]
    public function index(): Response
    {
        $results = [];
        
        // Test 1: Utilisateur postgres
        $url1 = 'postgresql://postgres:0hmMirvhu0ZnSps0@aws-0-eu-central-1.pooler.supabase.com:6543/postgres';
        $results['postgres'] = $this->testConnection($url1);
        
        // Test 2: Utilisateur postgres.hyrbvsxrxfanivasssfl
        $url2 = 'postgresql://postgres.hyrbvsxrxfanivasssfl:0hmMirvhu0ZnSps0@aws-0-eu-central-1.pooler.supabase.com:6543/postgres';
        $results['postgres.hyrbvsxrxfanivasssfl'] = $this->testConnection($url2);
        
        // Test 3: Utilisateur hyrbvsxrxfanivasssfl
        $url3 = 'postgresql://hyrbvsxrxfanivasssfl:0hmMirvhu0ZnSps0@aws-0-eu-central-1.pooler.supabase.com:6543/postgres';
        $results['hyrbvsxrxfanivasssfl'] = $this->testConnection($url3);
        
        $html = '<h1>Test des connexions à la base de données</h1>';
        
        foreach ($results as $user => $result) {
            $html .= "<h2>Test avec l'utilisateur: $user</h2>";
            $html .= "<pre>" . print_r($result, true) . "</pre>";
            $html .= "<hr>";
        }
        
        return new Response($html, Response::HTTP_OK, ['Content-Type' => 'text/html; charset=utf-8']);
    }
    
    private function testConnection(string $url): array
    {
        try {
            $pdo = new PDO($url);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Test de requête simple
            $stmt = $pdo->query('SELECT 1 as test, current_user as user, current_database() as db');
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'SUCCESS',
                'message' => 'Connexion réussie',
                'data' => $result,
                'url' => $url
            ];
            
        } catch (PDOException $e) {
            return [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'url' => $url
            ];
        }
    }
}
