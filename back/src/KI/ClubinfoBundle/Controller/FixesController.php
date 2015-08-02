<?php

namespace KI\ClubinfoBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;

class FixesController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Fix', 'Clubinfo');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les tâches de dépannage",
     *  output="KI\ClubinfoBundle\Entity\Fix",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getFixesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une tâche de dépannage",
     *  output="KI\ClubinfoBundle\Entity\Fix",
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
    public function getFixAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée une tâche de dépannage",
     *  input="KI\PublicationBundle\Form\FixType",
     *  output="KI\ClubinfoBundle\Entity\Fix",
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
    public function postFixAction()
    {
        $return = $this->partialPost($this->get('security.context')->isGranted('ROLE_USER'));

        if ($return['code'] == 201) {
            // On modifie légèrement la ressource qui vient d'être créée
            $user = $this->get('security.context')->getToken()->getUser();
            $return['item']->setUser($user);
            $return['item']->setDate(time());
            $return['item']->setStatus('Non vu');

            if ($return['item']->getFix()) {
                $dispatcher = $this->container->get('event_dispatcher');
                $achievementCheck = new AchievementCheckEvent(Achievement::BUG_CONTACT);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);
            } else {
                $dispatcher = $this->container->get('event_dispatcher');
                $achievementCheck = new AchievementCheckEvent(Achievement::BUG_REPORT);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);
            }
        }

        if ($return['item']->getProblem() != '[Test] J\'arrive pas à avoir Internet') {
            $fields = array(
                'channel' => $return['item']->getFix() ? '#depannage' : '#upont-feedback',
                'username' => $user->getFirstname().' '.$user->getLastname(),
                'icon_url' => 'https://upont.enpc.fr/api/'.$user->getImage()->getWebPath(),
                'text' => '"'.$return['item']->getProblem().'"'
            );

            $this->get('ki_upont.curl')->curl(
                'https://hooks.slack.com/services/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',
                array(
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($fields)
                )
            );
        }

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une tâche de dépannage",
     *  input="KI\PublicationBundle\Form\FixType",
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
    public function patchFixAction($slug)
    {
        $fix = $this->findBySlug($slug);

        if ($fix->getFix()) {
            $this->notify(
                'notif_fixes',
                'Demande de dépannage',
                'Ta demande de dépannage a été actualisée par le KI !',
                'to',
                array($fix->getUser())
            );
        }
        return $this->patch($slug);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une tâche de dépannage",
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
    public function deleteFixAction($slug) { return $this->delete($slug); }
}
