<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215025115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD libelle VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article ADD prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE detail ADD article_id INT NOT NULL');
        $this->addSql('ALTER TABLE detail ADD CONSTRAINT FK_2E067F937294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2E067F937294869C ON detail (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE article DROP libelle');
        $this->addSql('ALTER TABLE article DROP prix');
        $this->addSql('ALTER TABLE detail DROP CONSTRAINT FK_2E067F937294869C');
        $this->addSql('DROP INDEX IDX_2E067F937294869C');
        $this->addSql('ALTER TABLE detail DROP article_id');
    }
}
