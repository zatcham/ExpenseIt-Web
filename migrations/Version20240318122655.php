<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318122655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE xero_integration DROP FOREIGN KEY FK_CB74FDE238B53C32');
        $this->addSql('DROP INDEX UNIQ_CB74FDE238B53C32 ON xero_integration');
        $this->addSql('ALTER TABLE xero_integration DROP client_id, DROP client_secret, CHANGE company_id_id company_id INT NOT NULL');
        $this->addSql('ALTER TABLE xero_integration ADD CONSTRAINT FK_CB74FDE2979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CB74FDE2979B1AD6 ON xero_integration (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE xero_integration DROP FOREIGN KEY FK_CB74FDE2979B1AD6');
        $this->addSql('DROP INDEX UNIQ_CB74FDE2979B1AD6 ON xero_integration');
        $this->addSql('ALTER TABLE xero_integration ADD client_id VARCHAR(255) NOT NULL, ADD client_secret VARCHAR(255) NOT NULL, CHANGE company_id company_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE xero_integration ADD CONSTRAINT FK_CB74FDE238B53C32 FOREIGN KEY (company_id_id) REFERENCES companies (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CB74FDE238B53C32 ON xero_integration (company_id_id)');
    }
}
