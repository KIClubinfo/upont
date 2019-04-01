<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150801175559 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE BeerUser (id INT AUTO_INCREMENT NOT NULL, beer_id INT NOT NULL, user_id INT NOT NULL, date INT NOT NULL, INDEX IDX_DAF296D0989053 (beer_id), INDEX IDX_DAF296A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE BeerUser ADD CONSTRAINT FK_DAF296D0989053 FOREIGN KEY (beer_id) REFERENCES Beer (id)');
        $this->addSql('ALTER TABLE BeerUser ADD CONSTRAINT FK_DAF296A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('DROP TABLE fix_respos');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fix_respos (fix_id INT NOT NULL, respo_id INT NOT NULL, INDEX IDX_74B15CFECCC6CC89 (fix_id), INDEX IDX_74B15CFEDCF84E11 (respo_id), PRIMARY KEY(fix_id, respo_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fix_respos ADD CONSTRAINT FK_74B15CFECCC6CC89 FOREIGN KEY (fix_id) REFERENCES Fix (id)');
        $this->addSql('ALTER TABLE fix_respos ADD CONSTRAINT FK_74B15CFEDCF84E11 FOREIGN KEY (respo_id) REFERENCES fos_user (id)');
        $this->addSql('DROP TABLE BeerUser');
    }
}
