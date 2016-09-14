<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160913143620 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE BasketDate (id INT AUTO_INCREMENT NOT NULL, dateRetrieve DATE NOT NULL, locked TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE BasketOrder ADD dateRetrieve_id INT DEFAULT NULL, DROP dateRetrieve');
        $this->addSql('ALTER TABLE BasketOrder ADD CONSTRAINT FK_24DE5A6535A47674 FOREIGN KEY (dateRetrieve_id) REFERENCES BasketDate (id)');
        $this->addSql('CREATE INDEX IDX_24DE5A6535A47674 ON BasketOrder (dateRetrieve_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6303BDEB92EA88D ON BasketDate (dateRetrieve)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_E6303BDEB92EA88D ON BasketDate (dateRetrieve)');
        $this->addSql('ALTER TABLE BasketOrder DROP FOREIGN KEY FK_24DE5A6535A47674');
        $this->addSql('DROP TABLE BasketDate');
        $this->addSql('DROP INDEX IDX_24DE5A6535A47674 ON BasketOrder');
        $this->addSql('ALTER TABLE BasketOrder ADD dateRetrieve DATE NOT NULL, DROP dateRetrieve_id');
    }
}
