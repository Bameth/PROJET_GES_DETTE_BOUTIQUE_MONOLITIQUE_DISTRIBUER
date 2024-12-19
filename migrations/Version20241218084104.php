<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218084104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier (id SERIAL NOT NULL, article_id INT NOT NULL, qte INT NOT NULL, total DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_24CC0DF27294869C ON panier (article_id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF27294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE panier DROP CONSTRAINT FK_24CC0DF27294869C');
        $this->addSql('DROP TABLE panier');
    }
}
