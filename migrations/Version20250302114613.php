<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302114613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE airplane_model (id INT AUTO_INCREMENT NOT NULL, model VARCHAR(12) NOT NULL, capacity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE airplane ADD reference VARCHAR(20) NOT NULL, DROP model, DROP created_at, DROP updated_at, CHANGE capacity airplane_model_id INT NOT NULL');
        $this->addSql('ALTER TABLE airplane ADD CONSTRAINT FK_2636002D3E4DE130 FOREIGN KEY (airplane_model_id) REFERENCES airplane_model (id)');
        $this->addSql('CREATE INDEX IDX_2636002D3E4DE130 ON airplane (airplane_model_id)');
        $this->addSql('ALTER TABLE city CHANGE zip_code zip_code VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE flight CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation CHANGE number_flight_seat number_flight_seat VARCHAR(4) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE birth_date birth_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE airplane DROP FOREIGN KEY FK_2636002D3E4DE130');
        $this->addSql('DROP TABLE airplane_model');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('ALTER TABLE reservation CHANGE number_flight_seat number_flight_seat INT NOT NULL');
        $this->addSql('DROP INDEX IDX_2636002D3E4DE130 ON airplane');
        $this->addSql('ALTER TABLE airplane ADD model VARCHAR(12) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL, DROP reference, CHANGE airplane_model_id capacity INT NOT NULL');
        $this->addSql('ALTER TABLE flight CHANGE created_at created_at DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE birth_date birth_date DATE NOT NULL');
        $this->addSql('ALTER TABLE city CHANGE zip_code zip_code VARCHAR(10) NOT NULL');
    }
}
