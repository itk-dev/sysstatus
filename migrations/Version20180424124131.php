<?php declare(strict_types = 1);

namespace DoctrineMigrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180424124131 extends AbstractMigration
{
    public function up(Schema $schema):void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, sys_id VARCHAR(255) DEFAULT NULL, sys_title LONGTEXT DEFAULT NULL, sys_alternative_title LONGTEXT DEFAULT NULL, sys_updated DATETIME DEFAULT NULL, sys_description LONGTEXT DEFAULT NULL, sys_owner LONGTEXT DEFAULT NULL, sys_owner_subdepartment LONGTEXT DEFAULT NULL, sys_emergency_setup LONGTEXT DEFAULT NULL, sys_contractor LONGTEXT DEFAULT NULL, sys_urgency_rating LONGTEXT DEFAULT NULL, sys_number_of_users LONGTEXT DEFAULT NULL, sys_technical_documentation LONGTEXT DEFAULT NULL, sys_external_dependencies LONGTEXT DEFAULT NULL, sys_important_information LONGTEXT DEFAULT NULL, sys_superuser_organization LONGTEXT DEFAULT NULL, sys_server_names LONGTEXT DEFAULT NULL, sys_itsecurity_category LONGTEXT DEFAULT NULL, sys_link_to_security_review LONGTEXT DEFAULT NULL, sys_link_to_contract LONGTEXT DEFAULT NULL, sys_end_of_contract DATETIME DEFAULT NULL, sys_archiving LONGTEXT DEFAULT NULL, sys_open_data LONGTEXT DEFAULT NULL, sys_open_source LONGTEXT DEFAULT NULL, sys_digital_post LONGTEXT DEFAULT NULL, sys_system_category LONGTEXT DEFAULT NULL, sys_digital_transactions_pr_year LONGTEXT DEFAULT NULL, sys_total_transactions_pr_year LONGTEXT DEFAULT NULL, sys_self_service_url LONGTEXT DEFAULT NULL, sys_version LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, sys_id VARCHAR(255) DEFAULT NULL, sys_title LONGTEXT DEFAULT NULL, sys_alternative_title LONGTEXT DEFAULT NULL, sys_updated DATETIME DEFAULT NULL, sys_owner LONGTEXT DEFAULT NULL, sys_confidential_information TINYINT(1) DEFAULT NULL, sys_purpose LONGTEXT DEFAULT NULL, sys_classification LONGTEXT DEFAULT NULL, sys_date_for_revision DATETIME DEFAULT NULL, sys_persons LONGTEXT DEFAULT NULL, sys_information_types LONGTEXT DEFAULT NULL, sys_dato_to_previous_internal_system_dependencies LONGTEXT DEFAULT NULL, sys_dato_from_previous_external_system_dependencies LONGTEXT DEFAULT NULL, sys_data_location LONGTEXT DEFAULT NULL, sys_latest_deletion_date LONGTEXT DEFAULT NULL, sys_data_worth_saving TINYINT(1) DEFAULT NULL, sys_data_worth_saving_via LONGTEXT DEFAULT NULL, sys_data_processors LONGTEXT DEFAULT NULL, sys_data_processing_agreement LONGTEXT DEFAULT NULL, sys_data_processing_agreement_link LONGTEXT DEFAULT NULL, sys_auditor_statement LONGTEXT DEFAULT NULL, sys_auditor_statement_link LONGTEXT DEFAULT NULL, sys_usage LONGTEXT DEFAULT NULL, sys_request_for_insight LONGTEXT DEFAULT NULL, sys_date_use DATETIME DEFAULT NULL, sys_status LONGTEXT DEFAULT NULL, sys_remarks LONGTEXT DEFAULT NULL, sys_video_suveillance LONGTEXT DEFAULT NULL, sys_obligation_to_inform LONGTEXT DEFAULT NULL, sys_legal_basis LONGTEXT DEFAULT NULL, sys_consent LONGTEXT DEFAULT NULL, sys_impact_analysis LONGTEXT DEFAULT NULL, sys_authorization_procedure LONGTEXT DEFAULT NULL, sys_version LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema):void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE system');
        $this->addSql('DROP TABLE report');
    }
}
