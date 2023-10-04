<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230920165509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE icon_image (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, img VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publisher_description (id INT AUTO_INCREMENT NOT NULL, publisher_id INT NOT NULL, setting VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_9A9B589E40C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE publisher_description ADD CONSTRAINT FK_9A9B589E40C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('ALTER TABLE location ADD icon_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB20B00A52 FOREIGN KEY (icon_image_id) REFERENCES icon_image (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB20B00A52 ON location (icon_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB20B00A52');
        $this->addSql('ALTER TABLE publisher_description DROP FOREIGN KEY FK_9A9B589E40C86FCE');
        $this->addSql('DROP TABLE icon_image');
        $this->addSql('DROP TABLE publisher_description');
        $this->addSql('DROP INDEX IDX_5E9E89CB20B00A52 ON location');
        $this->addSql('ALTER TABLE location DROP icon_image_id');
    }
}
