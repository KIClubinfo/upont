<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170620162245 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE Admissible');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Admissible (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, firstName VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, lastName VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, date INT NOT NULL, scei VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, contact VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, room VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, serie INT NOT NULL, details LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, year INT NOT NULL, UNIQUE INDEX UNIQ_C949BD7989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
