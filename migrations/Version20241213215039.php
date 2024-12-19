<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213215039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id SERIAL NOT NULL, user_id INT DEFAULT NULL, surname VARCHAR(50) NOT NULL, telephone VARCHAR(50) NOT NULL, adresse VARCHAR(50) NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('COMMENT ON COLUMN client.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN client.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE detail (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE dette (id SERIAL NOT NULL, client_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, montant_verser DOUBLE PRECISION NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_831BC80819EB6921 ON dette (client_id)');
        $this->addSql('COMMENT ON COLUMN dette.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN dette.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, brochure_filename VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, is_blocked BOOLEAN NOT NULL, create_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON users (login)');
        $this->addSql('COMMENT ON COLUMN users.create_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dette ADD CONSTRAINT FK_831BC80819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE dette DROP CONSTRAINT FK_831BC80819EB6921');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE detail');
        $this->addSql('DROP TABLE dette');
        $this->addSql('DROP TABLE users');
    }
}
