<?php

namespace KI\UserBundle\Command;

use KI\UserBundle\Entity\Facegame;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use KI\UserBundle\Event\AchievementCheckEvent;
use KI\UserBundle\Entity\Achievement;

class FacegameAchievementsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:achievements:facegame')
            ->setDescription('Checks facegames for achievements')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT fg FROM KIUserBundle:Facegame fg');
        $iterableResult = $query->iterate();

        /**
         * @var $facegame Facegame
         */
        foreach ($iterableResult as $row) {
            $facegame = $row[0];
            $user = $facegame->getUser();

            $achievementCheck = new AchievementCheckEvent(Achievement::GAME_PLAY, $user);
            $dispatcher = $this->getContainer()->get('event_dispatcher');

            $dispatcher->dispatch('upont.achievement', $achievementCheck);

            $promoUser = (int)$user->getPromo();
            $promoGame = (int)$facegame->getPromo();

            $wrongAnswers = $facegame->getWrongAnswers();
            $duration = $facegame->getDuration();

            if ($wrongAnswers == 0 && $promoGame == $promoUser - 1 && $duration < 60 * 1000) {

                $achievementCheck = new AchievementCheckEvent(Achievement::GAME_BEFORE, $user);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);

            } else if ($wrongAnswers == 0 && $promoGame == $promoUser && $duration < 60 * 1000) {

                $achievementCheck = new AchievementCheckEvent(Achievement::GAME_SELF, $user);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);

            } else if ($wrongAnswers == 0 && $promoGame == $promoUser + 1 && $duration < 60 * 1000) {

                $achievementCheck = new AchievementCheckEvent(Achievement::GAME_NEXT, $user);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);

            }
            if ($wrongAnswers == 0 && $promoGame < $promoUser && $facegame->getHardcore() && $duration < 60 * 1000) {

                $achievementCheck = new AchievementCheckEvent(Achievement::GAME_OLD, $user);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);

            }

            $em->detach($row[0]);
        }
    }
}
