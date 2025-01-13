<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180530113106 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE theme_category DROP FOREIGN KEY FK_A4720BB659027487');
        $this->addSql('ALTER TABLE theme_category DROP FOREIGN KEY FK_A4720BB612469DE2');
        $this->addSql('ALTER TABLE theme_category DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE theme_category ADD id INT AUTO_INCREMENT NOT NULL, ADD sort_order INT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE theme_category ADD CONSTRAINT FK_A4720BB659027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE theme_category ADD CONSTRAINT FK_A4720BB612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE theme_category MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE theme_category DROP FOREIGN KEY FK_A4720BB659027487');
        $this->addSql('ALTER TABLE theme_category DROP FOREIGN KEY FK_A4720BB612469DE2');
        $this->addSql('ALTER TABLE theme_category DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE theme_category DROP id, DROP sort_order');
    }
}
