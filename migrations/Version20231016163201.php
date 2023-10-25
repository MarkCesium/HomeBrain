<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016163201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE publisher_value_archieve (id INT AUTO_INCREMENT NOT NULL, publisher_id INT NOT NULL, value VARCHAR(255) NOT NULL, updated DATETIME NOT NULL, INDEX IDX_AD8C9D5540C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE publisher_value_archieve ADD CONSTRAINT FK_AD8C9D5540C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publisher_value_archieve DROP FOREIGN KEY FK_AD8C9D5540C86FCE');
        $this->addSql('DROP TABLE publisher_value_archieve');
    }
}
