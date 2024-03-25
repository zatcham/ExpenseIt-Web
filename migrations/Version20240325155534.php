<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325155534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request ADD budget_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F36ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('CREATE INDEX IDX_3B978F9F36ABA6B8 ON request (budget_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F36ABA6B8');
        $this->addSql('DROP INDEX IDX_3B978F9F36ABA6B8 ON request');
        $this->addSql('ALTER TABLE request DROP budget_id');
    }
}
