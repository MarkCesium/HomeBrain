<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918172032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_location (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, location_id INT DEFAULT NULL, INDEX IDX_BE136DCBA76ED395 (user_id), INDEX IDX_BE136DCB64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_location ADD CONSTRAINT FK_BE136DCBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_location ADD CONSTRAINT FK_BE136DCB64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE location_user DROP FOREIGN KEY FK_D97630964D218E');
        $this->addSql('ALTER TABLE location_user DROP FOREIGN KEY FK_D976309A76ED395');
        $this->addSql('DROP TABLE location_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location_user (location_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D97630964D218E (location_id), INDEX IDX_D976309A76ED395 (user_id), PRIMARY KEY(location_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE location_user ADD CONSTRAINT FK_D97630964D218E FOREIGN KEY (location_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE location_user ADD CONSTRAINT FK_D976309A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCBA76ED395');
        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCB64D218E');
        $this->addSql('DROP TABLE user_location');
    }
}
