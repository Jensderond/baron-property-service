<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211215143053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property ADD rental_condition VARCHAR(20) DEFAULT NULL, ADD availability VARCHAR(20) DEFAULT NULL, ADD available_from DATE DEFAULT NULL, ADD rented_till DATE DEFAULT NULL, ADD min_contract_length INT DEFAULT NULL, ADD contract_length INT DEFAULT NULL, ADD service_costs INT DEFAULT NULL, ADD owners_contribution_community INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP rental_condition, DROP availability, DROP available_from, DROP rented_till, DROP min_contract_length, DROP contract_length, DROP service_costs, DROP owners_contribution_community');
    }
}
