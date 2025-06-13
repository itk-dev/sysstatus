<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114090744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(191) NOT NULL, CHANGE username username VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE import_run CHANGE datetime datetime DATETIME DEFAULT NULL, CHANGE result result TINYINT(1) DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE question CHANGE question question LONGTEXT DEFAULT NULL, CHANGE sort_order sort_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE sys_link sys_link VARCHAR(255) DEFAULT NULL, CHANGE sys_system_owner sys_system_owner VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE self_service_available_from_item CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE system CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE sys_link sys_link VARCHAR(255) DEFAULT NULL, CHANGE sys_system_owner sys_system_owner VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_9775E7085E237E06 ON theme');
        $this->addSql('ALTER TABLE theme CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE theme_category CHANGE sort_order sort_order INT DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE question CHANGE question question LONGTEXT NOT NULL, CHANGE sort_order sort_order INT NOT NULL');
        $this->addSql('ALTER TABLE category CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE self_service_available_from_item CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE theme_category CHANGE sort_order sort_order INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE system CHANGE name name VARCHAR(255) NOT NULL, CHANGE sys_system_owner sys_system_owner VARCHAR(255) NOT NULL, CHANGE sys_link sys_link VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE report CHANGE name name VARCHAR(255) NOT NULL, CHANGE sys_system_owner sys_system_owner VARCHAR(255) NOT NULL, CHANGE sys_link sys_link VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE theme CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7085E237E06 ON theme (name)');
        $this->addSql('ALTER TABLE import_run CHANGE datetime datetime DATETIME NOT NULL, CHANGE result result TINYINT(1) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL');
    }
}
