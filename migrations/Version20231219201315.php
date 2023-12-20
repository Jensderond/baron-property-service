<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219201315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE construction_number (id INT AUTO_INCREMENT NOT NULL, construction_type_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, algemeen LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', external_id INT NOT NULL, media LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', address LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', description LONGTEXT DEFAULT NULL, teksten LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', diversen LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_6F427D677A653FE7 (construction_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE construction_type (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, external_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, algemeen LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', media LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', teksten LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_5AD33F79166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE land_registry_data (id INT AUTO_INCREMENT NOT NULL, aandeel VARCHAR(255) DEFAULT NULL, afgekocht_tot VARCHAR(255) DEFAULT NULL, afkoopoptie TINYINT(1) DEFAULT NULL, eeuwig_afgekocht TINYINT(1) DEFAULT NULL, eigendomssoort VARCHAR(255) DEFAULT NULL, einddatum DATE DEFAULT NULL, erfpacht_per_jaar INT DEFAULT NULL, erfpachtduur VARCHAR(255) DEFAULT NULL, erfpachtgever VARCHAR(255) DEFAULT NULL, erfpachtprijsvorm VARCHAR(255) DEFAULT NULL, gemeente VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, property_id INT DEFAULT NULL, mimetype VARCHAR(40) NOT NULL, url VARCHAR(500) NOT NULL, type VARCHAR(40) DEFAULT NULL, position INT DEFAULT NULL, INDEX IDX_6A2CA10C549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, external_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, description_site LONGTEXT DEFAULT NULL, city VARCHAR(255) NOT NULL, zipcode VARCHAR(20) NOT NULL, province VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, diversen LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', algemeen LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', slug VARCHAR(255) NOT NULL, media LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', finance LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, detail_id INT DEFAULT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, algemeen LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', financieel LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', teksten LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', status VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, house_number INT DEFAULT NULL, house_number_addition VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip VARCHAR(255) DEFAULT NULL, lat VARCHAR(255) DEFAULT NULL, lng VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, external_id INT NOT NULL, street VARCHAR(255) DEFAULT NULL, archived TINYINT(1) DEFAULT NULL, build_year INT DEFAULT NULL, price INT DEFAULT NULL, rental_price INT DEFAULT NULL, energy_class VARCHAR(25) DEFAULT NULL, UNIQUE INDEX UNIQ_8BF21CDED8D003BB (detail_id), UNIQUE INDEX UNIQ_8BF21CDE3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_detail (id INT NOT NULL, buitenruimte LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', etages LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', overig_onroerend_goed LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE construction_number ADD CONSTRAINT FK_6F427D677A653FE7 FOREIGN KEY (construction_type_id) REFERENCES construction_type (id)');
        $this->addSql('ALTER TABLE construction_type ADD CONSTRAINT FK_5AD33F79166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDED8D003BB FOREIGN KEY (detail_id) REFERENCES property_detail (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE3DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE construction_number DROP FOREIGN KEY FK_6F427D677A653FE7');
        $this->addSql('ALTER TABLE construction_type DROP FOREIGN KEY FK_5AD33F79166D1F9C');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C549213EC');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDED8D003BB');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE3DA5256D');
        $this->addSql('DROP TABLE construction_number');
        $this->addSql('DROP TABLE construction_type');
        $this->addSql('DROP TABLE land_registry_data');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE property_detail');
    }
}
