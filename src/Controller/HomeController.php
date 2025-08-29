<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(KernelInterface $kernel): Response
    {
        // Chargement des livres pour enfants depuis le JSON
        $jsonPath = $kernel->getProjectDir() . '/public/data/children-books.json';
        $livresEnfants = [];
        
        if (file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            $data = json_decode($jsonContent, true);
            $livresEnfants = array_slice($data['books'] ?? [], 0, 6); // Afficher seulement 6 livres
        }
        
        return $this->render('home/index.html.twig', [
            'livresEnfants' => $livresEnfants,
        ]);
    }
}
