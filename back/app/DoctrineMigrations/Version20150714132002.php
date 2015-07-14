<?php

namespace Application\Migrations;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use KI\UpontBundle\Entity\Users\Achievement;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150714132002 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function postUp(Schema $schema)
    {
        // Chargement des nouveaux achievements
        $em = $this->container->get('doctrine')->getManager();
        $repo = $em->getRepository('KIUpontBundle:Users\Achievement');
        $achievements = $repo->findAll();
        $ids = array();
        foreach ($achievements as $achievement)
            $ids[] = $achievement->getIdA();

        foreach (Achievement::getConstants() as $id) {
            if (!in_array($id, $ids)) {
                $achievement = new Achievement($id);
                $em->persist($achievement);
            }
        }
        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
