<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209135653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE constructor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE airplane ADD company_id INT NOT NULL');
        $this->addSql('ALTER TABLE airplane ADD CONSTRAINT FK_2636002D979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_2636002D979B1AD6 ON airplane (company_id)');
        $this->addSql('ALTER TABLE airplane_model ADD constructor_id INT NOT NULL');
        $this->addSql('ALTER TABLE airplane_model ADD CONSTRAINT FK_F1F15822D98BF9 FOREIGN KEY (constructor_id) REFERENCES constructor (id)');
        $this->addSql('CREATE INDEX IDX_F1F15822D98BF9 ON airplane_model (constructor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE airplane_model DROP FOREIGN KEY FK_F1F15822D98BF9');
        $this->addSql('DROP TABLE constructor');
        $this->addSql('ALTER TABLE airplane DROP FOREIGN KEY FK_2636002D979B1AD6');
        $this->addSql('DROP INDEX IDX_2636002D979B1AD6 ON airplane');
        $this->addSql('ALTER TABLE airplane DROP company_id');
        $this->addSql('DROP INDEX IDX_F1F15822D98BF9 ON airplane_model');
        $this->addSql('ALTER TABLE airplane_model DROP constructor_id');
    }
}
