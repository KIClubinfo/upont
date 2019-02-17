<?php

namespace App\Command;

use App\Entity\Club;
use App\Entity\ClubUser;
use App\Repository\ClubRepository;
use App\Repository\ClubUserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ClubUpdateCommand extends Command
{
    protected static $defaultName = 'upont:update:clubs';
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var ClubRepository
     */
    private $clubRepository;
    /**
     * @var ClubUserRepository
     */
    private $clubUserRepository;
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(EntityManager $entityManager, ClubRepository $clubRepository, ClubUserRepository $clubUserRepository, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->clubRepository = $clubRepository;
        $this->clubUserRepository = $clubUserRepository;
        $this->params = $params;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Enable or Disable listed clubs according to the existence of members in current associative promo')
            ->addArgument('clubs', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Clubs to update')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Update all clubs')
            ->addOption('preview', 'p', InputOption::VALUE_NONE, 'Show clubs to be updated without modification')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assoPromo = $this->params->get('upont')['promos']['assos'];
        $clubSlugs = $input->getArgument('clubs');

        if ($input->getOption('all')) {
            $clubsToUpdate = $this->clubRepository->findAll();
        }
        else {
            $clubsToUpdate = array_map([$this->clubRepository, 'findOneBySlug'], $clubSlugs);
        }

        $clubNumber = -1;
        foreach ($clubsToUpdate as $clubToUpdate) {
            $clubNumber++;
            if (count($clubToUpdate) == 0) {
                $output->writeln('<error>The slug "'.$clubSlugs[$clubNumber].'" doesn\'t match with any club</error>');
                continue;
            }
            $countUsers = $this->clubUserRepository->getCountUsersInClubWithPromo($clubToUpdate, $assoPromo);

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

        $this->entityManager->flush();
    }
}
