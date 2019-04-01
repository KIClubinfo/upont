<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151208203021 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Basket (id INT NOT NULL, content LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE BasketOrder (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, basket_id INT DEFAULT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, dateOrder INT NOT NULL, dateRetrieve INT DEFAULT NULL, paid TINYINT(1) DEFAULT NULL, INDEX IDX_24DE5A65A76ED395 (user_id), INDEX IDX_24DE5A651BE1FB52 (basket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Basket ADD CONSTRAINT FK_25EA554DBF396750 FOREIGN KEY (id) REFERENCES Likeable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE BasketOrder ADD CONSTRAINT FK_24DE5A65A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE BasketOrder ADD CONSTRAINT FK_24DE5A651BE1FB52 FOREIGN KEY (basket_id) REFERENCES Basket (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE BasketOrder DROP FOREIGN KEY FK_24DE5A651BE1FB52');
        $this->addSql('DROP TABLE Basket');
        $this->addSql('DROP TABLE BasketOrder');
    }
}
