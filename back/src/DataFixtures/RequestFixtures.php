<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Request;

class RequestFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request = new Request();
        $request->setName('Guardians Of The Galaxy');
        $request->setVotes(42);
        $request->setUser($this->getReference('user-trancara'));
        $request->setDate(time() - 9000);
        $manager->persist($request);

        $request = new Request();
        $request->setName('Windows vista cracké');
        $request->setVotes(-3);
        $request->setUser($this->getReference('user-muzardt'));
        $request->setDate(time());
        $manager->persist($request);

        $manager->flush();
    }

    public function getOrder()
    {
        return 31;
    }
}
