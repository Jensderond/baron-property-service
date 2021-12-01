<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125092742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan (id INT NOT NULL, property_id INT NOT NULL, sort INT NOT NULL, url LONGTEXT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_DD5A5B7D549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7D549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE property ADD external_plans LONGTEXT DEFAULT NULL, ADD external_panoramas LONGTEXT DEFAULT NULL, DROP bathrooms');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE plan');
        $this->addSql('ALTER TABLE property ADD bathrooms INT DEFAULT NULL, DROP external_plans, DROP external_panoramas');
    }
}
