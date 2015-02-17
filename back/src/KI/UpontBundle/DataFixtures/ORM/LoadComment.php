<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Comment;

class LoadCommentFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $comment = new Comment();
        $comment->setDate(time());
        $comment->setText('Génial !');
        $comment->setAuthor($this->getReference('user-trancara'));
        $comment->addLike($this->getReference('user-taquet-c'));
        $comment->addLike($this->getReference('user-dziris'));
        $comment->addDislike($this->getReference('user-de-boisc'));
        $manager->persist($comment);
        $this->addReference('comment-genial', $comment);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
