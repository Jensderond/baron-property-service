<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211021094407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ADD update_hash VARCHAR(255) DEFAULT NULL, ADD price INT DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD street_address VARCHAR(255) DEFAULT NULL, ADD house_number VARCHAR(255) DEFAULT NULL, ADD house_number_addition VARCHAR(255) DEFAULT NULL, ADD street VARCHAR(255) DEFAULT NULL, ADD zip VARCHAR(255) DEFAULT NULL, ADD city VARCHAR(255) DEFAULT NULL, ADD latitude VARCHAR(255) DEFAULT NULL, ADD longitude VARCHAR(255) DEFAULT NULL, ADD acceptance VARCHAR(255) DEFAULT NULL, ADD energy_class VARCHAR(10) DEFAULT NULL, ADD type VARCHAR(255) DEFAULT NULL, ADD subtype VARCHAR(255) DEFAULT NULL, ADD new_construction TINYINT(1) NOT NULL, ADD pets_allowed TINYINT(1) NOT NULL, ADD images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD image VARCHAR(255) DEFAULT NULL, ADD titles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', ADD meta_keywords LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', ADD meta_descriptions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', ADD price_type_sale VARCHAR(255) DEFAULT NULL, DROP external_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ADD external_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP update_hash, DROP price, DROP address, DROP street_address, DROP house_number, DROP house_number_addition, DROP street, DROP zip, DROP city, DROP latitude, DROP longitude, DROP acceptance, DROP energy_class, DROP type, DROP subtype, DROP new_construction, DROP pets_allowed, DROP images, DROP image, DROP titles, DROP meta_keywords, DROP meta_descriptions, DROP price_type_sale');
    }
}
