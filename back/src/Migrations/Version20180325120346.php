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

        $this->addSql('ALTER TABLE Event CHANGE startDate startDate DATETIME NOT NULL COMMENT \'(DC2Type:carbondatetime)\', CHANGE endDate endDate DATETIME NOT NULL COMMENT \'(DC2Type:carbondatetime)\', CHANGE shotgunDate shotgunDate DATETIME DEFAULT NULL COMMENT \'(DC2Type:carbondatetime)\'');
        $this->addSql('ALTER TABLE EventUser CHANGE shotgunDate shotgunDate DATETIME NOT NULL COMMENT \'(DC2Type:carbondatetime)\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Event CHANGE startDate startDate INT NOT NULL, CHANGE endDate endDate INT NOT NULL, CHANGE shotgunDate shotgunDate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE EventUser CHANGE shotgunDate shotgunDate INT NOT NULL');
    }
}
