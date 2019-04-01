<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhotoRemoveCommand extends Command
{
    protected static $defaultName = 'upont:photo:remove';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Remove the photo of a user')
            ->addArgument('username', InputArgument::REQUIRED, 'The user whose photo is to be removed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->userRepository->findOneByUsername($input->getArgument('username'));
        $user->setImage(null);
        $this->entityManager->flush();
    }
}
