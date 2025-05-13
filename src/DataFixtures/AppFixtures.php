<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer quelques auteurs
        $auteur1 = new Auteur();
        $auteur1->setNom('Hugo');
        $auteur1->setPrenom('Victor');
        
        $auteur2 = new Auteur();
        $auteur2->setNom('Camus');
        $auteur2->setPrenom('Albert');
        
        $auteur3 = new Auteur();
        $auteur3->setNom('Zola');
        $auteur3->setPrenom('Émile');
        
        $manager->persist($auteur1);
        $manager->persist($auteur2);
        $manager->persist($auteur3);
        
        // Créer quelques livres
        $livre1 = new Livre();
        $livre1->setTitre('Les Misérables');
        $livre1->setResume('Roman historique publié en 1862');
        $livre1->setCouverture('miserables.jpg');
        $livre1->setAuteur($auteur1);
        
        $livre2 = new Livre();
        $livre2->setTitre('Notre-Dame de Paris');
        $livre2->setResume('Roman gothique publié en 1831');
        $livre2->setCouverture('notre-dame.jpg');
        $livre2->setAuteur($auteur1);
        
        $livre3 = new Livre();
        $livre3->setTitre('L\'Étranger');
        $livre3->setResume('Roman publié en 1942');
        $livre3->setCouverture('etranger.jpg');
        $livre3->setAuteur($auteur2);
        
        $livre4 = new Livre();
        $livre4->setTitre('La Peste');
        $livre4->setResume('Roman publié en 1947');
        $livre4->setCouverture('peste.jpg');
        $livre4->setAuteur($auteur2);
        
        $livre5 = new Livre();
        $livre5->setTitre('Germinal');
        $livre5->setResume('Roman publié en 1885');
        $livre5->setCouverture('germinal.jpg');
        $livre5->setAuteur($auteur3);
        
        $manager->persist($livre1);
        $manager->persist($livre2);
        $manager->persist($livre3);
        $manager->persist($livre4);
        $manager->persist($livre5);
        
        $manager->flush();
    }
}
