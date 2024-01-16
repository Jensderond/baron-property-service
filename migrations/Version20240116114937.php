<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116114937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE construction_number ADD media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project ADD media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property ADD media_hash CHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP media_hash');
        $this->addSql('ALTER TABLE property DROP media_hash');
        $this->addSql('ALTER TABLE construction_number DROP media_hash');
    }
}
