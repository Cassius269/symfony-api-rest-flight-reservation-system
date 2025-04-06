<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406090125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE copilot (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flight_copilot (flight_id INT NOT NULL, copilot_id INT NOT NULL, INDEX IDX_16407F5091F478C5 (flight_id), INDEX IDX_16407F50F1F70582 (copilot_id), PRIMARY KEY(flight_id, copilot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE copilot ADD CONSTRAINT FK_95AC9CC6BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flight_copilot ADD CONSTRAINT FK_16407F5091F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flight_copilot ADD CONSTRAINT FK_16407F50F1F70582 FOREIGN KEY (copilot_id) REFERENCES copilot (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE copilot DROP FOREIGN KEY FK_95AC9CC6BF396750');
        $this->addSql('ALTER TABLE flight_copilot DROP FOREIGN KEY FK_16407F5091F478C5');
        $this->addSql('ALTER TABLE flight_copilot DROP FOREIGN KEY FK_16407F50F1F70582');
        $this->addSql('DROP TABLE copilot');
        $this->addSql('DROP TABLE flight_copilot');
    }
}
