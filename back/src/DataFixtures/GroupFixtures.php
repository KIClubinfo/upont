<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $group = new Group('admin');
        $group->setRoles(['ROLE_ADMIN']);
        $manager->persist($group);
        $this->addReference('group-admin', $group);

        $group = new Group('modo');
        $group->setRoles(['ROLE_MODO']);
        $manager->persist($group);
        $this->addReference('group-modo', $group);

        $group = new Group('user');
        $group->setRoles(['ROLE_USER']);
        $manager->persist($group);
        $this->addReference('group-user', $group);

        // Peut ranger les fichiers PontHub
        $group = new Group('jardinier');
        $group->setRoles(['ROLE_JARDINIER']);
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
}
