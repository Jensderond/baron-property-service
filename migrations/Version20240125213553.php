<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125213553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bog_object ADD service_cost_condition VARCHAR(20) DEFAULT NULL, ADD service_cost_vat TINYINT(1) DEFAULT NULL, CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bog_object DROP service_cost_condition, DROP service_cost_vat, CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }
}
