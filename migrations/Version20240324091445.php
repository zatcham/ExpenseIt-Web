<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324091445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE approval_comments DROP FOREIGN KEY FK_C0B532309D86650F');
        $this->addSql('ALTER TABLE approval_comments DROP FOREIGN KEY FK_C0B5323022532272');
        $this->addSql('DROP INDEX IDX_C0B5323022532272 ON approval_comments');
        $this->addSql('DROP INDEX IDX_C0B532309D86650F ON approval_comments');
        $this->addSql('ALTER TABLE approval_comments ADD request_id INT NOT NULL, ADD user_id INT NOT NULL, DROP request_id_id, DROP user_id_id');
        $this->addSql('ALTER TABLE approval_comments ADD CONSTRAINT FK_C0B53230427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id)');
        $this->addSql('ALTER TABLE approval_comments ADD CONSTRAINT FK_C0B53230A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C0B53230427EB8A5 ON approval_comments (request_id)');
        $this->addSql('CREATE INDEX IDX_C0B53230A76ED395 ON approval_comments (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE approval_comments DROP FOREIGN KEY FK_C0B53230427EB8A5');
        $this->addSql('ALTER TABLE approval_comments DROP FOREIGN KEY FK_C0B53230A76ED395');
        $this->addSql('DROP INDEX IDX_C0B53230427EB8A5 ON approval_comments');
        $this->addSql('DROP INDEX IDX_C0B53230A76ED395 ON approval_comments');
        $this->addSql('ALTER TABLE approval_comments ADD request_id_id INT NOT NULL, ADD user_id_id INT NOT NULL, DROP request_id, DROP user_id');
        $this->addSql('ALTER TABLE approval_comments ADD CONSTRAINT FK_C0B532309D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE approval_comments ADD CONSTRAINT FK_C0B5323022532272 FOREIGN KEY (request_id_id) REFERENCES request (id)');
        $this->addSql('CREATE INDEX IDX_C0B5323022532272 ON approval_comments (request_id_id)');
        $this->addSql('CREATE INDEX IDX_C0B532309D86650F ON approval_comments (user_id_id)');
    }
}
