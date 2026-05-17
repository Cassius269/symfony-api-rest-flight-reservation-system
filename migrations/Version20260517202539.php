<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517202539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_captain (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, captain_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_8E67A58C979B1AD6 (company_id), INDEX IDX_8E67A58C3346729B (captain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_captain ADD CONSTRAINT FK_8E67A58C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company_captain ADD CONSTRAINT FK_8E67A58C3346729B FOREIGN KEY (captain_id) REFERENCES captain (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_captain DROP FOREIGN KEY FK_8E67A58C979B1AD6');
        $this->addSql('ALTER TABLE company_captain DROP FOREIGN KEY FK_8E67A58C3346729B');
        $this->addSql('DROP TABLE company_captain');
    }
}
