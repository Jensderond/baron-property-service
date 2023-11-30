<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130213320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ADD detail_id INT DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD house_number INT DEFAULT NULL, ADD house_number_addition VARCHAR(255) DEFAULT NULL, ADD city VARCHAR(255) DEFAULT NULL, ADD zip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDED8D003BB FOREIGN KEY (detail_id) REFERENCES property_detail (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BF21CDED8D003BB ON property (detail_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDED8D003BB');
        $this->addSql('DROP INDEX UNIQ_8BF21CDED8D003BB ON property');
        $this->addSql('ALTER TABLE property DROP detail_id, DROP address, DROP house_number, DROP house_number_addition, DROP city, DROP zip');
    }
}
