<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:create-cover-images',
    description: 'Crée des images de couverture vides pour les livres d\'enfants',
)]
class CreateCoverImagesCommand extends Command
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
        $this->kernel = $kernel;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Chemin du répertoire des couvertures
        $uploadDir = $this->kernel->getProjectDir() . '/public/uploads/couvertures/';

        // S'assurer que le répertoire existe
        $fs = new Filesystem();
        if (!$fs->exists($uploadDir)) {
            $fs->mkdir($uploadDir);
            $io->note('Création du répertoire de couvertures.');
        }

        // Liste des images de couverture à créer
        $images = [
            'charlie.jpg',
            'harry.jpg',
            'petit-prince.jpg',
            'max.jpg',
            'chat.jpg',
            'fifi.jpg',
            'gruffalo.jpg',
            'brigands.jpg',
            'arbre.jpg',
            'ohboy.jpg',
            'matilda.jpg',
            'bete.jpg',
        ];

        $createdCount = 0;
        $skippedCount = 0;

        // Créer des fichiers image vides pour chaque livre
        foreach ($images as $filename) {
            $targetPath = $uploadDir . $filename;

            // Vérifier si l'image existe déjà
            if ($fs->exists($targetPath)) {
                $io->text(sprintf('Image "%s" existe déjà. Ignorée.', $filename));
                $skippedCount++;
                continue;
            }

            // Créer un fichier image vide
            try {
                // Contenu d'une petite image JPEG vide (1x1 pixel)
                $emptyJpegData = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDAREAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAACv/EABQQAQAAAAAAAAAAAAAAAAAAAAD/xAAUAQEAAAAAAAAAAAAAAAAAAAAA/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AfwD/2Q==');
                
                file_put_contents($targetPath, $emptyJpegData);
                $io->text(sprintf('Image vide "%s" créée.', $filename));
                $createdCount++;
            } catch (\Exception $e) {
                $io->error(sprintf('Erreur lors de la création de "%s": %s', $filename, $e->getMessage()));
            }
        }

        $io->success(sprintf('%d images créées, %d ignorées.', $createdCount, $skippedCount));

        return Command::SUCCESS;
    }
} 