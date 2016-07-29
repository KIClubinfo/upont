<?php

namespace KI\UserBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\Facegame;
use KI\UserBundle\Event\AchievementCheckEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


// Valide les formulaires pour une entité et affiche la réponse à la demande
class FacegameHelper
{
    protected $manager;
    protected $repository;
    protected $dispatcher;
    protected $tokenStorage;

    public function __construct(
        EntityManager            $manager,
        EntityRepository         $repository,
        EventDispatcherInterface $dispatcher,
        TokenStorageInterface    $tokenStorage
    )
    {
        $this->manager      = $manager;
        $this->repository   = $repository;
        $this->dispatcher   = $dispatcher;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     *  On remplit la listUsers selon les paramètres rentrés
     *  Chaque array contient les noms proposés, une image et la position de la proposition correcte
     *  @param  Facegame $game La partie à populer
     *  @return bool Si la partie est possible ou non (assez d'élève dans la promo)
     */
    public function fillUserList(Facegame $facegame)
    {
        $hardcore = $facegame->getHardcore();
        $promo    = $facegame->getPromo();
        $player   = $facegame->getUser();
        $list     = $facegame->getListUsers();

        if ($hardcore) {
            $defaultTraits = ['department', 'promo', 'location', 'origin', 'nationality'];
            $nbTraits      = count($defaultTraits);
        }

        $users = $promo !== null ? $this->repository->findByPromo($promo) : $this->repository->findAll();

        $countUsers = count($users);
        if ($countUsers < 5) {
            return false;
        }

        // Gestion du nombre de questions possibles
        $nbQuestions = min(10, $countUsers/2 - 1);
        $nbPropositions = 3;

        while (count($list) < $nbQuestions) {
            $tempList = [];
            $ids      = [];

            $tempList['firstPart'] = count($list) < $nbQuestions/2;

            if ($hardcore) {
                // Si la promo est déjà établie on ne va pas la demander comme carac
                do {
                    $trait = $defaultTraits[rand(0, $nbTraits - 1)];
                } while ($promo !== null && $trait == 'promo');

                $tempList['trait'] = $trait;
                $userTraits = [];
            }

            // La réponse est décidée aléatoirement
            $tempList['answer'] = rand(0, $nbPropositions - 1);

            for ($i = 0; $i < $nbPropositions; $i++) {
                // On vérifie que l'user existe, qu'il a une image de profil,
                // qu'on ne propose pas le nom de la personne ayant lancé le test
                // et qu'on ne propose pas 2 fois la même caractéristique
                do {
                    // On vérifie qu'on ne propose pas deux fois le même nom
                    do {
                        $tempId = rand(0, $countUsers - 1);
                    } while (in_array($tempId, $ids));

                    $ids[] = $tempId;
                    $user  = $users[$tempId];

                    if ($hardcore) {
                        $method    = 'get'.ucfirst($trait);
                        $tempTrait = $user->$method();
                    }
                }
                while (!isset($user)
                    || $user->getImage() === null
                    || $user->getPromo() === null
                    || $user->getUsername() == $player->getUsername()
                    || $hardcore
                    && ($tempTrait === null || in_array($tempTrait, $userTraits, true))
                );

                $tempList[$i]['name'] = $user->getFirstName().' '.$user->getLastName();
                $tempList[$i]['picture'] = $user->getImage()->getWebPath();

                if ($hardcore) {
                    $userTraits[] = $tempTrait;
                    $tempList[$i]['trait'] = $tempTrait;
                }
            }
            $list[] = $tempList;
        }
        $facegame->setListUsers($list);
        return true;
    }

    /**
     * Finit un jeu
     * @param Facegame $game     La partie à résoudre
     * @param integer  $wrongAnswers Le nombre de mauvaises réponses
     */
    public function endGame(Facegame $game, $wrongAnswers, $duration)
    {
        if (!empty($game->getDuration())) {
            throw new BadRequestHttpException('Jeu déjà fini');
        }

        $game->setWrongAnswers($wrongAnswers);
        $game->setDuration($duration);
        $this->manager->flush();

        $achievementCheck = new AchievementCheckEvent(Achievement::GAME_PLAY);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $promoUser = (int)$this->tokenStorage->getToken()->getUser()->getPromo();
        $promoGame = (int)$game->getPromo();

        if ($wrongAnswers == 0 && $promoGame == $promoUser - 1 && $duration < 60) {

            $achievementCheck = new AchievementCheckEvent(Achievement::GAME_BEFORE);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        } else if ($wrongAnswers == 0 && $promoGame == $promoUser && $duration < 60) {

            $achievementCheck = new AchievementCheckEvent(Achievement::GAME_SELF);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        } else if ($wrongAnswers == 0 && $promoGame == $promoUser + 1 && $duration < 60) {

            $achievementCheck = new AchievementCheckEvent(Achievement::GAME_NEXT);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        }
        if ($wrongAnswers == 0 && $promoGame < $promoUser && $game->getHardcore() && $duration < 60) {

            $achievementCheck = new AchievementCheckEvent(Achievement::GAME_OLD);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        }
    }
}
