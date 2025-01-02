<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180516111410 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE system ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE system ADD CONSTRAINT FK_C94D118BFE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id)');
        $this->addSql('CREATE INDEX IDX_C94D118BFE54D947 ON system (group_id)');
        $this->addSql('ALTER TABLE report ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784FE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id)');
        $this->addSql('CREATE INDEX IDX_C42F7784FE54D947 ON report (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784FE54D947');
        $this->addSql('DROP INDEX IDX_C42F7784FE54D947 ON report');
        $this->addSql('ALTER TABLE report DROP group_id');
        $this->addSql('ALTER TABLE system DROP FOREIGN KEY FK_C94D118BFE54D947');
        $this->addSql('DROP INDEX IDX_C94D118BFE54D947 ON system');
        $this->addSql('ALTER TABLE system DROP group_id');
    }
}
