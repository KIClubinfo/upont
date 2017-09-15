<?php

namespace KI\UserBundle\Command;

use KI\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DepartmentUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:update:department')
            ->setDescription('Update users\' departments for the given list of usernames : "username1,username2..."')
            ->addArgument('department', InputArgument::REQUIRED, 'The department.')
            ->addArgument('usernames', InputArgument::REQUIRED, 'The usernames of the users whose departments are to be updated or the file that contains it if -f is set')
            ->addOption('file', 'f', InputOption::VALUE_NONE, 'If set, the absolute path of the file that contains the list of usernames must be given as second argument instead of the list itself')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $successCount = 0;

        if ($input->getOption('file')) {
            $list = fopen($input->getArgument('usernames'), 'r+');
            $usernames = str_replace(["\r", "\n"], ['', ''], fgets($list));
            $usernameArray = explode(',', $usernames);
        }
        else {
            $usernameArray = explode(',', $input->getArgument('usernames'));
        }

        foreach ($usernameArray as $username) {
            $user = $repo->findOneByUsername($username);
            if ($user) {
                $user->setDepartment($input->getArgument('department'));
                $successCount++;
            }
            else {
                $output->writeln('<error>Username '.$username.' n\'existe pas<error>');
            }
        }
        $em->flush();

        $output->writeln('<comment>'.$input->getArgument('department').' : '.$successCount.' élève'.($successCount >= 2 ? 's' : '').' sur '.count($usernameArray).' mis à jour'.'<comment>');
    }
}
