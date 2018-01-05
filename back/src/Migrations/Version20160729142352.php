<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160729142352 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE Experience');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Experience (id INT NOT NULL, user_id INT DEFAULT NULL, startDate INT DEFAULT NULL, endDate INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, country VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, longitude DOUBLE PRECISION DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, category VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, company VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_4ACDC2D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
