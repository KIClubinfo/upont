<?php

namespace KI\FoyerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\FoyerBundle\Entity\Transaction;

class LoadTransactionFixture extends AbstractFixture implements OrderedFixtureInterface
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

    public function getOrder()
    {
        return 62;
    }
}
