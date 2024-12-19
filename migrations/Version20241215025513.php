<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215025513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detail ADD dept_id INT NOT NULL');
        $this->addSql('ALTER TABLE detail ADD qte INT NOT NULL');
        $this->addSql('ALTER TABLE detail ADD CONSTRAINT FK_2E067F933E23E247 FOREIGN KEY (dept_id) REFERENCES dette (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2E067F933E23E247 ON detail (dept_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE detail DROP CONSTRAINT FK_2E067F933E23E247');
        $this->addSql('DROP INDEX IDX_2E067F933E23E247');
        $this->addSql('ALTER TABLE detail DROP dept_id');
        $this->addSql('ALTER TABLE detail DROP qte');
    }
}
