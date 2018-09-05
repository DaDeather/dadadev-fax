<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180828074421 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE faxes (id INT AUTO_INCREMENT NOT NULL, fax_id VARCHAR(50) NOT NULL, fax_direction VARCHAR(25) NOT NULL, duration INT DEFAULT NULL, from_phone_number VARCHAR(255) DEFAULT NULL, to_phone_number VARCHAR(255) DEFAULT NULL, pages_count INT DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, price_unit VARCHAR(3) DEFAULT NULL, remote_station_id VARCHAR(255) DEFAULT NULL, fax_status VARCHAR(255) DEFAULT NULL, media_id VARCHAR(50) DEFAULT NULL, media_url TEXT DEFAULT NULL, local_file_path VARCHAR(255) DEFAULT NULL, local_file_mime VARCHAR(255) DEFAULT NULL, local_file_size_in_bytes INT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, deleted DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D8AE631AFCB68748 (fax_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (code VARCHAR(191) NOT NULL, name VARCHAR(512) NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, deleted DATETIME DEFAULT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(191) NOT NULL, password VARCHAR(512) NOT NULL, forename VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, last_login DATETIME DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, deleted DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles_to_users (user_id INT NOT NULL, user_role_code VARCHAR(191) NOT NULL, INDEX IDX_5E6F3E62A76ED395 (user_id), INDEX IDX_5E6F3E62C94ACFE8 (user_role_code), PRIMARY KEY(user_id, user_role_code)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_roles_to_users ADD CONSTRAINT FK_5E6F3E62A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_roles_to_users ADD CONSTRAINT FK_5E6F3E62C94ACFE8 FOREIGN KEY (user_role_code) REFERENCES user_roles (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_roles_to_users DROP FOREIGN KEY FK_5E6F3E62C94ACFE8');
        $this->addSql('ALTER TABLE user_roles_to_users DROP FOREIGN KEY FK_5E6F3E62A76ED395');
        $this->addSql('DROP TABLE faxes');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_roles_to_users');
    }
}
