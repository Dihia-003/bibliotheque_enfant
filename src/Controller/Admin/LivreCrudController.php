<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class LivreCrudController extends AbstractCrudController
{
    private $slugger;
    
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    
    public static function getEntityFqcn(): string
    {
        return Livre::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Livre')
            ->setEntityLabelInPlural('Livres')
            ->setPageTitle('index', 'Liste des livres')
            ->setPageTitle('edit', fn (Livre $livre) => sprintf('Modifier %s', $livre->getTitre()));
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre')
                ->setLabel('Titre'),
                
            TextEditorField::new('resume')
                ->setLabel('Résumé'),
                
            ImageField::new('couverture')
                ->setLabel('Couverture')
                ->setBasePath('uploads/couvertures')
                ->setUploadDir('public/uploads/couvertures')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            
            AssociationField::new('auteur')
                ->setRequired(true)
                ->setLabel('Auteur')
                ->setFormTypeOption('choice_label', function($auteur) {
                    return $auteur->getPrenom() . ' ' . $auteur->getNom();
                }),
        ];
    }
    
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Nettoyer le résumé des balises HTML avant de sauvegarder
        if ($entityInstance instanceof Livre && $entityInstance->getResume()) {
            $cleanResume = strip_tags($entityInstance->getResume());
            $entityInstance->setResume($cleanResume);
        }
        
        parent::persistEntity($entityManager, $entityInstance);
    }
    
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Nettoyer le résumé des balises HTML avant de mettre à jour
        if ($entityInstance instanceof Livre && $entityInstance->getResume()) {
            $cleanResume = strip_tags($entityInstance->getResume());
            $entityInstance->setResume($cleanResume);
        }
        
        parent::updateEntity($entityManager, $entityInstance);
    }
}
