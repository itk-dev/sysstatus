<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903091717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(191) NOT NULL, CHANGE username username VARCHAR(191) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_4B019DDB5E237E06 ON fos_group');
        $this->addSql('ALTER TABLE fos_group CHANGE name name VARCHAR(255) NOT NULL, CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE fos_user ADD password VARCHAR(255) NOT NULL, CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON fos_user (username)');
        $this->addSql('DROP INDEX UNIQ_9775E7085E237E06 ON theme');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7085E237E06 ON theme (name)');
        $this->addSql('ALTER TABLE fos_group CHANGE name name VARCHAR(180) NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B019DDB5E237E06 ON fos_group (name)');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USERNAME ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP password, CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
