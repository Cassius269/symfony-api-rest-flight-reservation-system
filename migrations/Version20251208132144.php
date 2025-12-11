<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208132144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stop_reservation (stop_id INT NOT NULL, reservation_id INT NOT NULL, INDEX IDX_912889EE3902063D (stop_id), INDEX IDX_912889EEB83297E7 (reservation_id), PRIMARY KEY(stop_id, reservation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stop_reservation ADD CONSTRAINT FK_912889EE3902063D FOREIGN KEY (stop_id) REFERENCES stop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stop_reservation ADD CONSTRAINT FK_912889EEB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stop_reservation DROP FOREIGN KEY FK_912889EE3902063D');
        $this->addSql('ALTER TABLE stop_reservation DROP FOREIGN KEY FK_912889EEB83297E7');
        $this->addSql('DROP TABLE stop_reservation');
    }
}
