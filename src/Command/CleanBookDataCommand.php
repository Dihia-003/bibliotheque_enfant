<?php

namespace App\Command;

use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clean-book-data',
    description: 'Nettoie les balises HTML des résumés de livres',
)]
class CleanBookDataCommand extends Command
{
    private $livreRepository;
    private $entityManager;

    public function __construct(LivreRepository $livreRepository, EntityManagerInterface $entityManager)
    {
        $this->livreRepository = $livreRepository;
        $this->entityManager = $entityManager;
        
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $livres = $this->livreRepository->findAll();
        $count = 0;
        
        foreach ($livres as $livre) {
            $resume = $livre->getResume();
            if ($resume) {
                $cleanResume = strip_tags($resume);
                if ($cleanResume !== $resume) {
                    $livre->setResume($cleanResume);
                    $count++;
                }
            }
        }
        
        $this->entityManager->flush();
        
        $io->success("$count livres ont été mis à jour pour supprimer les balises HTML.");

        return Command::SUCCESS;
    }
} 