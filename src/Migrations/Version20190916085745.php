<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190916085745 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

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
