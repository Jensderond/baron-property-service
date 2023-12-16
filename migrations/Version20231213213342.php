<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231213213342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE construction_number (id INT AUTO_INCREMENT NOT NULL, construction_type_id INT NOT NULL, project_id INT NOT NULL, algemeen LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', external_id INT NOT NULL, media LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', teksten LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', diversen LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', active TINYINT(1) NOT NULL, INDEX IDX_6F427D677A653FE7 (construction_type_id), INDEX IDX_6F427D67166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE construction_type (id INT AUTO_INCREMENT NOT NULL, external_id INT NOT NULL, algemeen LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', media LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', teksten LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, external_id INT NOT NULL, algemeen LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', diversen LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', city VARCHAR(255) NOT NULL, zipcode VARCHAR(20) NOT NULL, province VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE construction_number ADD CONSTRAINT FK_6F427D677A653FE7 FOREIGN KEY (construction_type_id) REFERENCES construction_type (id)');
        $this->addSql('ALTER TABLE construction_number ADD CONSTRAINT FK_6F427D67166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE construction_number DROP FOREIGN KEY FK_6F427D677A653FE7');
        $this->addSql('ALTER TABLE construction_number DROP FOREIGN KEY FK_6F427D67166D1F9C');
        $this->addSql('DROP TABLE construction_number');
        $this->addSql('DROP TABLE construction_type');
        $this->addSql('DROP TABLE project');
    }
}
