<?php

namespace KI\UpontBundle\Controller\Ponthub;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RequestsController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Request', 'Ponthub');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les demandes d'ajout de fichier",
     *  output="KI\UpontBundle\Entity\Ponthub\Request",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function getRequestsAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une demande d'ajout de fichier",
     *  output="KI\UpontBundle\Entity\Ponthub\Request",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function getRequestAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée une demande d'ajout de fichier",
     *  input="KI\UpontBundle\Form\Ponthub\RequestType",
     *  output="KI\UpontBundle\Entity\Ponthub\Request",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function postRequestAction()
    {
        $return = $this->partialPost($this->get('security.context')->isGranted('ROLE_USER'));

        // On modifie légèrement la ressource qui vient d'être créée
        $return['item']->setDate(time());
        $return['item']->setUser($this->container->get('security.context')->getToken()->getUser());
        $return['item']->setVotes(1);
        $this->em->flush();

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une demande d'ajout de fichier",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function deleteRequestAction($slug)
    {
        return $this->delete($slug, $this->get('security.context')->isGranted('ROLE_USER'));
    }

    /**
     * @ApiDoc(
     *  description="Approuve une demande d'ajout de fichier",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function upvoteRequestAction($slug)
    {
        $item = $this->findBySlug($slug);
        $item->setVotes($item->getVotes() + 1);
        $this->em->flush();

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Désapprouve une demande d'ajout de fichier",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function downvoteRequestAction($slug)
    {
        $item = $this->findBySlug($slug);
        $item->setVotes($item->getVotes() - 1);
        $this->em->flush();

        return $this->jsonResponse(null, 204);
    }
}
