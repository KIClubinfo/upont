<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180325120346 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Event

        $this->addSql('ALTER TABLE Event ADD newDate DATETIME');

        $this->addSql('UPDATE Event SET newDate = FROM_UNIXTIME(startDate)');
        $this->addSql('ALTER TABLE Event CHANGE startDate startDate DATETIME NOT NULL COMMENT \'(DC2Type:carbondatetime)\'');
        $this->addSql('UPDATE Event SET startDate = newDate');

        $this->addSql('UPDATE Event SET newDate = FROM_UNIXTIME(endDate)');
        $this->addSql('ALTER TABLE Event CHANGE endDate endDate DATETIME NOT NULL COMMENT \'(DC2Type:carbondatetime)\'');
        $this->addSql('UPDATE Event SET endDate = newDate');

        $this->addSql('UPDATE Event SET newDate = FROM_UNIXTIME(shotgunDate)');
        $this->addSql('ALTER TABLE Event CHANGE shotgunDate shotgunDate DATETIME DEFAULT NULL COMMENT \'(DC2Type:carbondatetime)\'');
        $this->addSql('UPDATE Event SET shotgunDate = newDate');

        $this->addSql('ALTER TABLE Event DROP COLUMN newDate');

        // EventUser

        $this->addSql('ALTER TABLE EventUser ADD newDate DATETIME');

        $this->addSql('UPDATE EventUser SET newDate = FROM_UNIXTIME(shotgunDate)');
        $this->addSql('ALTER TABLE EventUser CHANGE shotgunDate shotgunDate DATETIME NOT NULL COMMENT \'(DC2Type:carbondatetime)\'');
        $this->addSql('UPDATE EventUser SET shotgunDate = newDate');

        $this->addSql('ALTER TABLE EventUser DROP COLUMN newDate');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Event

        $this->addSql('ALTER TABLE Event ADD newDate INT DEFAULT NULL');

        $this->addSql('UPDATE Event SET newDate = UNIX_TIMESTAMP(startDate)');
        $this->addSql('ALTER TABLE Event CHANGE startDate startDate INT NOT NULL');
        $this->addSql('UPDATE Event SET startDate = newDate');

        $this->addSql('UPDATE Event SET newDate = UNIX_TIMESTAMP(endDate)');
        $this->addSql('ALTER TABLE Event CHANGE endDate endDate INT NOT NULL');
        $this->addSql('UPDATE Event SET endDate = newDate');

        $this->addSql('UPDATE Event SET newDate = UNIX_TIMESTAMP(shotgunDate)');
        $this->addSql('ALTER TABLE Event CHANGE shotgunDate shotgunDate INT DEFAULT NULL');
        $this->addSql('UPDATE Event SET shotgunDate = newDate');

        $this->addSql('ALTER TABLE Event DROP COLUMN newDate');

        // EventUser

        $this->addSql('ALTER TABLE EventUser ADD newDate INT DEFAULT NULL');

        $this->addSql('UPDATE EventUser SET newDate = UNIX_TIMESTAMP(shotgunDate)');
        $this->addSql('ALTER TABLE EventUser CHANGE shotgunDate shotgunDate INT NOT NULL');
        $this->addSql('UPDATE EventUser SET shotgunDate = newDate');

        $this->addSql('ALTER TABLE EventUser DROP COLUMN newDate');
    }
}
