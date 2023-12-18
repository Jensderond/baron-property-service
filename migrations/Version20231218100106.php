<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218100106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, property_id INT DEFAULT NULL, mimetype VARCHAR(40) NOT NULL, url VARCHAR(500) NOT NULL, type VARCHAR(40) DEFAULT NULL, position INT DEFAULT NULL, INDEX IDX_6A2CA10C549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property ADD image_id INT DEFAULT NULL, ADD build_year INT NOT NULL, ADD price INT DEFAULT NULL, ADD rental_price INT DEFAULT NULL, ADD energy_class VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE3DA5256D FOREIGN KEY (image_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BF21CDE3DA5256D ON property (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE3DA5256D');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C549213EC');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP INDEX UNIQ_8BF21CDE3DA5256D ON property');
        $this->addSql('ALTER TABLE property DROP image_id, DROP build_year, DROP price, DROP rental_price, DROP energy_class');
    }
}
