<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114085724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update foreign keys for new user/group tables.';
    }

    public function up(Schema $schema): void
    {
        // Remove foreign keys for FOS tables
        $this->addSql('ALTER TABLE system_group DROP FOREIGN KEY FK_390FDF5FFE54D947');
        $this->addSql('ALTER TABLE report_group DROP FOREIGN KEY FK_47DC43BCFE54D947');
        $this->addSql('ALTER TABLE group_report_themes DROP FOREIGN KEY FK_63582903FE54D947');
        $this->addSql('ALTER TABLE group_system_themes DROP FOREIGN KEY FK_5A25495DFE54D947');

        // Drop internal FOS user -> groups mapping keys.
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447A76ED395');

        // Remove tables, data was migrated in last migration step.
        $this->addSql('DROP TABLE fos_group');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('DROP TABLE fos_user');

        // Add new foreign keys mapping the new user/group tables
        $this->addSql('ALTER TABLE group_system_themes ADD CONSTRAINT FK_5A25495DFE54D947 FOREIGN KEY (group_id) REFERENCES `user_group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_report_themes ADD CONSTRAINT FK_63582903FE54D947 FOREIGN KEY (group_id) REFERENCES `user_group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_group ADD CONSTRAINT FK_47DC43BCFE54D947 FOREIGN KEY (group_id) REFERENCES `user_group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE system_group ADD CONSTRAINT FK_390FDF5FFE54D947 FOREIGN KEY (group_id) REFERENCES `user_group` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // There is now down for this migration. So left empty.
    }
}
