<?php

namespace App\DataFixtures;

use App\Entity\Request;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RequestFixtures extends Fixture implements DependentFixtureInterface
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

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
