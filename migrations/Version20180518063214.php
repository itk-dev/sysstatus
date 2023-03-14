<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180518063214 extends AbstractMigration
{
    public function up(Schema $schema):void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE system ADD responsible_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE system ADD CONSTRAINT FK_C94D118B602AD315 FOREIGN KEY (responsible_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_C94D118B602AD315 ON system (responsible_id)');
        $this->addSql('ALTER TABLE report ADD responsible_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784602AD315 FOREIGN KEY (responsible_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_C42F7784602AD315 ON report (responsible_id)');
    }

    public function down(Schema $schema):void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784602AD315');
        $this->addSql('DROP INDEX IDX_C42F7784602AD315 ON report');
        $this->addSql('ALTER TABLE report DROP responsible_id');
        $this->addSql('ALTER TABLE system DROP FOREIGN KEY FK_C94D118B602AD315');
        $this->addSql('DROP INDEX IDX_C94D118B602AD315 ON system');
        $this->addSql('ALTER TABLE system DROP responsible_id');
    }
}
