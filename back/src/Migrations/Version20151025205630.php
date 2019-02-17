<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151025205630 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Pontlyvalent (id INT AUTO_INCREMENT NOT NULL, target_id INT NOT NULL, author_id INT NOT NULL, text LONGTEXT NOT NULL, date INT NOT NULL, INDEX IDX_F2847AD6158E0B66 (target_id), INDEX IDX_F2847AD6F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Pontlyvalent ADD CONSTRAINT FK_F2847AD6158E0B66 FOREIGN KEY (target_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE Pontlyvalent ADD CONSTRAINT FK_F2847AD6F675F31B FOREIGN KEY (author_id) REFERENCES fos_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE Pontlyvalent');
    }
}
