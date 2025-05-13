<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuteursController extends AbstractController
{
    #[Route('/auteurs', name: 'app_auteurs')]
    public function index(AuteurRepository $auteurRepository): Response
    {
        // Récupérer tous les auteurs
        $auteurs = $auteurRepository->findAll();
        
        return $this->render('auteurs/index.html.twig', [
            'controller_name' => 'AuteursController',
            'auteurs' => $auteurs,
        ]);
    }
    
    #[Route('/auteurs/{id}', name: 'app_auteurs_show', requirements: ['id' => '\d+'])]
    public function show(Auteur $auteur): Response
    {
        return $this->render('auteurs/show.html.twig', [
            'auteur' => $auteur,
        ]);
    }
} 