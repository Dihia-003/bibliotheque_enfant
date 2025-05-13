<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un utilisateur administrateur',
)]
class CreateAdminUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si un admin existe déjà
        $existingAdmin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@bibliotheque.fr']);
        
        if ($existingAdmin) {
            $io->warning('Un utilisateur administrateur existe déjà avec l\'email admin@bibliotheque.fr');
            return Command::SUCCESS;
        }

        // Créer un nouvel administrateur
        $user = new User();
        $user->setEmail('admin@bibliotheque.fr');
        $user->setRoles(['ROLE_ADMIN']);
        
        // Définir un mot de passe (MotDePasse123! dans cet exemple)
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'MotDePasse123!'
        );
        $user->setPassword($hashedPassword);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Utilisateur administrateur créé avec succès !');
        $io->text('Email: admin@bibliotheque.fr');
        $io->text('Mot de passe: MotDePasse123!');
        $io->text('Veuillez changer ce mot de passe après votre première connexion.');

        return Command::SUCCESS;
    }
} 