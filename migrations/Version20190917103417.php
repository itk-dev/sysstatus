<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190917103417 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE report_group (report_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_47DC43BC4BD2A4C0 (report_id), INDEX IDX_47DC43BCFE54D947 (group_id), PRIMARY KEY(report_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_system_themes (group_id INT NOT NULL, theme_id INT NOT NULL, INDEX IDX_5A25495DFE54D947 (group_id), INDEX IDX_5A25495D59027487 (theme_id), PRIMARY KEY(group_id, theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_report_themes (group_id INT NOT NULL, theme_id INT NOT NULL, INDEX IDX_63582903FE54D947 (group_id), INDEX IDX_6358290359027487 (theme_id), PRIMARY KEY(group_id, theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system_group (system_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_390FDF5FD0952FA5 (system_id), INDEX IDX_390FDF5FFE54D947 (group_id), PRIMARY KEY(system_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report_group ADD CONSTRAINT FK_47DC43BC4BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_group ADD CONSTRAINT FK_47DC43BCFE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_system_themes ADD CONSTRAINT FK_5A25495DFE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_system_themes ADD CONSTRAINT FK_5A25495D59027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_report_themes ADD CONSTRAINT FK_63582903FE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_report_themes ADD CONSTRAINT FK_6358290359027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE system_group ADD CONSTRAINT FK_390FDF5FD0952FA5 FOREIGN KEY (system_id) REFERENCES system (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE system_group ADD CONSTRAINT FK_390FDF5FFE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778459027487');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784FE54D947');
        $this->addSql('DROP INDEX IDX_C42F7784FE54D947 ON report');
        $this->addSql('DROP INDEX IDX_C42F778459027487 ON report');
        $this->addSql('ALTER TABLE report DROP theme_id, DROP group_id');
        $this->addSql('ALTER TABLE system DROP FOREIGN KEY FK_C94D118B59027487');
        $this->addSql('ALTER TABLE system DROP FOREIGN KEY FK_C94D118BFE54D947');
        $this->addSql('DROP INDEX IDX_C94D118BFE54D947 ON system');
        $this->addSql('DROP INDEX IDX_C94D118B59027487 ON system');
        $this->addSql('ALTER TABLE system DROP theme_id, DROP group_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE report_group');
        $this->addSql('DROP TABLE group_system_themes');
        $this->addSql('DROP TABLE group_report_themes');
        $this->addSql('DROP TABLE system_group');
        $this->addSql('ALTER TABLE report ADD theme_id INT DEFAULT NULL, ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778459027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784FE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id)');
        $this->addSql('CREATE INDEX IDX_C42F7784FE54D947 ON report (group_id)');
        $this->addSql('CREATE INDEX IDX_C42F778459027487 ON report (theme_id)');
        $this->addSql('ALTER TABLE system ADD theme_id INT DEFAULT NULL, ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE system ADD CONSTRAINT FK_C94D118B59027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE system ADD CONSTRAINT FK_C94D118BFE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id)');
        $this->addSql('CREATE INDEX IDX_C94D118BFE54D947 ON system (group_id)');
        $this->addSql('CREATE INDEX IDX_C94D118B59027487 ON system (theme_id)');
    }
}
