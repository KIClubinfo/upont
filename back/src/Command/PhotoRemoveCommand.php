<?php
namespace App\Command;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhotoRemoveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:remove:photo')
            ->setDescription('Remove the photo of a user')
            ->addArgument('username', InputArgument::REQUIRED, 'The user whose photo is to be removed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $repo->findOneByUsername($input->getArgument('username'));
        $user->setImage(null);
        $em->flush();
    }
}
