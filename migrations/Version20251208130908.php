<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208130908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE airport (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(40) NOT NULL, iata_code VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_7E91F7C28BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(35) NOT NULL, iata_code VARCHAR(2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_agent (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, agent_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_617A3EF8979B1AD6 (company_id), INDEX IDX_617A3EF83414710B (agent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_manager (id INT AUTO_INCREMENT NOT NULL, manager_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, INDEX IDX_DA763F6E783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flight_operation_officer (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manager (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stop (id INT AUTO_INCREMENT NOT NULL, flight_id INT NOT NULL, airport_arrival_id INT NOT NULL, date_departure DATETIME NOT NULL, date_arrival DATETIME NOT NULL, order_flight INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_B95616B691F478C5 (flight_id), INDEX IDX_B95616B66E52B0FE (airport_arrival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE airport ADD CONSTRAINT FK_7E91F7C28BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE company_agent ADD CONSTRAINT FK_617A3EF8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company_agent ADD CONSTRAINT FK_617A3EF83414710B FOREIGN KEY (agent_id) REFERENCES agent (id)');
        $this->addSql('ALTER TABLE company_manager ADD CONSTRAINT FK_DA763F6E783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id)');
        $this->addSql('ALTER TABLE flight_operation_officer ADD CONSTRAINT FK_833DCAD6BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B9BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stop ADD CONSTRAINT FK_B95616B691F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id)');
        $this->addSql('ALTER TABLE stop ADD CONSTRAINT FK_B95616B66E52B0FE FOREIGN KEY (airport_arrival_id) REFERENCES airport (id)');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60EE7984489');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60EF0127FF');
        $this->addSql('DROP INDEX IDX_C257E60EE7984489 ON flight');
        $this->addSql('DROP INDEX IDX_C257E60EF0127FF ON flight');
        $this->addSql('ALTER TABLE flight ADD company_id INT NOT NULL, ADD airport_departure_id INT NOT NULL, ADD airport_arrival_id INT NOT NULL, ADD is_direct TINYINT(1) NOT NULL, ADD is_canceled TINYINT(1) NOT NULL, ADD is_late TINYINT(1) NOT NULL, DROP city_departure_id, DROP city_arrival_id');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E90E8127C FOREIGN KEY (airport_departure_id) REFERENCES airport (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E6E52B0FE FOREIGN KEY (airport_arrival_id) REFERENCES airport (id)');
        $this->addSql('CREATE INDEX IDX_C257E60E979B1AD6 ON flight (company_id)');
        $this->addSql('CREATE INDEX IDX_C257E60E90E8127C ON flight (airport_departure_id)');
        $this->addSql('CREATE INDEX IDX_C257E60E6E52B0FE ON flight (airport_arrival_id)');
        $this->addSql('ALTER TABLE reservation ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_42C849556BF700BD ON reservation (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E90E8127C');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E6E52B0FE');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E979B1AD6');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556BF700BD');
        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DBF396750');
        $this->addSql('ALTER TABLE airport DROP FOREIGN KEY FK_7E91F7C28BAC62AF');
        $this->addSql('ALTER TABLE company_agent DROP FOREIGN KEY FK_617A3EF8979B1AD6');
        $this->addSql('ALTER TABLE company_agent DROP FOREIGN KEY FK_617A3EF83414710B');
        $this->addSql('ALTER TABLE company_manager DROP FOREIGN KEY FK_DA763F6E783E3463');
        $this->addSql('ALTER TABLE flight_operation_officer DROP FOREIGN KEY FK_833DCAD6BF396750');
        $this->addSql('ALTER TABLE manager DROP FOREIGN KEY FK_FA2425B9BF396750');
        $this->addSql('ALTER TABLE stop DROP FOREIGN KEY FK_B95616B691F478C5');
        $this->addSql('ALTER TABLE stop DROP FOREIGN KEY FK_B95616B66E52B0FE');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE airport');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_agent');
        $this->addSql('DROP TABLE company_manager');
        $this->addSql('DROP TABLE flight_operation_officer');
        $this->addSql('DROP TABLE manager');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE stop');
        $this->addSql('DROP INDEX IDX_42C849556BF700BD ON reservation');
        $this->addSql('ALTER TABLE reservation DROP status_id');
        $this->addSql('DROP INDEX IDX_C257E60E979B1AD6 ON flight');
        $this->addSql('DROP INDEX IDX_C257E60E90E8127C ON flight');
        $this->addSql('DROP INDEX IDX_C257E60E6E52B0FE ON flight');
        $this->addSql('ALTER TABLE flight ADD city_departure_id INT NOT NULL, ADD city_arrival_id INT NOT NULL, DROP company_id, DROP airport_departure_id, DROP airport_arrival_id, DROP is_direct, DROP is_canceled, DROP is_late');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60EE7984489 FOREIGN KEY (city_departure_id) REFERENCES city (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60EF0127FF FOREIGN KEY (city_arrival_id) REFERENCES city (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C257E60EE7984489 ON flight (city_departure_id)');
        $this->addSql('CREATE INDEX IDX_C257E60EF0127FF ON flight (city_arrival_id)');
    }
}
