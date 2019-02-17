<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170910121605 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications_recipient DROP FOREIGN KEY FK_526CE266A76ED395');
        $this->addSql('ALTER TABLE notifications_recipient DROP FOREIGN KEY FK_526CE266D4BE081');
        $this->addSql('ALTER TABLE notifications_recipient ADD CONSTRAINT FK_526CE266A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications_recipient ADD CONSTRAINT FK_526CE266D4BE081 FOREIGN KEY (notifications_id) REFERENCES Notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications_read DROP FOREIGN KEY FK_73A811C8A76ED395');
        $this->addSql('ALTER TABLE notifications_read DROP FOREIGN KEY FK_73A811C8EF1A9D84');
        $this->addSql('ALTER TABLE notifications_read ADD CONSTRAINT FK_73A811C8A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications_read ADD CONSTRAINT FK_73A811C8EF1A9D84 FOREIGN KEY (notification_id) REFERENCES Notification (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notifications_read DROP FOREIGN KEY FK_73A811C8EF1A9D84');
        $this->addSql('ALTER TABLE notifications_read DROP FOREIGN KEY FK_73A811C8A76ED395');
        $this->addSql('ALTER TABLE notifications_read ADD CONSTRAINT FK_73A811C8EF1A9D84 FOREIGN KEY (notification_id) REFERENCES Notification (id)');
        $this->addSql('ALTER TABLE notifications_read ADD CONSTRAINT FK_73A811C8A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE notifications_recipient DROP FOREIGN KEY FK_526CE266D4BE081');
        $this->addSql('ALTER TABLE notifications_recipient DROP FOREIGN KEY FK_526CE266A76ED395');
        $this->addSql('ALTER TABLE notifications_recipient ADD CONSTRAINT FK_526CE266D4BE081 FOREIGN KEY (notifications_id) REFERENCES Notification (id)');
        $this->addSql('ALTER TABLE notifications_recipient ADD CONSTRAINT FK_526CE266A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
    }
}
