<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250729142154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE report CHANGE sys_status sys_status VARCHAR(255) DEFAULT NULL, CHANGE sys_owner_sub sys_owner_sub VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX archived_status_owner_idx ON report (archived_at, sys_status, sys_owner_sub)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX sys_internal_id_idx ON report (sys_internal_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE system CHANGE sys_owner_sub sys_owner_sub VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX archived_status_owner_idx ON system (archived_at, sys_status, sys_owner_sub)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX sys_internal_id_idx ON system (sys_internal_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX archived_status_owner_idx ON system
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX sys_internal_id_idx ON system
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE system CHANGE sys_owner_sub sys_owner_sub LONGTEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX archived_status_owner_idx ON report
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX sys_internal_id_idx ON report
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report CHANGE sys_status sys_status LONGTEXT DEFAULT NULL, CHANGE sys_owner_sub sys_owner_sub LONGTEXT DEFAULT NULL
        SQL);
    }
}
