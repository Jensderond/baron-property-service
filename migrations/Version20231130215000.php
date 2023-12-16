<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130215000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE land_registry_data (id INT AUTO_INCREMENT NOT NULL, aandeel VARCHAR(255) DEFAULT NULL, afgekocht_tot VARCHAR(255) DEFAULT NULL, afkoopoptie TINYINT(1) DEFAULT NULL, eeuwig_afgekocht TINYINT(1) DEFAULT NULL, eigendomssoort VARCHAR(255) DEFAULT NULL, einddatum DATE DEFAULT NULL, erfpacht_per_jaar INT DEFAULT NULL, erfpachtduur VARCHAR(255) DEFAULT NULL, erfpachtgever VARCHAR(255) DEFAULT NULL, erfpachtprijsvorm VARCHAR(255) DEFAULT NULL, gemeente VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, detail_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, algemeen LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', financieel LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', teksten LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', status VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, house_number INT DEFAULT NULL, house_number_addition VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, lat VARCHAR(255) DEFAULT NULL, lng VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, external_id INT NOT NULL, UNIQUE INDEX UNIQ_8BF21CDED8D003BB (detail_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_detail (id INT AUTO_INCREMENT NOT NULL, kadaster_id INT DEFAULT NULL, buitenruimte LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_507110EF7F4EB63A (kadaster_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDED8D003BB FOREIGN KEY (detail_id) REFERENCES property_detail (id)');
        $this->addSql('ALTER TABLE property_detail ADD CONSTRAINT FK_507110EF7F4EB63A FOREIGN KEY (kadaster_id) REFERENCES land_registry_data (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDED8D003BB');
        $this->addSql('ALTER TABLE property_detail DROP FOREIGN KEY FK_507110EF7F4EB63A');
        $this->addSql('DROP TABLE land_registry_data');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE property_detail');
    }
}
