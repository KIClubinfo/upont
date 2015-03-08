<?php

namespace KI\UpontBundle\Controller\Core;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use KI\UpontBundle\Entity\Core\Comment;

// Fonctions de like/dislike/commentaire
class LikeableController extends \KI\UpontBundle\Controller\Core\BaseController
{
    // Précise si une classe est likeable ou non
    protected function isLikeable($item)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException('Accès refusé');

        if (!is_a($item, 'Likeable'))
            return;
    }

    // Sert à initialiser le controleur avec la bonne classe quand il est appelé
    // par les routes génériques de like
    // i.e on veut l'initialiser par exemple avec la classe Newsitems si la route
    // est /newsitems/{slug}/like
    protected function autoInitialize($object)
    {
        switch ($object) {
        case 'clubs'    : $this->initialize('Club', 'Users'); break;
        case 'newsitems': $this->initialize('Newsitem', 'Publications'); break;
        case 'events'   : $this->initialize('Event', 'Publications'); break;
        case 'courses'  : $this->initialize('Course', 'Publications'); break;
        case 'exercices': $this->initialize('Exercice', 'Publications'); break;
        case 'movies'   : $this->initialize('Movie', 'Ponthub'); break;
        case 'series'   : $this->initialize('Serie', 'Ponthub'); break;
        case 'episodes' : $this->initialize('Episode', 'Ponthub'); break;
        case 'albums'   : $this->initialize('Album', 'Ponthub'); break;
        case 'musics'   : $this->initialize('Music', 'Ponthub'); break;
        case 'games'    : $this->initialize('Game', 'Ponthub'); break;
        case 'softwares': $this->initialize('Software', 'Ponthub'); break;
        case 'others'   : $this->initialize('Other', 'Ponthub'); break;
        case 'comments' : $this->initialize('Comment', 'Core'); break;

        default: return;
        }
    }

    protected function retrieveLikes($item)
    {
        $this->isLikeable($item);

        // Si l'entité a un système de like/dislike, précise si l'user actuel (un)like
        $item->setLike($item->getLikes()->contains($this->user));
        $item->setDislike($item->getDislikes()->contains($this->user));

        if (is_a($item, 'Event')) {
            $item->setAttend($item->getAttendees()->contains($this->user));
            $item->setPookie($item->getPookies()->contains($this->user));
        }
        return $item;
    }

    // Fonctions relatives aux likes/dislikes/commentaires
    protected function like($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur n'a pas déjà liké cet objet on le rajoute
        if (!$item->getLikes()->contains($this->user))
            $item->addLike($this->user);
        // Si l'utilisateur avait précédemment unliké, on l'enlève
        if ($item->getDislikes()->contains($this->user))
            $item->removeDislike($this->user);

        $this->em->flush();
    }

    protected function dislike($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur n'a pas déjà unliké cet objet on le rajoute
        if (!$item->getDislikes()->contains($this->user))
            $item->addDislike($this->user);
        // Si l'utilisateur avait précédemment liké, on l'enlève
        if ($item->getLikes()->contains($this->user))
            $item->removeLike($this->user);

        $this->em->flush();
    }

    protected function deleteLike($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur a déjà unliké on l'enlève
        if ($item->getLikes()->contains($this->user))
            $item->removeLike($this->user);
        $this->em->flush();
    }

    protected function deleteDislike($item)
    {
        $this->isLikeable($item);

        // Si l'utilisateur a déjà unliké on l'enlève
        if ($item->getDislikes()->contains($this->user))
            $item->removeDislike($this->user);
        $this->em->flush();
    }

    /**
     * @ApiDoc(
     *  description="Like",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function likeView($object, $slug)
    {
        $this->autoInitialize($object);
        $this->like($this->findBySlug($slug));
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Dislike",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function dislikeView($object, $slug)
    {
        $this->autoInitialize($object);
        $this->dislike($this->findBySlug($slug));
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son like",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteLikeView($object, $slug)
    {
        $this->autoInitialize($object);
        $this->deleteLike($this->findBySlug($slug));
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son dislike",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteDislikeView($object, $slug)
    {
        $this->autoInitialize($object);
        $this->deleteDislike($this->findBySlug($slug));
        return $this->jsonResponse(null, 204);
    }













    // Commentaires

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
    public function getCommentsView($object, $slug)
    {
        $this->autoInitialize($object);
        $item = $this->findBySlug($slug);
        $comments = $item->getComments();

        foreach ($comments as &$comment) {
            $comment = $this->retrieveLikes($comment);
        }

        return $this->restResponse($comments);
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
     */
    public function postCommentView($object, $slug)
    {
        $this->autoInitialize($object);
        $item = $this->findBySlug($slug);
        return $this->postComment($item);
    }

    protected function postComment($item)
    {
        $request = $this->getRequest()->request;

        if (!$request->has('text'))
            return $this->jsonResponse('Texte de commentaire non précisé', 400);
        if ($request->get('text') == '')
            return $this->jsonResponse('Texte de commentaire non précisé', 400);

        $comment = new Comment();
        $comment->setDate(time());
        $comment->setText($request->get('text'));
        $comment->setAuthor($this->user);
        $this->em->persist($comment);

        $item->addComment($comment);
        $this->em->flush();

        return $this->restResponse($comment,
            201,
            array(
                'Location' => $this->generateUrl(
                    'upont_api_patch_comment',
                    array('id' => $comment->getId()),
                    true
                )
            )
        );
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
     */
    public function patchCommentView($id)
    {
        // L'id doit être entier
        if (!($id > 0))
            return $this->jsonResponse(null, 404);

        $request = $this->getRequest()->request;
        if (!$request->has('text'))
            return $this->jsonResponse('Texte de commentaire non précisé', 400);
        if ($request->get('text') == '')
            return $this->jsonResponse('Texte de commentaire non précisé', 400);

        $this->initialize('Comment', 'Core');
        $comment = $this->findBySlug($id);
        $comment->setText($request->get('text'));
        $this->em->flush();
        return $this->jsonResponse(null, 204);
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
    public function deleteCommentView($id)
    {
        // L'id doit être entier
        if (!($id > 0))
            return $this->jsonResponse(null, 404);

        $this->initialize('Comment', 'Core');
        $comment = $this->findBySlug($id);
        $this->em->remove($comment);
        $this->em->flush();
        return $this->jsonResponse(null, 204);
    }
















    // Même chose pour les sous ressources

    /**
     * @ApiDoc(
     *  description="Like une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function likeSubView($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($object);
        $this->like($this->findBySlug($slug));
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Dislike une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function dislikeSubView($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($object);
        $this->findBySlug($slug);
        $this->autoInitialize($subobject);
        $this->dislike($this->findBySlug($subslug));
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son like d'une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteLikeSubView($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($object);
        $this->findBySlug($slug);
        $this->autoInitialize($subobject);
        $this->deleteLike($this->findBySlug($subslug));
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Enlève son dislike d'une sous ressource",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette View",
     *   403="Pas les droits suffisants pour effectuer cette View",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteDislikeSubView($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($object);
        $this->findBySlug($slug);
        $this->autoInitialize($subobject);
        $this->deleteDislike($this->findBySlug($subslug));
        return $this->jsonResponse(null, 204);
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
     */
    public function getCommentsSubView($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($object);
        $this->findBySlug($slug);
        $this->autoInitialize($subobject);
        $item = $this->findBySlug($subslug);
        return $this->restResponse($item->getComments());
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
     */
    public function postCommentSubView($object, $slug, $subobject, $subslug)
    {
        $this->autoInitialize($object);
        $this->findBySlug($slug);
        $this->autoInitialize($subobject);
        $item = $this->findBySlug($subslug);
        return $this->postComment($item);
    }
}
