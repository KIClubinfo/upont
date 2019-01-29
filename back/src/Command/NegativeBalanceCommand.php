<?php

namespace App\Command;

use App\Entity\User;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NegativeBalanceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:foyer:negativebalance')
            ->setDescription('Sends email to users with negatif balance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT usr FROM App:User usr WHERE usr.balance < 0');
        $iterableResult = $query->iterate();

        /**
         * @var $user User
         */
        foreach ($iterableResult as $row) {
            $user = $row[0];

            // Envoi du mail
            $message = (new Swift_Message('Pense à recharger ton compte foyer !'))
                ->setFrom('foyer.daube@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($this->getContainer()->get('twig')->render('negative-balance.html.twig', [
                    'user' => $user
                ]), 'text/html');

            $this->getContainer()->get('mailer')->send($message);
        }
    }
}
