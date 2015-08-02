<?php

namespace KI\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UserBundle\Entity\Group;


class LoadGroupFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $group = new Group('admin');
        $group->setRoles(array('ROLE_ADMIN'));
        $manager->persist($group);
        $this->addReference('group-admin', $group);

        $group = new Group('modo');
        $group->setRoles(array('ROLE_MODO'));
        $manager->persist($group);
        $this->addReference('group-modo', $group);

        $group = new Group('user');
        $group->setRoles(array('ROLE_USER'));
        $manager->persist($group);
        $this->addReference('group-user', $group);

        // Peut ranger les fichiers PontHub
        $group = new Group('jardinier');
        $group->setRoles(array('ROLE_JARDINIER'));
        $manager->persist($group);
        $this->addReference('group-jardinier', $group);

        // Donne les droits en lecture seule
        $group = new Group('admissible');
        $group->addRole('ROLE_ADMISSIBLE');
        $manager->persist($group);
        $this->addReference('group-admissible', $group);

        // Donne accès au droit de poster pour un département par exemple
        // mais pas d'accès en lecture
        $group = new Group('exterieur');
        $group->addRole('ROLE_EXTERIEUR');
        $manager->persist($group);
        $this->addReference('group-exterieur', $group);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
