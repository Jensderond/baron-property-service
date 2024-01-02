<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240102155649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE3DA5256D');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C549213EC');
        $this->addSql('DROP TABLE media');
        $this->addSql('ALTER TABLE construction_number ADD detail LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD rooms INT DEFAULT NULL, ADD bedrooms INT DEFAULT NULL, ADD financieel LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD status VARCHAR(255) NOT NULL, ADD living_area INT DEFAULT NULL, ADD price_condition VARCHAR(255) NOT NULL, ADD price_amount VARCHAR(255) NOT NULL, ADD price_currency VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE construction_type ADD type VARCHAR(255) DEFAULT NULL, ADD rooms INT DEFAULT NULL, CHANGE slug living_area VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD living_area VARCHAR(255) NOT NULL, ADD plot VARCHAR(255) DEFAULT NULL, CHANGE finance main_image LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('DROP INDEX UNIQ_8BF21CDE3DA5256D ON property');
        $this->addSql('ALTER TABLE property ADD image LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD media LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, property_id INT DEFAULT NULL, mimetype VARCHAR(40) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, position INT DEFAULT NULL, INDEX IDX_6A2CA10C549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property ADD image_id INT DEFAULT NULL, DROP image, DROP media, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE3DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BF21CDE3DA5256D ON property (image_id)');
        $this->addSql('ALTER TABLE project DROP created_at, DROP updated_at, DROP living_area, DROP plot, CHANGE main_image finance LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE construction_number DROP detail, DROP rooms, DROP bedrooms, DROP financieel, DROP status, DROP living_area, DROP price_condition, DROP price_amount, DROP price_currency');
        $this->addSql('ALTER TABLE construction_type DROP type, DROP rooms, CHANGE living_area slug VARCHAR(255) NOT NULL');
    }
}
