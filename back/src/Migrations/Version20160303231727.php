<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160303231727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Movie DROP vo, DROP vf, DROP vost, DROP vostfr, DROP hd');
        $this->addSql('ALTER TABLE Serie DROP vo, DROP vf, DROP vost, DROP vostfr, DROP hd');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Serie ADD vo TINYINT(1) DEFAULT NULL, ADD vf TINYINT(1) DEFAULT NULL, ADD vost TINYINT(1) DEFAULT NULL, ADD vostfr TINYINT(1) DEFAULT NULL, ADD hd TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE Movie ADD vo TINYINT(1) NOT NULL, ADD vf TINYINT(1) NOT NULL, ADD vost TINYINT(1) NOT NULL, ADD vostfr TINYINT(1) NOT NULL, ADD hd TINYINT(1) NOT NULL');
    }
}
