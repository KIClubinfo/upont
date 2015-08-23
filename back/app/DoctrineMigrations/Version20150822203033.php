<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150822203033 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user ADD mailEvent TINYINT(1) NOT NULL, ADD mailModification TINYINT(1) NOT NULL, ADD mailShotgun TINYINT(1) NOT NULL, DROP details, CHANGE statsFoyer statsFoyer TINYINT(1) NOT NULL, CHANGE statsPonthub statsPonthub TINYINT(1) NOT NULL, CHANGE statsFacegame statsFacegame TINYINT(1) NOT NULL');
        $this->addSql('UPDATE fos_user SET mailEvent="1", mailModification="1", mailShotgun ="1", statsFoyer="1", statsPonthub="1", statsFacegame="1"');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user ADD details LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, DROP mailEvent, DROP mailModification, DROP mailShotgun, CHANGE statsFoyer statsFoyer TINYINT(1) DEFAULT NULL, CHANGE statsPonthub statsPonthub TINYINT(1) DEFAULT NULL, CHANGE statsFacegame statsFacegame TINYINT(1) DEFAULT NULL');
    }
}
