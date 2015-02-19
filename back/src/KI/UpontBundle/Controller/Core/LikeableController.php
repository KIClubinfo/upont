<?php

namespace KI\UpontBundle\Controller\Core;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use KI\UpontBundle\Entity\Comment;

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
        case 'clubs'    : $this->initialize('Club', 'Users');            break;
        case 'newsitems': $this->initialize('Newsitem', 'Publications'); break;
        case 'events'   : $this->initialize('Event', 'Publications');    break;
        case 'courses'  : $this->initialize('Course', 'Publications');   break;
        case 'movies'   : $this->initialize('Movie', 'Ponthub');         break;
        case 'series'   : $this->initialize('Serie', 'Ponthub');         break;
        case 'albums'   : $this->initialize('Album', 'Ponthub');         break;
        case 'games'    : $this->initialize('Game', 'Ponthub');          break;
        case 'softwares': $this->initialize('Software', 'Ponthub');      break;
        case 'others'   : $this->initialize('Other', 'Ponthub');         break;

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
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function likeAction($object, $slug)
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
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function dislikeAction($object, $slug)
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
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteLikeAction($object, $slug)
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
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteDislikeAction($object, $slug)
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
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function getCommentsAction($object, $slug)
    {
        $this->autoInitialize($object);
        $item = $this->findBySlug($slug);
        return $this->restResponse($item->getComments());
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un commentaire",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function postCommentAction($object, $slug)
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

        return RestView::create($comment,
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
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function patchCommentAction($id)
    {
        // L'id doit être entier
        if (!($id > 0))
            return $this->jsonResponse(null, 404);

        $request = $this->getRequest()->request;
        if (!$request->has('text'))
            return $this->jsonResponse('Texte de commentaire non précisé', 400);
        if ($request->get('text') == '')
            return $this->jsonResponse('Texte de commentaire non précisé', 400);

        $this->initialize('Comment');
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
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Likeable"
     * )
     */
    public function deleteCommentAction($id)
    {
        // L'id doit être entier
        if (!($id > 0))
            return $this->jsonResponse(null, 404);

        $this->initialize('Comment');
        $comment = $this->findBySlug($id);
        $item->removeComment($comment);
        $this->em->flush();
        return $this->jsonResponse(null, 204);
    }
}
