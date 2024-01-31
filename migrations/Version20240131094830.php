<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131094830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bog_object CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project ADD lat NUMERIC(10, 7) DEFAULT NULL, ADD lng NUMERIC(10, 7) DEFAULT NULL, CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bog_object CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project DROP lat, DROP lng, CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }
}
