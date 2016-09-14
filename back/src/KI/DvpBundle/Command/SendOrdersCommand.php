<?php

namespace KI\DvpBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NoResultException;
use KI\DvpBundle\Entity\BasketDate;;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Swift_Message;


class SendOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:dvp:sendorders')
            ->setDescription('Sends email to users with negatif balance')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT date 
            FROM KIDvpBundle:BasketDate date 
            WHERE date.dateRetrieve > :now AND date.locked = 0 
            ORDER BY date.dateRetrieve ASC')
            ->setParameter('now', new \DateTime())
            ->setMaxResults(1);
        /**
         * @var $basketDate BasketDate
         */
        $basketDate = $query->getOneOrNullResult();

        if($basketDate) {

            $orders = $basketDate->getOrders();

            // Envoi du mail
            $message = Swift_Message::newInstance()
                ->setSubject('Commandes du ' . $basketDate->getDateRetrieve()->format('l d F Y'))
                ->setFrom('noreply@upont.enpc.fr')
                ->setTo('paniersprimeur@upont.enpc.fr')
                ->setBody($this->getContainer()->get('twig')->render('KIDvpBundle::basket-orders.html.twig', [
                    'orders' => $orders
                ]), 'text/html');

            $this->getContainer()->get('mailer')->send($message);

            // Verrouillage de la semaine
            $basketDate->setLocked(true);

            $prevDate = $basketDate->getDateRetrieve();
        } else {
            $prevDate = new \DateTime();
        }

        // Création des semaines suivantes
        $query = $em->createQuery('SELECT COUNT(date.id) 
            FROM KIDvpBundle:BasketDate date 
            WHERE date.dateRetrieve > :prev AND date.locked = 0 
            ORDER BY date.dateRetrieve ASC')
            ->setParameter('prev', $prevDate);

        $count = $query->getSingleScalarResult();

        while($count < 4) {
            $prevDate->modify('next thursday');

            $dateExists = $query = $em->createQuery('SELECT COUNT(date.id) 
                FROM KIDvpBundle:BasketDate date 
                WHERE date.dateRetrieve = :dateAdding')
                ->setParameter('dateAdding', $prevDate)
                ->getSingleScalarResult();

            if($dateExists == 0) {
                $nextBasketDate = new BasketDate();
                $nextBasketDate->setDateRetrieve($prevDate);
                $em->persist($nextBasketDate);
                $em->flush();
                $count++;
            }
        }

    }
}
