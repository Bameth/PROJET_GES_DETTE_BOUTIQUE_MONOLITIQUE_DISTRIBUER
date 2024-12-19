<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218193137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiement (id SERIAL NOT NULL, client_id INT DEFAULT NULL, dept_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, date_paiement TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1DC7A1E19EB6921 ON paiement (client_id)');
        $this->addSql('CREATE INDEX IDX_B1DC7A1E3E23E247 ON paiement (dept_id)');
        $this->addSql('COMMENT ON COLUMN paiement.date_paiement IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E3E23E247 FOREIGN KEY (dept_id) REFERENCES dette (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE paiement DROP CONSTRAINT FK_B1DC7A1E19EB6921');
        $this->addSql('ALTER TABLE paiement DROP CONSTRAINT FK_B1DC7A1E3E23E247');
        $this->addSql('DROP TABLE paiement');
    }
}
