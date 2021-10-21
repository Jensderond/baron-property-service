<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211020144349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ADD external_id VARCHAR(255) NOT NULL, ADD created DATETIME NOT NULL, ADD updated DATETIME NOT NULL, ADD registration_type VARCHAR(50) NOT NULL, ADD sale TINYINT(1) DEFAULT NULL, ADD rent TINYINT(1) DEFAULT NULL, CHANGE external_storage external_storage INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP external_id, DROP created, DROP updated, DROP registration_type, DROP sale, DROP rent, CHANGE external_storage external_storage VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
