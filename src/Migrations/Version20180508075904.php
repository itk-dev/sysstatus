<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180508075904 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer ADD system_id INT DEFAULT NULL, ADD report_id INT DEFAULT NULL, CHANGE smiley smiley ENUM(\'GREEN\', \'YELLOW\', \'RED\', \'BLUE\') DEFAULT NULL COMMENT \'(DC2Type:SmileyType)\'');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25D0952FA5 FOREIGN KEY (system_id) REFERENCES system (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A254BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id)');
        $this->addSql('CREATE INDEX IDX_DADD4A25D0952FA5 ON answer (system_id)');
        $this->addSql('CREATE INDEX IDX_DADD4A254BD2A4C0 ON answer (report_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25D0952FA5');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A254BD2A4C0');
        $this->addSql('DROP INDEX IDX_DADD4A25D0952FA5 ON answer');
        $this->addSql('DROP INDEX IDX_DADD4A254BD2A4C0 ON answer');
        $this->addSql('ALTER TABLE answer DROP system_id, DROP report_id, CHANGE smiley smiley VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
