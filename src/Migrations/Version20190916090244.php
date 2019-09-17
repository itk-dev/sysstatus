<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190916090244 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE report_group (report_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_47DC43BC4BD2A4C0 (report_id), INDEX IDX_47DC43BCFE54D947 (group_id), PRIMARY KEY(report_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system_group (system_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_390FDF5FD0952FA5 (system_id), INDEX IDX_390FDF5FFE54D947 (group_id), PRIMARY KEY(system_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report_group ADD CONSTRAINT FK_47DC43BC4BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_group ADD CONSTRAINT FK_47DC43BCFE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE system_group ADD CONSTRAINT FK_390FDF5FD0952FA5 FOREIGN KEY (system_id) REFERENCES system (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE system_group ADD CONSTRAINT FK_390FDF5FFE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE report_group');
        $this->addSql('DROP TABLE system_group');
    }
}
