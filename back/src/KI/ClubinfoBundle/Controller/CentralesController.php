<?php

namespace KI\ClubinfoBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use KI\CoreBundle\Controller\ResourceController;

class CentralesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Centrale', 'Clubinfo');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les centrales d'achat",
     *  output="KI\ClubinfoBundle\Entity\Centrale",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     */
    public function getCentralesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une centrale d'achat",
     *  output="KI\ClubinfoBundle\Entity\Centrale",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Get("/centrales/{slug}")
     */
    public function getCentraleAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée une centrale d'achat",
     *  input="KI\ClubinfoBundle\Form\CentraleType",
     *  output="KI\ClubinfoBundle\Entity\Centrale",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Post("/centrales")
     */
    public function postCentraleAction()
    {
        return $this->post($this->get('security.context')->isGranted('ROLE_USER'));
    }

    /**
     * @ApiDoc(
     *  description="Modifie une centrale d'achat",
     *  input="KI\ClubinfoBundle\Form\CentraleType",
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
     * @Route\Patch("/centrales/{slug}")
     */
    public function patchCentraleAction($slug)
    {
        $fix = $this->findBySlug($slug);

        // if ($fix->getFix()) {
        //     $this->get('ki_user.service.notify')->notify(
        //         'notif_fixs',
        //         'Demande de dépannage',
        //         'Ta demande de dépannage a été actualisée par le KI !',
        //         'to',
        //         array($fix->getUser())
        //     );
        // }
        return $this->patch($slug);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une centrale d'achat",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Delete("/centrales/{slug}")
     */
    public function deleteCentraleAction($slug)
    {
        // $fix = $this->findBySlug($slug);
        // $user = $this->get('security.context')->getToken()->getUser();
        // return $this->delete($slug, $user->getUsername() == $fix->getUser()->getUsername());
    }
}
