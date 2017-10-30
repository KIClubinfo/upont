<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171015200834 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Post ADD publicationState VARCHAR(255) DEFAULT \'published\' NOT NULL');
        $this->addSql('
            UPDATE Post
            SET Post.publicationState = \'emailed\'
            WHERE Post.send_mail
            ');
        $this->addSql('ALTER TABLE Post DROP send_mail');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Post ADD send_mail TINYINT(1) DEFAULT NULL');
        $this->addSql('
            UPDATE Post
            SET Post.send_mail = true
            WHERE Post.publicationState = \'emailed\'
            SET Post.send_mail = false
            WHERE Post.publicationState != \'emailed\'
            ');
        $this->addSql('ALTER TABLE Post DROP publicationState');
    }
}
