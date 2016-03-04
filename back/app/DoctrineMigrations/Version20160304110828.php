<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160304110828 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Music DROP FOREIGN KEY FK_C930D4E1137ABCF');
        $this->addSql('DROP TABLE Album');
        $this->addSql('DROP TABLE Music');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Album (id INT NOT NULL, artist VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, year INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Music (id INT NOT NULL, album_id INT DEFAULT NULL, INDEX IDX_C930D4E1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Album ADD CONSTRAINT FK_F8594147BF396750 FOREIGN KEY (id) REFERENCES Likeable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Music ADD CONSTRAINT FK_C930D4EBF396750 FOREIGN KEY (id) REFERENCES Likeable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Music ADD CONSTRAINT FK_C930D4E1137ABCF FOREIGN KEY (album_id) REFERENCES Album (id)');
    }
}
