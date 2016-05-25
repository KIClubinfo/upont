<?php

namespace KI\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use KI\CoreBundle\Controller\ResourceController;

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
     */
    public function getAdmissiblesAction()
    {
        // On charge tous les admissibles
        $admissibles = $this->repository->findAll();
        $result = array();
        $year = strftime('%Y');

        // On ne garde que les admissibles de cette année
        foreach ($admissibles as $admissible) {
            if (strftime('%Y', $admissible->getDate()) == $year) {
                $result[] = $admissible;
            }
        }
        return $result;
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
     */
    public function getAdmissibleAction($slug) { return $this->getOne($slug); }

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
     */
    public function postAdmissibleAction() {
        $return = $this->postData(true);

        if ($return['code'] == 201) {
            $this->get('ki_user.listener.admissible')->postPersist($return['item']);
        }

        return $this->postView($return);
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
     */
    public function patchAdmissibleAction($slug) { return $this->patch($slug); }

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
     */
    public function deleteAdmissibleAction($slug) { return $this->delete($slug); }
}
