<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927163614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE publisher_setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE publisher_description ADD publisher_setting_id INT NOT NULL, DROP setting');
        $this->addSql('ALTER TABLE publisher_description ADD CONSTRAINT FK_9A9B589EF37A58E6 FOREIGN KEY (publisher_setting_id) REFERENCES publisher_setting (id)');
        $this->addSql('CREATE INDEX IDX_9A9B589EF37A58E6 ON publisher_description (publisher_setting_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publisher_description DROP FOREIGN KEY FK_9A9B589EF37A58E6');
        $this->addSql('DROP TABLE publisher_setting');
        $this->addSql('DROP INDEX IDX_9A9B589EF37A58E6 ON publisher_description');
        $this->addSql('ALTER TABLE publisher_description ADD setting VARCHAR(255) NOT NULL, DROP publisher_setting_id');
    }
}
