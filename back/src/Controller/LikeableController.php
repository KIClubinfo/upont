<?php

namespace App\Controller;

use App\Entity\Likeable;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
            return;
        }
    }

    /**
     *  Sert à initialiser le controleur avec la bonne classe quand il est appelé
     *  par les routes génériques de like. Par exemple on veut l'initialiser
     *  avec la classe Newsitems si la route est /newsitems/{slug}/like
     *  @param  string $object Le type d'objet à initialiser
     *  @throws Exception Si l'objet ne correspond à aucun entité likeable connue
     */
    protected function autoInitialize($object)
    {
        $likeables = $this->getParameter('likeables');
        $className = ucfirst(preg_replace('/s$/', '', $object));

        foreach ($likeables as $class) {
            if ($class === $className) {
                return $this->initialize('App\\Entity\\' . $class, 'App\\Form\\'  . $class . 'Type');
            }
        }
        throw new \Exception('Initialisation impossible du controleur');
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
     * @ApiDoc(
     *  description="Like",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/like")
     * @Method("POST")
     */
    public function likeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->like($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Dislike",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/dislike")
     * @Method("POST")
     */
    public function dislikeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->dislike($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son like",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/like")
     * @Method("DELETE")
     */
    public function deleteLikeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->deleteLike($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son dislike",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/dislike")
     * @Method("DELETE")
     */
    public function deleteDislikeAction($object, $slug)
    {
        $this->autoInitialize($object);
        $this->deleteDislike($this->findBySlug($slug));
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Like une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/{subobject}/{subslug}/like")
     * @Method("POST")
     */
    public function likeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->like($this->findBySlug($subslug));
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Dislike une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/{subobject}/{subslug}/dislike")
     * @Method("POST")
     */
    public function dislikeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->dislike($this->findBySlug($subslug));
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son like d'une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/{subobject}/{subslug}/like")
     * @Method("DELETE")
     */
    public function deleteLikeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->deleteLike($this->findBySlug($subslug));
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son dislike d'une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *  },
     *  section="Likeable"
     * )
     * @Route("/{object}/{slug}/{subobject}/{subslug}/dislike")
     * @Method("DELETE")
     */
    public function deleteDislikeSubAction($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($subobject);
        $this->deleteDislike($this->findBySlug($subslug));
        return $this->json(null, 204);
    }
}
