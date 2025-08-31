<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour créer la table user manquante
 */
final class Version20250512121153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table user pour l\'inscription et la connexion';
    }

    public function up(Schema $schema): void
    {
        // Création de la table user
        $this->addSql(<<<'SQL'
            CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Suppression de la table user
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
