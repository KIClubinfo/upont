<?php

namespace KI\UserBundle\Command;

use KI\UserBundle\Entity\Club;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClubUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:update:clubs')
            ->setDescription('Enable or Disable listed clubs according to the existence of members in current associative promo')
            ->addArgument('clubs', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Clubs to update')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Update all clubs')
            ->addOption('preview', 'p', InputOption::VALUE_NONE, 'Show clubs to be updated without modification')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $this->getContainer()->get('doctrine')->getRepository(Club::class);
        $assoPromo = $this->getContainer()->getParameter('upont')['promos']['assos'];
        $clubSlugs = $input->getArgument('clubs');

        if ($input->getOption('all')) {
            $clubsToUpdate = $repo->findAll();
        }
        else {
            $clubsToUpdate = array_map([$repo, 'findOneBySlug'], $clubSlugs);
        }

        foreach ($clubsToUpdate as $clubToUpdate) {
            $clubUser = $em->createQuery('SELECT cu
                    FROM KIUserBundle:ClubUser cu,
                    KIUserBundle:User user
                    WHERE cu.club = :club
                  AND cu.user = user
                    AND user.promo = :promo')
            ->setParameter('club', $clubToUpdate)
            ->setParameter('promo', $assoPromo)
            ->setMaxResults(1)
            ->getOneOrNullResult();

            if (!$clubUser && $clubToUpdate->getActive()) {
                if ($input->getOption('preview')) {
                    $output->writeln('<comment>'.$clubToUpdate->getFullName().' to be disabled'.'</comment>');
                }
                else {
                    $clubToUpdate->setActive(false);
                    $output->writeln('<comment>'.$clubToUpdate->getFullName().' disabled'.'</comment>');
                }
            }
            else if ($clubUser && !$clubToUpdate->getActive()) {
                if ($input->getOption('preview')) {
                    $output->writeln('<comment>'.$clubToUpdate->getFullName().' to be enabled'.'</comment>');
                }
                else {
                    $clubToUpdate->setActive(true);
                    $output->writeln('<comment>'.$clubToUpdate->getFullName().' enabled'.'</comment>');
                }
            }
        }

        $em->flush();
    }
}
