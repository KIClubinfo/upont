<?php

namespace KI\UserBundle\Command;

use KI\UserBundle\Entity\Club;
use KI\UserBundle\Entity\ClubUser;
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
        $userRepo = $this->getContainer()->get('doctrine')->getRepository(Club::class);
        $clubUserRepo = $this->getContainer()->get('doctrine')->getRepository(ClubUser::class);
        $assoPromo = $this->getContainer()->getParameter('upont')['promos']['assos'];
        $clubSlugs = $input->getArgument('clubs');

        if ($input->getOption('all')) {
            $clubsToUpdate = $userRepo->findAll();
        }
        else {
            $clubsToUpdate = array_map([$userRepo, 'findOneBySlug'], $clubSlugs);
        }

        $clubNumber = -1;
        foreach ($clubsToUpdate as $clubToUpdate) {
            $clubNumber++;
            if (count($clubToUpdate) == 0) {
                $output->writeln('<error>The slug "'.$clubSlugs[$clubNumber].'" doesn\'t match with any club</error>');
                continue;
            }
            $countUsers = $clubUserRepo->getCountUsersInClubWithPromo($clubToUpdate, $assoPromo);

            if ($countUsers == 0 && $clubToUpdate->getActive()) {
                if ($input->getOption('preview')) {
                    $output->writeln('<comment>'.$clubToUpdate->getFullName().' to be disabled'.'</comment>');
                }
                else {
                    $clubToUpdate->setActive(false);
                    $output->writeln('<comment>'.$clubToUpdate->getFullName().' disabled'.'</comment>');
                }
            }
            else if ($countUsers > 0 && !$clubToUpdate->getActive()) {
                if ($input->getOption('preview')) {
                    $output->writeln('<info>'.$clubToUpdate->getFullName().' to be enabled'.'</info>');
                }
                else {
                    $clubToUpdate->setActive(true);
                    $output->writeln('<info>'.$clubToUpdate->getFullName().' enabled'.'</info>');
                }
            }
        }

        $em->flush();
    }
}
