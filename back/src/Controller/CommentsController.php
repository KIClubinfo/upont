<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommentsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Comment::class, CommentType::class);
    }

    /**
     * @ApiDoc(
     *  description="Retourne les commentaires",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/comments/{slug}")
     * @Method("GET")
     */
    public function getCommentAction($slug)
    {
        $comment = $this->getOne($slug);

        return $this->json($comment);
    }

    /**
     * @ApiDoc(
     *  description="Retourne les commentaires",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/comments")
     * @Method("GET")
     */
    public function getCommentsAction($object, $slug)
    {
        $this->trust($this->is('USER'));
        $this->autoInitialize($object);
        $item = $this->findBySlug($slug);
        return $this->json($item->getComments());
    }

    /**
     * @ApiDoc(
     *  description="Retourne les commentaires d'une sous ressource",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/{subobject}/{subslug}/comments")
     * @Method("GET")
     */
    public function getCommentsSubAction($object, $slug, $subobject, $subslug)
    {
        $this->trust($this->is('USER'));
        $this->autoInitialize($subobject);
        $item = $this->findBySlug($subslug);
        return $this->json($item->getComments());
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
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/comments")
     * @Method("POST")
     */
    public function postCommentAction($object, $slug)
    {
        $data = $this->post($this->is('USER') && !$this->is('EXTERIEUR'));

        if ($data['code'] == 201) {
            $this->autoInitialize($object);
            $item = $this->findBySlug($slug);
            $item->addComment($data['item']);
        }
        $this->initialize('Comment', 'Core');
        return $this->formJson($data);
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
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/{subobject}/{subslug}/comments")
     * @Method("POST")
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
     *  },
     *  section="Likeable"
     * )
     * @Route("/comments/{id}")
     * @Method("PATCH")
     */
    public function patchCommentAction($id)
    {
        $comment = $this->findBySlug($id);

        $data = $this->patch($id, !$this->is('ADMIN') && $this->user != $comment->getAuthor());

        return $this->formJson($data);
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
     *  },
     *  section="Likeable"
     * )
     * @Route("/comments/{id}")
     * @Method("DELETE")
     */
    public function deleteCommentAction($id)
    {
        $comment = $this->findBySlug($id);
        $this->delete($id, !$this->is('ADMIN') && $this->user != $comment->getAuthor());

        return $this->json(null, 204);
    }
}
