<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114101024 extends AbstractMigration
{
    /**
     * @var array<int, mixed>
     */
    private array $report_groups = [];

    /**
     * @var array<int, mixed>
     */
    private array $system_groups = [];

    public function getDescription(): string
    {
        return 'Migrate relationship to user groups';
    }

    public function preUp(Schema $schema): void
    {
        $this->report_groups = $this->getData('SELECT * FROM report_group');
        $this->system_groups = $this->getData('SELECT * FROM system_group');
    }

    public function up(Schema $schema): void
    {
        // Create new join tables.
        $this->addSql('CREATE TABLE report_user_group (report_id INT NOT NULL, user_group_id INT NOT NULL, INDEX IDX_9C4093A64BD2A4C0 (report_id), INDEX IDX_9C4093A61ED93D47 (user_group_id), PRIMARY KEY(report_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system_user_group (system_id INT NOT NULL, user_group_id INT NOT NULL, INDEX IDX_3AD1AFA4D0952FA5 (system_id), INDEX IDX_3AD1AFA41ED93D47 (user_group_id), PRIMARY KEY(system_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add new foreign keys mapping report and system with the new user_group table
        $this->addSql('ALTER TABLE report_user_group ADD CONSTRAINT FK_9C4093A64BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_user_group ADD CONSTRAINT FK_9C4093A61ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE system_user_group ADD CONSTRAINT FK_3AD1AFA4D0952FA5 FOREIGN KEY (system_id) REFERENCES system (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE system_user_group ADD CONSTRAINT FK_3AD1AFA41ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');

        // Migrate data.
        foreach ($this->report_groups as $mapping) {
            $this->addSql('INSERT INTO `report_user_group` (`report_id`, `user_group_id`) VALUES ('.$mapping['report_id'].', '.$mapping['group_id'].')');
        }
        foreach ($this->system_groups as $mapping) {
            $this->addSql('INSERT INTO `system_user_group` (`system_id`, `user_group_id`) VALUES ('.$mapping['system_id'].', '.$mapping['group_id'].')');
        }

        // Remove old tables.
        $this->addSql('ALTER TABLE system_group DROP FOREIGN KEY FK_390FDF5FFE54D947');
        $this->addSql('ALTER TABLE system_group DROP FOREIGN KEY FK_390FDF5FD0952FA5');
        $this->addSql('ALTER TABLE report_group DROP FOREIGN KEY FK_47DC43BCFE54D947');
        $this->addSql('ALTER TABLE report_group DROP FOREIGN KEY FK_47DC43BC4BD2A4C0');
        $this->addSql('DROP TABLE system_group');
        $this->addSql('DROP TABLE report_group');

        // Update the mapping tabelles 'group_system_themes' and 'group_report_themes'
        $this->addSql('ALTER TABLE user_user_group RENAME INDEX idx_8f02bf9da76ed395 TO IDX_28657971A76ED395');
        $this->addSql('ALTER TABLE user_user_group RENAME INDEX idx_8f02bf9dfe54d947 TO IDX_286579711ED93D47');
        $this->addSql('ALTER TABLE group_system_themes DROP FOREIGN KEY FK_5A25495DFE54D947');
        $this->addSql('DROP INDEX IDX_5A25495DFE54D947 ON group_system_themes');
        $this->addSql('DROP INDEX `primary` ON group_system_themes');
        $this->addSql('ALTER TABLE group_system_themes CHANGE group_id user_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE group_system_themes ADD CONSTRAINT FK_5A25495D1ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5A25495D1ED93D47 ON group_system_themes (user_group_id)');
        $this->addSql('ALTER TABLE group_system_themes ADD PRIMARY KEY (user_group_id, theme_id)');
        $this->addSql('ALTER TABLE group_report_themes DROP FOREIGN KEY FK_63582903FE54D947');
        $this->addSql('DROP INDEX IDX_63582903FE54D947 ON group_report_themes');
        $this->addSql('DROP INDEX `primary` ON group_report_themes');
        $this->addSql('ALTER TABLE group_report_themes CHANGE group_id user_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE group_report_themes ADD CONSTRAINT FK_635829031ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_635829031ED93D47 ON group_report_themes (user_group_id)');
        $this->addSql('ALTER TABLE group_report_themes ADD PRIMARY KEY (user_group_id, theme_id)');
    }

    public function down(Schema $schema): void
    {
        // There is no down for this migration. So left empty.
    }

    /**
     * Helper function to get data from the database.
     *
     * @param string $sql
     *   SQL statement to fetch data
     *
     * @return array<int, mixed>
     *   The data fetched from the database as associative array
     *
     * @throws Exception
     *   Database error
     */
    private function getData(string $sql): array
    {
        $stmt = $this->connection->executeQuery($sql);

        return $stmt->fetchAllAssociative();
    }
}
