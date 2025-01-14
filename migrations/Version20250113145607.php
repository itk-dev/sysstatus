<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250113145607 extends AbstractMigration
{
    /**
     * @var array<int, mixed>
     */
    private array $groups = [];

    /**
     * @var array<int, mixed>
     */
    private array $users = [];

    /**
     * @var array<int, mixed>
     */
    private array $mapping = [];

    public function getDescription(): string
    {
        return 'Migrate groups and users to new structure';
    }

    public function preUp(Schema $schema): void
    {
        $this->groups = $this->getData('SELECT * FROM fos_group');
        foreach ($this->groups as &$group) {
            $group['roles'] = unserialize($group['roles']);
        }

        $this->users = $this->getData('SELECT * FROM fos_user');
        foreach ($this->users as &$user) {
            $user['roles'] = unserialize($user['roles']);
        }

        $this->mapping = $this->getData('SELECT * FROM fos_user_user_group');
    }

    public function up(Schema $schema): void
    {
        // Insert groups.
        foreach ($this->groups as $group) {
            $this->addSql('INSERT INTO `user_group` (`id`, `name`, `roles`) VALUES ('.$group['id'].", '".$group['name']."', '".json_encode($group['roles'])."')");
        }

        // Insert users.
        foreach ($this->users as $user) {
            $data = [
                $user['id'],
                "'".$user['username']."'",
                "'".$user['password']."'",
                "'".$user['email']."'",
                $user['enabled'],
                !empty($user['last_login']) ? "'".$user['last_login']."'" : 'NULL',
                "'".json_encode($user['roles'])."'",
                "'".$user['created_by']."'",
                "'".$user['updated_by']."'",
                !empty($user['created_at']) ? "'".$user['created_at']."'" : 'NULL',
                !empty($user['updated_at']) ? "'".$user['updated_at']."'" : 'NULL',
            ];
            $this->addSql('INSERT INTO `user` (`id`, `username`, `password`, `email`, `enabled`, `last_login`, `roles`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES ('.implode(',', $data).')');
        }

        // Link users and groups.
        foreach ($this->mapping as $mapping) {
            $this->addSql('INSERT INTO `user_user_group` (`user_id`, `user_group_id`) VALUES ('.$mapping['user_id'].', '.$mapping['group_id'].')');
        }
    }

    public function down(Schema $schema): void
    {
        // There is now down for this migration. So left empty.
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
