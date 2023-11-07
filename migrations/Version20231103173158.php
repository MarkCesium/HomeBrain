<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103173158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location ADD user_api_id INT NOT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB31C42205 FOREIGN KEY (user_api_id) REFERENCES user_api (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB31C42205 ON location (user_api_id)');
        $this->addSql('ALTER TABLE notice CHANGE action action SMALLINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB31C42205');
        $this->addSql('DROP INDEX IDX_5E9E89CB31C42205 ON location');
        $this->addSql('ALTER TABLE location DROP user_api_id');
        $this->addSql('ALTER TABLE notice CHANGE action action SMALLINT NOT NULL COMMENT \'0 - test,
        1 - warning,
        2 - something added,
        3 - something removed.\'');
    }
}
