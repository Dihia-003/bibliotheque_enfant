<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;

final class DbUrlController extends AbstractController
{
    #[Route('/db-url', name: 'app_db_url')]
    public function index(): Response
    {
        $dbUrl = $this->getParameter('doctrine.default_connection');
        
        return new Response(
            '<h1>URL de la base de données actuelle</h1>' .
            '<h2>Paramètre doctrine.default_connection :</h2>' .
            '<pre>' . print_r($dbUrl, true) . '</pre>' .
            '<h2>Variable d\'environnement DATABASE_URL :</h2>' .
            '<pre>' . ($_ENV['DATABASE_URL'] ?? 'Non définie') . '</pre>' .
            '<h2>Variables d\'environnement :</h2>' .
            '<pre>' . print_r($_ENV, true) . '</pre>',
            Response::HTTP_OK,
            ['Content-Type' => 'text/html; charset=utf-8']
        );
    }
}
