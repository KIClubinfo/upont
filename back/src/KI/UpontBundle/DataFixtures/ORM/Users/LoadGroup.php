<?php

namespace KI\UpontBundle\DataFixtures\ORM\Users;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\Group;


class LoadGroupFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $group = new Group('admin');
        $group->addRole('ROLE_ADMIN');
        $manager->persist($group);
        $this->addReference('group-admin', $group);

        $group = new Group('modo');
        $group->addRole('ROLE_MODO');
        $manager->persist($group);
        $this->addReference('group-modo', $group);

        $group = new Group('user');
        $group->addRole('ROLE_USER');
        $manager->persist($group);
        $this->addReference('group-user', $group);

        $group = new Group('jardinier');
        $group->addRole('ROLE_JARDINIER');
        $manager->persist($group);
        $this->addReference('group-jardinier', $group);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
