<?php

namespace KI\UpontBundle\Controller\Users;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AdmissiblesController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Admissible', 'Users');
    }



    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les admissibles",
     *  output="KI\UpontBundle\Entity\Users\Admissible",
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
        $admissibles = $this->repo->findAll();
        $result = array();

        // On ne garde que les admissibles de cette année
        foreach ($admissibles as $admissible) {
            if ($admissible->getDate() == date('Y')) {
                $result[] = $admissible;
            }
        }
        return $result;
    }

    /**
     * @ApiDoc(
     *  description="Retourne un admissible",
     *  output="KI\UpontBundle\Entity\Users\Admissible",
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
     *  input="KI\UpontBundle\Form\Users\AdmissibleType",
     *  output="KI\UpontBundle\Entity\Users\Admissible",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function postAdmissibleAction()
    {
        $return = $this->partialPost(true);

        if ($return['code'] == 201) {
            // On modifie légèrement la ressource qui vient d'être créée
            $return['item']->setDate(time());
        }

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un admissible",
     *  input="KI\UpontBundle\Form\Users\AdmissibleType",
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
    public function patchAdmissibleAction($slug)
    {
        return $this->patch($slug);
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
     */
    public function deleteAdmissibleAction($slug) { return $this->delete($slug); }
}
