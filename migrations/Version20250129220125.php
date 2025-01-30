<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129220125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight ADD airplane_id INT NOT NULL');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E996E853C FOREIGN KEY (airplane_id) REFERENCES airplane (id)');
        $this->addSql('CREATE INDEX IDX_C257E60E996E853C ON flight (airplane_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight DROP FOREIGN KEY FK_C257E60E996E853C');
        $this->addSql('DROP INDEX IDX_C257E60E996E853C ON flight');
        $this->addSql('ALTER TABLE flight DROP airplane_id');
    }
}
