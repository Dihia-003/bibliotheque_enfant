<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class LivreController extends AbstractController
{
    #[Route('/livre', name: 'app_livre')]
    public function index(LivreRepository $livreRepository, KernelInterface $kernel, SerializerInterface $serializer): Response
    {
        try {
            // Chargement des livres depuis la base de données, si disponibles
            $livresDb = $livreRepository->findAll();
        } catch (\Exception $e) {
            // En cas d'erreur de base de données, on continue avec un tableau vide
            $livresDb = [];
        }
        
        // Chargement des livres pour enfants depuis le JSON
        $jsonPath = $kernel->getProjectDir() . '/public/data/children-books.json';
        $livresEnfants = [];
        
        if (file_exists($jsonPath)) {
            try {
                $jsonContent = file_get_contents($jsonPath);
                $data = json_decode($jsonContent, true);
                $livresEnfants = $data['books'] ?? [];
            } catch (\Exception $e) {
                // En cas d'erreur de lecture du JSON, on continue avec un tableau vide
                $livresEnfants = [];
            }
        }
        
        return $this->render('livre/index.html.twig', [
            'controller_name' => 'LivreController',
            'livres' => $livresDb,
            'livresEnfants' => $livresEnfants,
        ]);
    }
    
    #[Route('/livre/{id}', name: 'app_livre_show', requirements: ['id' => '\d+'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }
    
    #[Route('/livre/enfant/{id}', name: 'app_livre_enfant_show')]
    public function showEnfant(string $id, KernelInterface $kernel): Response
    {
        // Chargement des livres pour enfants depuis le JSON
        $jsonPath = $kernel->getProjectDir() . '/public/data/children-books.json';
        $livre = null;
        
        if (file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            $data = json_decode($jsonContent, true);
            
            // Rechercher le livre par ID
            foreach ($data['books'] as $book) {
                if ($book['id'] == $id) {
                    $livre = $book;
                    break;
                }
            }
        }
        
        if (!$livre) {
            throw $this->createNotFoundException('Le livre pour enfant demandé n\'existe pas');
        }
        
        return $this->render('livre/show_enfant.html.twig', [
            'livre' => $livre,
        ]);
    }
}
