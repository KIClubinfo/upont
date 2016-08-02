<?php

namespace KI\UserBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdmissiblesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Admissible', 'User');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les admissibles",
     *  output="KI\UserBundle\Entity\Admissible",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/admissibles")
     * @Method("GET")
     */
    public function getAdmissiblesAction()
    {
        // On charge tous les admissibles
        $admissibles = $this->repository->getCurrentYearAdmissibles();

        return $this->json($admissibles);
    }

    /**
     * @ApiDoc(
     *  description="Retourne un admissible",
     *  output="KI\UserBundle\Entity\Admissible",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/admissibles/{slug}")
     * @Method("GET")
     */
    public function getAdmissibleAction($slug)
    {
        $admissible = $this->getOne($slug);

        return $this->json($admissible);
    }

    /**
     * @ApiDoc(
     *  description="Crée un admissible",
     *  input="KI\UserBundle\Form\AdmissibleType",
     *  output="KI\UserBundle\Entity\Admissible",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/admissibles")
     * @Method("POST")
     */
    public function postAdmissibleAction()
    {
        $data = $this->post(true);

        if ($data['code'] == 201) {
            $this->get('ki_user.listener.admissible')->postPersist($data['item']);
        }

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un admissible",
     *  input="KI\UserBundle\Form\AdmissibleType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/admissibles/{slug}")
     * @Method("PATCH")
     */
    public function patchAdmissibleAction($slug)
    {
        $data = $this->patch($slug);

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un admissible",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/admissibles/{slug}")
     * @Method("DELETE")
     */
    public function deleteAdmissibleAction($slug)
    {
        $this->delete($slug);

        return $this->json(null, 204);
    }
}
