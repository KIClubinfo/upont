<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $transaction = new Transaction();
        $transaction->setDate(time()-3600);
        $transaction->setAmount(-1);
        $transaction->setBeer($this->getReference('beer-leffe'));
        $transaction->setUser($this->getReference('user-trancara'));
        $manager->persist($transaction);

        for ($i = 0; $i < 6; $i++) {
            $transaction = new Transaction();
            $transaction->setDate(time()-900*$i);
            $transaction->setAmount(-1);
            $transaction->setBeer($this->getReference('beer-kro'));
            $transaction->setUser($this->getReference('user-de-boisc'));
            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            BeerFixtures::class
        ];
    }
}
