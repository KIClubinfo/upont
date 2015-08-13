<?php

namespace KI\PublicationBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;

class NewsitemsController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Newsitem', 'Publication');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les news",
     *  output="KI\PublicationBundle\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getNewsitemsAction()
    {
        return $this->getAll($this->get('security.context')->isGranted('ROLE_EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Retourne une news",
     *  output="KI\PublicationBundle\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getNewsitemAction($slug)
    {
        return $this->getOne($slug, $this->get('security.context')->isGranted('ROLE_EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Crée une news",
     *  input="KI\PublicationBundle\Form\NewsitemType",
     *  output="KI\PublicationBundle\Entity\Newsitem",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function postNewsitemAction()
    {
        $return = $this->postData($this->isClubMember());

        if ($return['code'] == 201) {
            // On modifie légèrement la ressource qui vient d'être créée
            $return['item']->setDate(time());
            $return['item']->setAuthorUser($this->container->get('security.context')->getToken()->getUser());

            $club = $return['item']->getAuthorClub();
            $text = substr($return['item']->getText(), 0, 140).'...';

            // Si ce n'est pas un event perso, on notifie les utilisateurs suivant le club
            if ($club) {
                $dispatcher = $this->container->get('event_dispatcher');
                $achievementCheck = new AchievementCheckEvent(Achievement::NEWS_CREATE);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);

                $allUsers = $this->manager->getRepository('KIUserBundle:User')->findAll();
                $users = array();

                foreach ($allUsers as $candidate) {
                    if ($candidate->getClubsNotFollowed()->contains($club)) {
                        $users[] = $candidate;
                    }
                }

                $text = substr($return['item']->getText(), 0, 140).'...';
                $this->get('ki_user.service.notify')->notify(
                    'notif_followed_news',
                    $return['item']->getName(),
                    $text,
                    'exclude',
                    $users
                );
            } else {
                // Si c'est une news perso on notifie tous ceux qui ont envie
                $this->get('ki_user.service.notify')->notify(
                    'notif_news_perso',
                    $return['item']->getName(),
                    $text,
                    'exclude',
                    array()
                );
            }
        }

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une news",
     *  input="KI\PublicationBundle\Form\NewsitemType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function patchNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        return $this->patch($slug, $this->isClubMember($club));
    }

    /**
     * @ApiDoc(
     *  description="Supprime une news",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function deleteNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        return $this->delete($slug, $this->isClubMember($club));
    }
}
