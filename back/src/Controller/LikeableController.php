<?php

namespace App\Controller;

use App\Entity\Likeable;
use App\Entity\LikeClass;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Fonctions de like/dislike/commentaire
class LikeableController extends BaseController
{
    /**
     * Précise si une classe peut être likée par l'utilisateur actuel
     * @param mixed $item L'item à tester
     * @return boolean
     */
    protected function isLikeable($item)
    {
        if (!$this->is('USER') || $this->is('ADMISSIBLE') || $this->is('EXTERIEUR')) {
            throw new AccessDeniedException('Accès refusé');
        }

        if (!$item instanceof Likeable) {
            return false;
        }

        return true;
    }

    /**
     *  Sert à initialiser le controleur avec la bonne classe quand il est appelé
     *  par les routes génériques de like. Par exemple on veut l'initialiser
     *  avec la classe Newsitems si la route est /newsitems/{slug}/like
     * @param  string $object Le type d'objet à initialiser
     * @throws Exception Si l'objet ne correspond à aucun entité likeable connue
     */
    protected function autoInitialize($object)
    {
        $likeables = $this->getParameter('likeables');
        $className = ucfirst(preg_replace('/s$/', '', $object));

        foreach ($likeables as $class) {
            if ($class === $className) {
                return $this->initialize('App\\Entity\\' . $class, 'App\\Form\\' . $class . 'Type');
            }
        }
        throw new Exception('Initialisation impossible du controleur');
    }

    /**
     * Marque un objet likeable comme liké
     * @param LikeClass $item
     */
    protected function like($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur n'a pas déjà liké cet objet on le rajoute
        if (!$item->isLiked($this->user)) {
            $item->addLike($this->user);
            $item->setLike(true);
        }

        // Si l'utilisateur avait précédemment unliké, on l'enlève
        if ($item->isDisliked($this->user)) {
            $item->removeDislike($this->user);
            $item->setDislike(false);
        }
        $this->manager->flush();
    }

    /**
     * Marque un objet likeable comme disliké
     * @param LikeClass $item
     */
    protected function dislike($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur n'a pas déjà unliké cet objet on le rajoute
        if (!$item->isDisliked($this->user)) {
            $item->addDislike($this->user);
            $item->setDislike(true);
        }

        // Si l'utilisateur avait précédemment liké, on l'enlève
        if ($item->isLiked($this->user)) {
            $item->removeLike($this->user);
            $item->setLike(false);
        }
        $this->manager->flush();
    }

    /**
     * Marque un objet likeable comme non liké
     * @param LikeClass $item
     */
    protected function deleteLike($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur a déjà unliké on l'enlève
        if ($item->isLiked($this->user)) {
            $item->removeLike($this->user);
            $item->setLike(false);
        }
        $this->manager->flush();
    }

    /**
     * Marque un objet likeable comme non disliké
     * @param LikeClass $item
     */
    protected function deleteDislike($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur a déjà unliké on l'enlève
        if ($item->isDisliked($this->user)) {
            $item->removeDislike($this->user);
            $item->setDislike(false);
        }
        $this->manager->flush();
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Like",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/like", name="like_object", methods={"POST"})
     */
    public function likeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->like($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Dislike",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/dislike", name="dislike_object", methods={"POST"})
     */
    public function dislikeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->dislike($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Enlève son like",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/like", name="unlike_object", methods={"DELETE"})
     */
    public function deleteLikeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->deleteLike($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Enlève son dislike",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/dislike", name="undislike_object", methods={"DELETE"})
     */
    public function deleteDislikeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->deleteDislike($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Like une sous ressource",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/{subobject}/{subslug}/like", name="like_subobject", methods={"POST"})
     */
    public function likeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->like($this->findBySlug($subslug));
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Dislike une sous ressource",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/{subobject}/{subslug}/dislike", name="dislike_subobject", methods={"POST"})
     */
    public function dislikeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->dislike($this->findBySlug($subslug));
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Enlève son like d'une sous ressource",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/{subobject}/{subslug}/like", name="unlike_subobject", methods={"DELETE"})
     */
    public function deleteLikeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->deleteLike($this->findBySlug($subslug));
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Likeable"},
     *     summary="Enlève son dislike d'une sous ressource",
     *     @SWG\Response(
     *         response="204",
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
     * @Route("/{object}/{slug}/{subobject}/{subslug}/dislike", name="undislike_subobject", methods={"DELETE"})
     */
    public function deleteDislikeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->deleteDislike($this->findBySlug($subslug));
        return $this->json(null, 204);
    }
}
