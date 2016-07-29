<?php

namespace KI\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommentsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Comment', 'Core');
    }

    /**
     * @ApiDoc(
     *  description="Retourne les commentaires",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function getCommentAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Retourne les commentaires",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     * @Route\Get("/{object}/{slug}/comments")
     */
    public function getCommentsAction($object, $slug)
    {
        $this->trust($this->is('USER'));
        $this->autoInitialize($object);
        $item = $this->findBySlug($slug);
        return $this->restResponse($item->getComments());
    }

    /**
     * @ApiDoc(
     *  description="Retourne les commentaires d'une sous ressource",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     * @Route\Get("/{object}/{slug}/{subobject}/{subslug}/comments")
     */
    public function getCommentsSubAction($object, $slug, $subobject, $subslug)
    {
        $this->trust($this->is('USER'));
        $this->autoInitialize($subobject);
        $item = $this->findBySlug($subslug);
        return $this->restResponse($item->getComments());
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un commentaire",
     *  requirements={
     *   {
     *    "name"="text",
     *    "dataType"="string",
     *    "description"="Le commentaire"
     *   }
     *  },
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     * @Route\Post("/{object}/{slug}/comments")
     */
    public function postCommentAction($object, $slug)
    {
        $return = $this->postData($this->is('USER') && !$this->is('EXTERIEUR'));

        if ($return['code'] == 201) {
            $this->autoInitialize($object);
            $item = $this->findBySlug($slug);
            $item->addComment($return['item']);
        }
        $this->initialize('Comment', 'Core');
        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un commentaire à une sous ressource",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     * @Route\Post("/{object}/{slug}/{subobject}/{subslug}/comments")
     */
    public function postCommentSubAction($object, $slug, $subobject, $subslug)
    {
        $return = $this->partialPost();

        if ($return['code'] == 201) {
            $this->autoInitialize($subobject);
            $item = $this->findBySlug($subslug);
            $item->addComment($return['item']);
        }
        $this->initialize('Comment', 'Core');
        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un commentaire",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     * @Route\Patch("/comments/{id}")
     */
    public function patchCommentAction($id)
    {
        $comment = $this->findBySlug($id);
        return $this->patch($id, !$this->is('ADMIN') && $this->user != $comment->getAuthor());
    }

    /**
     * @ApiDoc(
     *  description="Supprime un commentaire",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteCommentAction($id)
    {
        $comment = $this->findBySlug($id);
        return $this->delete($id, !$this->is('ADMIN') && $this->user != $comment->getAuthor());
    }
}
