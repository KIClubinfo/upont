<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Comment::class, CommentType::class);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Retourne les commentaires",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/comments/{slug}", methods={"GET"})
     */
    public function getCommentAction($slug)
    {
        $comment = $this->getOne($slug);

        return $this->json($comment);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Retourne les commentaires",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/{object}/{slug}/comments", methods={"GET"})
     */
    public function getCommentsAction($object, $slug)
    {
        $this->trust($this->is('USER'));
        $this->autoInitialize($object);
        $item = $this->findBySlug($slug);
        return $this->json($item->getComments());
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Retourne les commentaires d'une sous ressource",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/{object}/{slug}/{subobject}/{subslug}/comments", methods={"GET"})
     */
    public function getCommentsSubAction($object, $slug, $subobject, $subslug)
    {
        $this->trust($this->is('USER'));
        $this->autoInitialize($subobject);
        $item = $this->findBySlug($subslug);
        return $this->json($item->getComments());
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Ajoute un commentaire",
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/{object}/{slug}/comments", methods={"POST"})
     */
    public function postCommentAction($object, $slug)
    {
        $data = $this->post($this->is('USER') && !$this->is('EXTERIEUR'));

        if ($data['code'] == 201) {
            $this->autoInitialize($object);
            $item = $this->findBySlug($slug);
            $item->addComment($data['item']);
        }
        $this->initialize(Comment::class, CommentType::class);
        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Ajoute un commentaire à une sous ressource",
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/{object}/{slug}/{subobject}/{subslug}/comments", methods={"POST"})
     */
    public function postCommentSubAction($object, $slug, $subobject, $subslug)
    {
        $return = $this->partialPost();

        if ($return['code'] == 201) {
            $this->autoInitialize($subobject);
            $item = $this->findBySlug($subslug);
            $item->addComment($return['item']);
        }
        $this->initialize(Comment::class, CommentType::class);
        return $this->postView($return);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Modifie un commentaire",
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/comments/{id}", methods={"PATCH"})
     */
    public function patchCommentAction($id)
    {
        $comment = $this->findBySlug($id);

        $data = $this->patch($id, !$this->is('ADMIN') && $this->user != $comment->getAuthor());

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Supprime un commentaire",
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette View"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/comments/{id}", methods={"DELETE"})
     */
    public function deleteCommentAction($id)
    {
        $comment = $this->findBySlug($id);
        $this->delete($id, !$this->is('ADMIN') && $this->user != $comment->getAuthor());

        return $this->json(null, 204);
    }
}
