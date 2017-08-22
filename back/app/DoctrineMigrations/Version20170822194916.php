<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170822194916 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE family (id INT NOT NULL, image_id INT DEFAULT NULL, banner_id INT DEFAULT NULL, fullName VARCHAR(255) NOT NULL, presentation LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_A5E6215B3DA5256D (image_id), UNIQUE INDEX UNIQ_A5E6215B684EC833 (banner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE family ADD CONSTRAINT FK_A5E6215B3DA5256D FOREIGN KEY (image_id) REFERENCES Image (id)');
        $this->addSql('ALTER TABLE family ADD CONSTRAINT FK_A5E6215B684EC833 FOREIGN KEY (banner_id) REFERENCES Image (id)');
        $this->addSql('ALTER TABLE family ADD CONSTRAINT FK_A5E6215BBF396750 FOREIGN KEY (id) REFERENCES Likeable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user ADD family_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A6479C35E566A FOREIGN KEY (family_id) REFERENCES family (id)');
        $this->addSql('CREATE INDEX IDX_957A6479C35E566A ON fos_user (family_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A6479C35E566A');
        $this->addSql('DROP TABLE family');
        $this->addSql('DROP INDEX IDX_957A6479C35E566A ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP family_id');
    }
}
