<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151025122328 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Transaction DROP FOREIGN KEY FK_DAF296A76ED395');
        $this->addSql('ALTER TABLE Transaction DROP FOREIGN KEY FK_DAF296D0989053');
        $this->addSql('DROP INDEX idx_daf296d0989053 ON Transaction');
        $this->addSql('CREATE INDEX IDX_F4AB8A06D0989053 ON Transaction (beer_id)');
        $this->addSql('DROP INDEX idx_daf296a76ed395 ON Transaction');
        $this->addSql('CREATE INDEX IDX_F4AB8A06A76ED395 ON Transaction (user_id)');
        $this->addSql('ALTER TABLE Transaction ADD CONSTRAINT FK_DAF296A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE Transaction ADD CONSTRAINT FK_DAF296D0989053 FOREIGN KEY (beer_id) REFERENCES Beer (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Transaction DROP FOREIGN KEY FK_F4AB8A06D0989053');
        $this->addSql('ALTER TABLE Transaction DROP FOREIGN KEY FK_F4AB8A06A76ED395');
        $this->addSql('DROP INDEX idx_f4ab8a06d0989053 ON Transaction');
        $this->addSql('CREATE INDEX IDX_DAF296D0989053 ON Transaction (beer_id)');
        $this->addSql('DROP INDEX idx_f4ab8a06a76ed395 ON Transaction');
        $this->addSql('CREATE INDEX IDX_DAF296A76ED395 ON Transaction (user_id)');
        $this->addSql('ALTER TABLE Transaction ADD CONSTRAINT FK_F4AB8A06D0989053 FOREIGN KEY (beer_id) REFERENCES Beer (id)');
        $this->addSql('ALTER TABLE Transaction ADD CONSTRAINT FK_F4AB8A06A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
    }
}
