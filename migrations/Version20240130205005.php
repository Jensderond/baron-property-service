<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130205005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bog_object CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE construction_number ADD readable_status VARCHAR(50) DEFAULT NULL, CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bog_object CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE construction_number DROP readable_status, CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }
}
