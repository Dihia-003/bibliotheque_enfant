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
    name: 'app:download-cover-images',
    description: 'Télécharge des images de couverture pour les livres d\'enfants',
)]
class DownloadCoverImagesCommand extends Command
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

        // Liste des images de couverture à télécharger
        $images = [
            'charlie.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'harry.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'petit-prince.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'max.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'chat.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'fifi.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'gruffalo.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'brigands.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'arbre.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'ohboy.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'matilda.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
            'bete.jpg' => 'https://raw.githubusercontent.com/OpenLibrary/openlibrary/master/static/images/onix-placeholder.png',
        ];

        $downloadedCount = 0;
        $skippedCount = 0;

        // Télécharger les images
        foreach ($images as $filename => $url) {
            $targetPath = $uploadDir . $filename;

            // Vérifier si l'image existe déjà
            if ($fs->exists($targetPath)) {
                $io->text(sprintf('Image "%s" existe déjà. Ignorée.', $filename));
                $skippedCount++;
                continue;
            }

            try {
                // Télécharger l'image
                $content = @file_get_contents($url);
                if ($content === false) {
                    throw new \Exception("Impossible de télécharger l'image depuis: $url");
                }

                // Enregistrer l'image
                file_put_contents($targetPath, $content);
                $io->text(sprintf('Image "%s" téléchargée avec succès.', $filename));
                $downloadedCount++;
            } catch (\Exception $e) {
                $io->error(sprintf('Erreur lors du téléchargement de "%s": %s', $filename, $e->getMessage()));
            }
        }

        // Créer une image de placeholder si nécessaire
        $createPlaceholder = function ($filename, $text) use ($uploadDir, $fs, $io) {
            $targetPath = $uploadDir . $filename;
            
            if ($fs->exists($targetPath)) {
                return;
            }
            
            // Créer une image de placeholder
            $image = imagecreatetruecolor(300, 450);
            $bgColor = imagecolorallocate($image, 240, 240, 240);
            $textColor = imagecolorallocate($image, 50, 50, 50);
            
            imagefill($image, 0, 0, $bgColor);
            
            // Dessiner un cadre
            $borderColor = imagecolorallocate($image, 200, 200, 200);
            imagerectangle($image, 0, 0, 299, 449, $borderColor);
            
            // Ajouter le texte
            $fontSize = 5;
            $textWidth = imagefontwidth($fontSize) * strlen($text);
            $textHeight = imagefontheight($fontSize);
            
            $x = (300 - $textWidth) / 2;
            $y = (450 - $textHeight) / 2;
            
            imagestring($image, $fontSize, $x, $y, $text, $textColor);
            
            // Enregistrer l'image
            imagejpeg($image, $targetPath, 90);
            imagedestroy($image);
            
            $io->text(sprintf('Image placeholder "%s" créée.', $filename));
        };

        // Créer les placeholders pour les fichiers qui n'ont pas pu être téléchargés
        foreach ($images as $filename => $url) {
            $targetPath = $uploadDir . $filename;
            if (!$fs->exists($targetPath)) {
                $text = pathinfo($filename, PATHINFO_FILENAME);
                $createPlaceholder($filename, $text);
            }
        }

        $io->success(sprintf('%d images téléchargées, %d ignorées. Les placeholders ont été créés pour toutes les images manquantes.', $downloadedCount, $skippedCount));

        return Command::SUCCESS;
    }
} 