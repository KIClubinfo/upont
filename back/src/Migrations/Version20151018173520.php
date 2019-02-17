<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151018173520 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Movie CHANGE vo vo TINYINT(1) NOT NULL, CHANGE vf vf TINYINT(1) NOT NULL, CHANGE vost vost TINYINT(1) NOT NULL, CHANGE vostfr vostfr TINYINT(1) NOT NULL, CHANGE hd hd TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Movie CHANGE vo vo TINYINT(1) DEFAULT NULL, CHANGE vf vf TINYINT(1) DEFAULT NULL, CHANGE vost vost TINYINT(1) DEFAULT NULL, CHANGE vostfr vostfr TINYINT(1) DEFAULT NULL, CHANGE hd hd TINYINT(1) DEFAULT NULL');
    }
}
