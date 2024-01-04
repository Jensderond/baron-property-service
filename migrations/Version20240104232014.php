<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240104232014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDED8D003BB');
        $this->addSql('DROP TABLE property_detail');
        $this->addSql('ALTER TABLE construction_number CHANGE algemeen algemeen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE address address JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE teksten teksten JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE diversen diversen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE detail detail JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE financieel financieel JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE construction_type CHANGE algemeen algemeen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE teksten teksten JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE main_image main_image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE project CHANGE diversen diversen JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE algemeen algemeen JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE main_image main_image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('DROP INDEX UNIQ_8BF21CDED8D003BB ON property');
        $this->addSql('ALTER TABLE property ADD etages JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD overig_onroerend_goed JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD buitenruimte JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP detail_id, CHANGE algemeen algemeen JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE financieel financieel JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE teksten teksten JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE image image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE property_detail (id INT NOT NULL, buitenruimte JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', etages JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', overig_onroerend_goed JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE construction_number CHANGE algemeen algemeen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE address address JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE teksten teksten JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE diversen diversen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE detail detail JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE financieel financieel JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE construction_type CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE teksten teksten JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE algemeen algemeen JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE main_image main_image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE project CHANGE diversen diversen JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE algemeen algemeen JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE main_image main_image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE property ADD detail_id INT DEFAULT NULL, DROP etages, DROP overig_onroerend_goed, DROP buitenruimte, CHANGE algemeen algemeen JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE financieel financieel JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE teksten teksten JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE image image JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE media media JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDED8D003BB FOREIGN KEY (detail_id) REFERENCES property_detail (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BF21CDED8D003BB ON property (detail_id)');
    }
}
