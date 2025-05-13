<?php

namespace App\Command;

use App\Entity\Auteur;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-children-books',
    description: 'Ajoute des livres pour enfants à la bibliothèque',
)]
class AddChildrenBooksCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Liste des auteurs pour enfants
        $authors = [
            ['prenom' => 'Roald', 'nom' => 'Dahl'],
            ['prenom' => 'J.K.', 'nom' => 'Rowling'],
            ['prenom' => 'Antoine', 'nom' => 'de Saint-Exupéry'],
            ['prenom' => 'Maurice', 'nom' => 'Sendak'],
            ['prenom' => 'Dr.', 'nom' => 'Seuss'],
            ['prenom' => 'Astrid', 'nom' => 'Lindgren'],
            ['prenom' => 'Julia', 'nom' => 'Donaldson'],
            ['prenom' => 'Tomi', 'nom' => 'Ungerer'],
            ['prenom' => 'Claude', 'nom' => 'Ponti'],
            ['prenom' => 'Marie-Aude', 'nom' => 'Murail'],
        ];

        // Liste des livres pour enfants
        $books = [
            [
                'titre' => 'Charlie et la Chocolaterie',
                'resume' => 'Charlie Bucket, un garçon pauvre, trouve un ticket d\'or qui lui permet de visiter la mystérieuse chocolaterie de Willy Wonka. Un monde fantastique rempli de sucreries et de surprises l\'attend !',
                'auteur_index' => 0,
                'couverture' => 'charlie.jpg'
            ],
            [
                'titre' => 'Harry Potter à l\'école des sorciers',
                'resume' => 'Harry Potter découvre qu\'il est un sorcier et commence sa première année à Poudlard, l\'école de sorcellerie. Il y fait des amis, apprend la magie et affronte des dangers.',
                'auteur_index' => 1,
                'couverture' => 'harry.jpg'
            ],
            [
                'titre' => 'Le Petit Prince',
                'resume' => 'Un aviateur échoué dans le désert rencontre un petit prince venu d\'une autre planète. À travers leurs conversations, ils explorent des vérités importantes sur la vie et l\'amitié.',
                'auteur_index' => 2,
                'couverture' => 'petit-prince.jpg'
            ],
            [
                'titre' => 'Max et les Maximonstres',
                'resume' => 'Max, envoyé dans sa chambre sans dîner, s\'évade dans un monde imaginaire peuplé de monstres sauvages dont il devient le roi.',
                'auteur_index' => 3,
                'couverture' => 'max.jpg'
            ],
            [
                'titre' => 'Le Chat Chapeauté',
                'resume' => 'Un chat malicieux portant un chapeau haut-de-forme rend visite à deux enfants et transforme leur journée ennuyeuse en aventure chaotique.',
                'auteur_index' => 4,
                'couverture' => 'chat.jpg'
            ],
            [
                'titre' => 'Fifi Brindacier',
                'resume' => 'Fifi, une fille de 9 ans à la force surhumaine, vit seule avec son singe et son cheval. Son indépendance et son esprit rebelle la conduisent dans des aventures extraordinaires.',
                'auteur_index' => 5,
                'couverture' => 'fifi.jpg'
            ],
            [
                'titre' => 'Gruffalo',
                'resume' => 'Une petite souris rusée invente un monstre effrayant appelé le Gruffalo pour échapper à ses prédateurs. Mais que se passe-t-il quand elle rencontre un vrai Gruffalo ?',
                'auteur_index' => 6,
                'couverture' => 'gruffalo.jpg'
            ],
            [
                'titre' => 'Les Trois Brigands',
                'resume' => 'Trois méchants brigands qui terrorisent la campagne voient leur vie transformée après leur rencontre avec une petite orpheline courageuse.',
                'auteur_index' => 7,
                'couverture' => 'brigands.jpg'
            ],
            [
                'titre' => 'L\'Arbre Sans Fin',
                'resume' => 'Hippolène vit dans un arbre sans fin avec sa famille. Après la mort de sa grand-mère, elle entreprend un voyage initiatique à travers les branches de l\'arbre.',
                'auteur_index' => 8,
                'couverture' => 'arbre.jpg'
            ],
            [
                'titre' => 'Oh, Boy!',
                'resume' => 'Trois enfants orphelins cherchent une famille. Leur demi-frère Barthélémy et leur tante demi-sœur Josiane sont les seuls parents qui leur restent, mais chacun a ses propres raisons de vouloir les accueillir.',
                'auteur_index' => 9,
                'couverture' => 'ohboy.jpg'
            ],
            [
                'titre' => 'Matilda',
                'resume' => 'Matilda, une petite fille surdouée, développe des pouvoirs télékinétiques qu\'elle utilise pour se défendre contre ses parents négligents et la terrible directrice de son école.',
                'auteur_index' => 0,
                'couverture' => 'matilda.jpg'
            ],
            [
                'titre' => 'La bête sous la ville',
                'resume' => 'Tom et ses amis partent à la recherche d\'une créature mystérieuse qui vivrait dans les égouts de leur ville. Une aventure pleine de rebondissements et de mystères !',
                'auteur_index' => 8,
                'couverture' => 'bete.jpg'
            ],
        ];

        // Vérifier si la base de données contient déjà des données
        $existingAuthors = $this->entityManager->getRepository(Auteur::class)->findAll();
        $existingBooks = $this->entityManager->getRepository(Livre::class)->findAll();

        if (!empty($existingBooks)) {
            $io->note('Des livres existent déjà dans la base de données. Ajout des nouveaux livres uniquement.');
            
            // Récupérer les titres existants
            $existingTitles = array_map(function($book) {
                return $book->getTitre();
            }, $existingBooks);
            
            // Filtrer pour ne garder que les nouveaux livres
            $booksToAdd = array_filter($books, function($book) use ($existingTitles) {
                return !in_array($book['titre'], $existingTitles);
            });
            
            $books = $booksToAdd;
        }

        // Créer ou récupérer les auteurs
        $authorEntities = [];
        foreach ($authors as $index => $authorData) {
            $existingAuthor = $this->entityManager->getRepository(Auteur::class)->findOneBy([
                'prenom' => $authorData['prenom'],
                'nom' => $authorData['nom']
            ]);

            if ($existingAuthor) {
                $authorEntities[$index] = $existingAuthor;
            } else {
                $author = new Auteur();
                $author->setPrenom($authorData['prenom']);
                $author->setNom($authorData['nom']);
                $this->entityManager->persist($author);
                $authorEntities[$index] = $author;
            }
        }

        // Flush pour s'assurer que les auteurs ont des IDs
        $this->entityManager->flush();

        // Créer les livres
        $addedBooks = 0;
        foreach ($books as $bookData) {
            $book = new Livre();
            $book->setTitre($bookData['titre']);
            $book->setResume($bookData['resume']);
            $book->setCouverture($bookData['couverture']);
            $book->setAuteur($authorEntities[$bookData['auteur_index']]);
            
            $this->entityManager->persist($book);
            $addedBooks++;
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d livres pour enfants ont été ajoutés à la bibliothèque !', $addedBooks));

        return Command::SUCCESS;
    }
} 