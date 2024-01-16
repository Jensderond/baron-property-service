<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240114132158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE construction_number (id INT AUTO_INCREMENT NOT NULL, construction_type_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, algemeen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', external_id INT NOT NULL, media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', address JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', description LONGTEXT DEFAULT NULL, teksten JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', diversen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', slug VARCHAR(255) NOT NULL, detail JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', rooms INT DEFAULT NULL, bedrooms INT DEFAULT NULL, financieel JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', status VARCHAR(255) NOT NULL, living_area INT DEFAULT NULL, price_condition VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', price_amount VARCHAR(255) NOT NULL, price_currency VARCHAR(3) NOT NULL, INDEX IDX_6F427D677A653FE7 (construction_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE construction_type (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, external_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', teksten JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', living_area VARCHAR(255) NOT NULL, algemeen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', type VARCHAR(255) DEFAULT NULL, rooms INT DEFAULT NULL, main_image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_5AD33F79166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, external_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, description_site LONGTEXT DEFAULT NULL, city VARCHAR(255) NOT NULL, zipcode VARCHAR(20) NOT NULL, province VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, category VARCHAR(255) NOT NULL, diversen JSON NOT NULL COMMENT \'(DC2Type:json)\', algemeen JSON NOT NULL COMMENT \'(DC2Type:json)\', slug VARCHAR(255) NOT NULL, media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', living_area VARCHAR(255) NOT NULL, rooms VARCHAR(255) NOT NULL, main_image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', plot VARCHAR(255) DEFAULT NULL, archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, algemeen JSON NOT NULL COMMENT \'(DC2Type:json)\', financieel JSON NOT NULL COMMENT \'(DC2Type:json)\', teksten JSON NOT NULL COMMENT \'(DC2Type:json)\', status VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, house_number INT DEFAULT NULL, house_number_addition VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, lat VARCHAR(255) DEFAULT NULL, lng VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, external_id INT NOT NULL, street VARCHAR(255) DEFAULT NULL, archived TINYINT(1) NOT NULL, build_year INT DEFAULT NULL, price INT DEFAULT NULL, energy_class VARCHAR(25) DEFAULT NULL, image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', etages JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', overig_onroerend_goed JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', buitenruimte JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', price_condition VARCHAR(255) NOT NULL, plot VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE construction_number ADD CONSTRAINT FK_6F427D677A653FE7 FOREIGN KEY (construction_type_id) REFERENCES construction_type (id)');
        $this->addSql('ALTER TABLE construction_type ADD CONSTRAINT FK_5AD33F79166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE construction_number DROP FOREIGN KEY FK_6F427D677A653FE7');
        $this->addSql('ALTER TABLE construction_type DROP FOREIGN KEY FK_5AD33F79166D1F9C');
        $this->addSql('DROP TABLE construction_number');
        $this->addSql('DROP TABLE construction_type');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE property');
    }
}
