<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240123171005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bog_object (id INT AUTO_INCREMENT NOT NULL, external_id INT NOT NULL, title VARCHAR(255) NOT NULL, diversen JSON NOT NULL COMMENT \'(DC2Type:json)\', city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, house_number VARCHAR(20) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, main_function VARCHAR(255) NOT NULL, status VARCHAR(30) NOT NULL, media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', description LONGTEXT DEFAULT NULL, media_hash CHAR(32) NOT NULL, finance JSON NOT NULL COMMENT \'(DC2Type:json)\', price INT DEFAULT NULL, energy_class VARCHAR(25) DEFAULT NULL, build_year INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', facilities VARCHAR(255) DEFAULT NULL, local_services VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bog_object');
        $this->addSql('ALTER TABLE construction_number CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE project CHANGE media_hash media_hash CHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE media_hash media_hash CHAR(32) NOT NULL');
    }
}
