<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809155020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consulting (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, owner_id INT NOT NULL, target VARCHAR(255) NOT NULL, code INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_CEBF92C7C33F7837 (document_id), INDEX IDX_CEBF92C77E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consulting ADD CONSTRAINT FK_CEBF92C7C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE consulting ADD CONSTRAINT FK_CEBF92C77E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consulting DROP FOREIGN KEY FK_CEBF92C7C33F7837');
        $this->addSql('ALTER TABLE consulting DROP FOREIGN KEY FK_CEBF92C77E3C61F9');
        $this->addSql('DROP TABLE consulting');
    }
}
