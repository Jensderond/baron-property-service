<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240120092114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property ADD description LONGTEXT DEFAULT NULL, ADD rooms INT DEFAULT NULL, ADD bedrooms INT DEFAULT NULL, ADD living_area INT DEFAULT NULL, CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property DROP description, DROP rooms, DROP bedrooms, DROP living_area, CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }
}
