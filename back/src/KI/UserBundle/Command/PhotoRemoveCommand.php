<?php
namespace KI\UserBundle\Command;
use KI\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
class PhotoRemoveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:remove:photo')
            ->setDescription('Import missing photos from Facebook for the given promo')
            ->addArgument('username', InputArgument::REQUIRED, 'The user whose photo is to be removed.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $repo->findOneByUsername($input->getArgument('username'));
        $user->getImage()->removeUpload();
        $image = null;
        $user->setImage($image);
	$em->flush();
    }
}
?>
