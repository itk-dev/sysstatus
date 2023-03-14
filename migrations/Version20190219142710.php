<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190219142710 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE self_service_available_from_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE self_service_available_from_item_system (self_service_available_from_item_id INT NOT NULL, system_id INT NOT NULL, INDEX IDX_4507C4EC3D71E66 (self_service_available_from_item_id), INDEX IDX_4507C4ECD0952FA5 (system_id), PRIMARY KEY(self_service_available_from_item_id, system_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE self_service_available_from_item_system ADD CONSTRAINT FK_4507C4EC3D71E66 FOREIGN KEY (self_service_available_from_item_id) REFERENCES self_service_available_from_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE self_service_available_from_item_system ADD CONSTRAINT FK_4507C4ECD0952FA5 FOREIGN KEY (system_id) REFERENCES system (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE self_service_available_from_item_system DROP FOREIGN KEY FK_4507C4EC3D71E66');
        $this->addSql('DROP TABLE self_service_available_from_item');
        $this->addSql('DROP TABLE self_service_available_from_item_system');
    }
}
