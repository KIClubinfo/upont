<?php

namespace KI\FoyerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\FoyerBundle\Entity\BeerUser;

class LoadBeerUserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $beerUser = new BeerUser();
        $beerUser->setDate(time()-3600);
        $beerUser->setAmount(1);
        $beerUser->setBeer($this->getReference('beer-leffe'));
        $beerUser->setUser($this->getReference('user-trancara'));
        $manager->persist($beerUser);

        for ($i = 0; $i < 6; $i++) {
            $beerUser = new BeerUser();
            $beerUser->setDate(time()-900*$i);
            $beerUser->setAmount(1);
            $beerUser->setBeer($this->getReference('beer-kro'));
            $beerUser->setUser($this->getReference('user-de-boisc'));
            $manager->persist($beerUser);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 62;
    }
}
