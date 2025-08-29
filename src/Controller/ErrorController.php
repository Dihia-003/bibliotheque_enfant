<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use Throwable;

final class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function index(LoggerInterface $logger): Response
    {
        // Capturer la dernière erreur
        $error = error_get_last();
        
        if ($error) {
            $logger->error('Erreur PHP: ' . json_encode($error));
        }
        
        return new Response(
            '<h1>Page de diagnostic des erreurs</h1>' .
            '<h2>Dernière erreur PHP :</h2>' .
            '<pre>' . print_r($error, true) . '</pre>' .
            '<h2>Informations système :</h2>' .
            '<ul>' .
            '<li>PHP Version: ' . PHP_VERSION . '</li>' .
            '<li>Extensions chargées: ' . implode(', ', get_loaded_extensions()) . '</li>' .
            '<li>Dossier de travail: ' . getcwd() . '</li>' .
            '</ul>',
            Response::HTTP_OK,
            ['Content-Type' => 'text/html; charset=utf-8']
        );
    }
}
