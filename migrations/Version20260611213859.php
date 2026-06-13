<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260611213859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agent (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE airplane (id INT AUTO_INCREMENT NOT NULL, airplane_model_id INT NOT NULL, company_id INT NOT NULL, reference VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_2636002D3E4DE130 (airplane_model_id), INDEX IDX_2636002D979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE airplane_model (id INT AUTO_INCREMENT NOT NULL, constructor_id INT NOT NULL, model VARCHAR(12) NOT NULL, capacity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_F1F15822D98BF9 (constructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE airport (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(40) NOT NULL, iata_code VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_7E91F7C28BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE captain (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, name VARCHAR(50) NOT NULL, zip_code VARCHAR(10) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_2D5B0234F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(35) NOT NULL, iata_code VARCHAR(2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_agent (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, agent_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_617A3EF8979B1AD6 (company_id), INDEX IDX_617A3EF83414710B (agent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_captain (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, captain_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_8E67A58C979B1AD6 (company_id), INDEX IDX_8E67A58C3346729B (captain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_manager (id INT AUTO_INCREMENT NOT NULL, manager_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, INDEX IDX_DA763F6E783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE constructor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE continent (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE copilot (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, continent_id INT NOT NULL, name VARCHAR(50) NOT NULL, iso_code VARCHAR(3) DEFAULT NULL, timezone VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_5373C966921F4C77 (continent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flight (id INT AUTO_INCREMENT NOT NULL, airplane_id INT NOT NULL, captain_id INT NOT NULL, company_id INT NOT NULL, airport_departure_id INT NOT NULL, airport_arrival_id INT NOT NULL, status_id INT NOT NULL, date_departure DATETIME NOT NULL, date_arrival DATETIME NOT NULL, price NUMERIC(6, 2) NOT NULL, is_direct TINYINT(1) NOT NULL, is_canceled TINYINT(1) NOT NULL, is_late TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_C257E60E996E853C (airplane_id), INDEX IDX_C257E60E3346729B (captain_id), INDEX IDX_C257E60E979B1AD6 (company_id), INDEX IDX_C257E60E90E8127C (airport_departure_id), INDEX IDX_C257E60E6E52B0FE (airport_arrival_id), INDEX IDX_C257E60E6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flight_copilot (flight_id INT NOT NULL, copilot_id INT NOT NULL, INDEX IDX_16407F5091F478C5 (flight_id), INDEX IDX_16407F50F1F70582 (copilot_id), PRIMARY KEY(flight_id, copilot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flight_operation_officer (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manager (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE passenger (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE regenerate (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, passenger_id INT NOT NULL, flight_id INT NOT NULL, status_id INT NOT NULL, number_flight_seat VARCHAR(4) NOT NULL, price NUMERIC(6, 2) NOT NULL, passenger_name_record VARCHAR(6) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_42C849554502E565 (passenger_id), INDEX IDX_42C8495591F478C5 (flight_id), INDEX IDX_42C849556BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stop (id INT AUTO_INCREMENT NOT NULL, flight_id INT NOT NULL, airport_arrival_id INT NOT NULL, date_departure DATETIME NOT NULL, date_arrival DATETIME NOT NULL, order_flight INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_B95616B691F478C5 (flight_id), INDEX IDX_B95616B66E52B0FE (airport_arrival_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stop_reservation (stop_id INT NOT NULL, reservation_id INT NOT NULL, INDEX IDX_912889EE3902063D (stop_id), INDEX IDX_912889EEB83297E7 (reservation_id), PRIMARY KEY(stop_id, reservation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, birth_date DATE DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE airplane ADD CONSTRAINT FK_2636002D3E4DE130 FOREIGN KEY (airplane_model_id) REFERENCES airplane_model (id)');
        $this->addSql('ALTER TABLE airplane ADD CONSTRAINT FK_2636002D979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE airplane_model ADD CONSTRAINT FK_F1F15822D98BF9 FOREIGN KEY (constructor_id) REFERENCES constructor (id)');
        $this->addSql('ALTER TABLE airport ADD CONSTRAINT FK_7E91F7C28BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE captain ADD CONSTRAINT FK_AE35BF5BBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE company_agent ADD CONSTRAINT FK_617A3EF8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company_agent ADD CONSTRAINT FK_617A3EF83414710B FOREIGN KEY (agent_id) REFERENCES agent (id)');
        $this->addSql('ALTER TABLE company_captain ADD CONSTRAINT FK_8E67A58C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company_captain ADD CONSTRAINT FK_8E67A58C3346729B FOREIGN KEY (captain_id) REFERENCES captain (id)');
        $this->addSql('ALTER TABLE company_manager ADD CONSTRAINT FK_DA763F6E783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id)');
        $this->addSql('ALTER TABLE copilot ADD CONSTRAINT FK_95AC9CC6BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966921F4C77 FOREIGN KEY (continent_id) REFERENCES continent (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E996E853C FOREIGN KEY (airplane_id) REFERENCES airplane (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E3346729B FOREIGN KEY (captain_id) REFERENCES captain (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E90E8127C FOREIGN KEY (airport_departure_id) REFERENCES airport (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E6E52B0FE FOREIGN KEY (airport_arrival_id) REFERENCES airport (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE flight_copilot ADD CONSTRAINT FK_16407F5091F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flight_copilot ADD CONSTRAINT FK_16407F50F1F70582 FOREIGN KEY (copilot_id) REFERENCES copilot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flight_operation_officer ADD CONSTRAINT FK_833DCAD6BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B9BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE passenger ADD CONSTRAINT FK_3BEFE8DDBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554502E565 FOREIGN KEY (passenger_id) REFERENCES passenger (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495591F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE stop ADD CONSTRAINT FK_B95616B691F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id)');
        $this->addSql('ALTER TABLE stop ADD CONSTRAINT FK_B95616B66E52B0FE FOREIGN KEY (airport_arrival_id) REFERENCES airport (id)');
        $this->addSql('ALTER TABLE stop_reservation ADD CONSTRAINT FK_912889EE3902063D FOREIGN KEY (stop_id) REFERENCES stop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stop_reservation ADD CONSTRAINT FK_912889EEB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DBF396750');
        $this->addSql('ALTER TABLE airplane DROP FOREIGN KEY FK_2636002D3E4DE130');
        $this->addSql('ALTER TABLE airplane DROP FOREIGN KEY FK_2636002D979B1AD6');
        $this->addSql('ALTER TABLE airplane_model DROP FOREIGN KEY FK_F1F15822D98BF9');
        $this->addSql('ALTER TABLE airport DROP FOREIGN KEY FK_7E91F7C28BAC62AF');
        $this->addSql('ALTER TABLE captain DROP FOREIGN KEY FK_AE35BF5BBF396750');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234F92F3E70');
        $this->addSql('ALTER TABLE company_agent DROP FOREIGN KEY FK_617A3EF8979B1AD6');
        $this->addSql('ALTER TABLE company_agent DROP FOREIGN KEY FK_617A3EF83414710B');
        $this->addSql('ALTER TABLE company_captain DROP FOREIGN KEY FK_8E67A58C979B1AD6');
        $this->addSql('ALTER TABLE company_captain DROP FOREIGN KEY FK_8E67A58C3346729B');
        $this->addSql('ALTER TABLE company_manager DROP FOREIGN KEY FK_DA763F6E783E3463');
        $this->addSql('ALTER TABLE copilot DROP FOREIGN KEY FK_95AC9CC6BF396750');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966921F4C77');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E996E853C');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E3346729B');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E979B1AD6');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E90E8127C');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E6E52B0FE');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E6BF700BD');
        $this->addSql('ALTER TABLE flight_copilot DROP FOREIGN KEY FK_16407F5091F478C5');
        $this->addSql('ALTER TABLE flight_copilot DROP FOREIGN KEY FK_16407F50F1F70582');
        $this->addSql('ALTER TABLE flight_operation_officer DROP FOREIGN KEY FK_833DCAD6BF396750');
        $this->addSql('ALTER TABLE manager DROP FOREIGN KEY FK_FA2425B9BF396750');
        $this->addSql('ALTER TABLE passenger DROP FOREIGN KEY FK_3BEFE8DDBF396750');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554502E565');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495591F478C5');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556BF700BD');
        $this->addSql('ALTER TABLE stop DROP FOREIGN KEY FK_B95616B691F478C5');
        $this->addSql('ALTER TABLE stop DROP FOREIGN KEY FK_B95616B66E52B0FE');
        $this->addSql('ALTER TABLE stop_reservation DROP FOREIGN KEY FK_912889EE3902063D');
        $this->addSql('ALTER TABLE stop_reservation DROP FOREIGN KEY FK_912889EEB83297E7');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE airplane');
        $this->addSql('DROP TABLE airplane_model');
        $this->addSql('DROP TABLE airport');
        $this->addSql('DROP TABLE captain');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_agent');
        $this->addSql('DROP TABLE company_captain');
        $this->addSql('DROP TABLE company_manager');
        $this->addSql('DROP TABLE constructor');
        $this->addSql('DROP TABLE continent');
        $this->addSql('DROP TABLE copilot');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE flight');
        $this->addSql('DROP TABLE flight_copilot');
        $this->addSql('DROP TABLE flight_operation_officer');
        $this->addSql('DROP TABLE manager');
        $this->addSql('DROP TABLE passenger');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE regenerate');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE stop');
        $this->addSql('DROP TABLE stop_reservation');
        $this->addSql('DROP TABLE user');
    }
}
