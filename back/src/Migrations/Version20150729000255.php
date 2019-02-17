<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150729000255 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Beer (id INT NOT NULL, image_id INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, ects DOUBLE PRECISION NOT NULL, volume DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_F8C4C9933DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Beer ADD CONSTRAINT FK_F8C4C9933DA5256D FOREIGN KEY (image_id) REFERENCES Image (id)');
        $this->addSql('ALTER TABLE Beer ADD CONSTRAINT FK_F8C4C993BF396750 FOREIGN KEY (id) REFERENCES Likeable (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE Beer');
    }
}
