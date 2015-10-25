<?php

namespace KI\PublicationBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\CoreBundle\Controller\ResourceController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MessagesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Message', 'Publication');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les messages",
     *  output="KI\PublicationBundle\Entity\Message",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getMessagesAction()
    {
        return $this->getAll($this->is('EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Retourne un message",
     *  output="KI\PublicationBundle\Entity\Message",
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
    public function getMessageAction($slug)
    {
        return $this->getOne($slug, $this->is('EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Crée une message",
     *  input="KI\PublicationBundle\Form\MessageType",
     *  output="KI\PublicationBundle\Entity\Message",
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
    public function postMessageAction()
    {
        return $this->post();
    }

    /**
     * @ApiDoc(
     *  description="Modifie un message",
     *  input="KI\PublicationBundle\Form\MessageType",
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
    public function patchMessageAction($slug)
    {
        // TODO auth
        return $this->patch($slug);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un message",
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
    public function deleteMessageAction($slug)
    {
        return $this->delete($slug);
    }
}
