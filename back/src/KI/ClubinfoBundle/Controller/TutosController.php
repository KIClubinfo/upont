<?php

namespace KI\ClubinfoBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TutosController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Tuto', 'Clubinfo');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les tutos",
     *  output="KI\ClubinfoBundle\Entity\Tuto",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos")
     * @Method("GET")
     */
    public function getTutosAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un tuto",
     *  output="KI\ClubinfoBundle\Entity\Tuto",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos/{slug}")
     * @Method("GET")
     */
    public function getTutoAction($slug)
    {
        $tuto = $this->getOne($slug);

        return $this->json($tuto);
    }

    /**
     * @ApiDoc(
     *  description="Crée un tuto",
     *  input="KI\ClubinfoBundle\Form\TutoType",
     *  output="KI\ClubinfoBundle\Entity\Tuto",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos")
     * @Method("POST")
     */
    public function postTutoAction()
    {
        $data = $this->post();

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un tuto",
     *  input="KI\ClubinfoBundle\Form\TutoType",
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
     * @Route("/tutos/{slug}")
     * @Method("PATCH")
     */
    public function patchTutoAction($slug)
    {
        $data = $this->patch($slug);

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un tuto",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos/{slug}")
     * @Method("DELETE")
     */
    public function deleteTutoAction($slug)
    {
        $this->delete($slug);

        return $this->json(null, 204);
    }
}
