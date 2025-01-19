<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250119231044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city ADD country_id INT NOT NULL');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_2D5B0234F92F3E70 ON city (country_id)');
        $this->addSql('ALTER TABLE flight ADD city_departure_id INT NOT NULL, ADD city_arrival_id INT NOT NULL');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60EE7984489 FOREIGN KEY (city_departure_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60EF0127FF FOREIGN KEY (city_arrival_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_C257E60EE7984489 ON flight (city_departure_id)');
        $this->addSql('CREATE INDEX IDX_C257E60EF0127FF ON flight (city_arrival_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234F92F3E70');
        $this->addSql('DROP INDEX IDX_2D5B0234F92F3E70 ON city');
        $this->addSql('ALTER TABLE city DROP country_id');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60EE7984489');
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60EF0127FF');
        $this->addSql('DROP INDEX IDX_C257E60EE7984489 ON flight');
        $this->addSql('DROP INDEX IDX_C257E60EF0127FF ON flight');
        $this->addSql('ALTER TABLE flight DROP city_departure_id, DROP city_arrival_id');
    }
}
