<?php

namespace KI\ClubinfoBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FixsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
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
     *  section="Clubinfo"
     * )
     * @Route("/fixs")
     * @Method("GET")
     */
    public function getFixsAction()
    {
        return $this->getAll();
    }

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
     *  section="Clubinfo"
     * )
     * @Route("/fixs/{slug}")
     * @Method("GET")
     */
    public function getFixAction($slug)
    {
        $fix = $this->getOne($slug);

        return $this->json($fix);
    }

    /**
     * @ApiDoc(
     *  description="Crée une tâche de dépannage",
     *  input="KI\ClubinfoBundle\Form\FixType",
     *  output="KI\ClubinfoBundle\Entity\Fix",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/fixs")
     * @Method("POST")
     */
    public function postFixAction()
    {
        $data = $this->post($this->is('USER'));

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une tâche de dépannage",
     *  input="KI\ClubinfoBundle\Form\FixType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/fixs/{slug}")
     * @Method("PATCH")
     */
    public function patchFixAction($slug)
    {
        $data = $this->patch($slug, $this->isClubMember('ki'));

        $fix = $data['item'];

        if ($fix->getFix()) {
            $this->get('ki_user.service.notify')->notify(
                'notif_fixs',
                'Demande de dépannage',
                'Ta demande de dépannage a été actualisée par le KI !',
                'to',
                [$fix->getUser()]
            );
        }

        return $this->formJson($data);
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
     *  section="Clubinfo"
     * )
     * @Route("/fixs/{slug}")
     * @Method("DELETE")
     */
    public function deleteFixAction($slug)
    {
        $fix = $this->getOne($slug);
        $this->delete($slug,
            $this->user->getUsername() == $fix->getUser()->getUsername() || $this->isClubMember('ki')
        );

        return $this->json(null, 204);
    }
}
