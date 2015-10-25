<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use KI\PublicationBundle\Entity\Newsitem;
use KI\PublicationBundle\Entity\Event;
use KI\PublicationBundle\Entity\Message;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151025173617 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE NewsitemFile (id INT AUTO_INCREMENT NOT NULL, newsitem_id INT DEFAULT NULL, ext VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, size INT NOT NULL, INDEX IDX_BD4EDD8567F576A9 (newsitem_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE NewsitemFile ADD CONSTRAINT FK_BD4EDD8567F576A9 FOREIGN KEY (newsitem_id) REFERENCES Newsitem (id)');

        $this->addSql('CREATE TABLE Message (id INT NOT NULL, image_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_790009E33DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Message ADD CONSTRAINT FK_790009E33DA5256D FOREIGN KEY (image_id) REFERENCES Image (id)');
        $this->addSql('ALTER TABLE Message ADD CONSTRAINT FK_790009E3BF396750 FOREIGN KEY (id) REFERENCES Likeable (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE Post DROP FOREIGN KEY FK_FAB8C3B3B254E200');
        $this->addSql('DROP INDEX IDX_FAB8C3B3B254E200 ON Post');

        $this->addSql('ALTER TABLE Newsitem DROP FOREIGN KEY FK_16B131903DA5256D');
        $this->addSql('DROP INDEX UNIQ_16B131903DA5256D ON Newsitem');
        $this->addSql('ALTER TABLE Newsitem ADD authorClub_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Newsitem ADD CONSTRAINT FK_16B13190B254E200 FOREIGN KEY (authorClub_id) REFERENCES Club (id)');
        $this->addSql('CREATE INDEX IDX_16B13190B254E200 ON Newsitem (authorClub_id)');

        $this->addSql('DROP TABLE PostFile');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE PostFile (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, ext VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, size INT NOT NULL, INDEX IDX_C1F76FF14B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE PostFile ADD CONSTRAINT FK_C1F76FF14B89032C FOREIGN KEY (post_id) REFERENCES Post (id)');

        $this->addSql('DROP INDEX IDX_16B13190B254E200 ON Newsitem');
        $this->addSql('ALTER TABLE Newsitem DROP FOREIGN KEY FK_16B13190B254E200');
        $this->addSql('ALTER TABLE Newsitem DROP authorclub_id');
        $this->addSql('ALTER TABLE Newsitem ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Newsitem ADD CONSTRAINT FK_16B131903DA5256D FOREIGN KEY (image_id) REFERENCES Image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16B131903DA5256D ON Newsitem (image_id)');

        $this->addSql('ALTER TABLE Post ADD authorClub_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B3B254E200 FOREIGN KEY (authorClub_id) REFERENCES Club (id)');
        $this->addSql('CREATE INDEX IDX_FAB8C3B3B254E200 ON Post (authorClub_id)');

        $this->addSql('DROP TABLE Message');

        $this->addSql('DROP TABLE NewsitemFile');
    }

    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $this->addSql("UPDATE Newsitem t0 INNER JOIN Post t1 ON t0.id = t1.id SET t0.authorClub_id = t1.authorClub_id");
        $this->addSql('ALTER TABLE Post DROP authorClub_id');

        $messages = $em->getRepository('KIPublicationBundle:Newsitem')->findBy(array('name' => 'message' ));

        foreach($messages as $item) {
            $message = new Message();
            $message->setName($item->getName());
            $message->setAuthorUser($item->getAuthorUser());
            $message->setDate($item->getDate());
            $message->setText($item->getText());
            $message->setComments($item->getComments());
            $message->setDislikes($item->getDislikes());
            $message->setLikes($item->getLikes());
            $image_id = $this->connection->prepare("SELECT image_id FROM Newsitem WHERE id = ".$item->getId());
            $image_id->execute();
            $image_id = $image_id->fetchColumn();

            if(!is_null($image_id))
                $message->setImage($em->getRepository('KICoreBundle:Image')->find($image_id));

            $em->persist($message);
            $em->remove($item);

            $em->flush();
        }

        $this->addSql('ALTER TABLE Newsitem DROP image_id');
    }
}
