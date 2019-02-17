<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151018174154 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE PostFile (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, ext VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, size INT NOT NULL, INDEX IDX_C1F76FF14B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE PostFile ADD CONSTRAINT FK_C1F76FF14B89032C FOREIGN KEY (post_id) REFERENCES Post (id)');
        $this->addSql('ALTER TABLE Post DROP FOREIGN KEY FK_FAB8C3B33DA5256D');
        $this->addSql('DROP INDEX UNIQ_FAB8C3B33DA5256D ON Post');
        $this->addSql('ALTER TABLE Post DROP image_id');
        $this->addSql('ALTER TABLE Newsitem ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Newsitem ADD CONSTRAINT FK_16B131903DA5256D FOREIGN KEY (image_id) REFERENCES Image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16B131903DA5256D ON Newsitem (image_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE PostFile');
        $this->addSql('ALTER TABLE Newsitem DROP FOREIGN KEY FK_16B131903DA5256D');
        $this->addSql('DROP INDEX UNIQ_16B131903DA5256D ON Newsitem');
        $this->addSql('ALTER TABLE Newsitem DROP image_id');
        $this->addSql('ALTER TABLE Post ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B33DA5256D FOREIGN KEY (image_id) REFERENCES Image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FAB8C3B33DA5256D ON Post (image_id)');
    }
}
