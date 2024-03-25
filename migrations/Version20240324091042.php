<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324091042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE approval_comments (id INT AUTO_INCREMENT NOT NULL, request_id_id INT NOT NULL, user_id_id INT NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_C0B5323022532272 (request_id_id), INDEX IDX_C0B532309D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE approval_comments ADD CONSTRAINT FK_C0B5323022532272 FOREIGN KEY (request_id_id) REFERENCES request (id)');
        $this->addSql('ALTER TABLE approval_comments ADD CONSTRAINT FK_C0B532309D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE approval_comments DROP FOREIGN KEY FK_C0B5323022532272');
        $this->addSql('ALTER TABLE approval_comments DROP FOREIGN KEY FK_C0B532309D86650F');
        $this->addSql('DROP TABLE approval_comments');
    }
}
