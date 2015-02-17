<?php

namespace KI\UpontBundle\Controller\Ponthub;

use KI\UpontBundle\Entity\Ponthub\Movie;
use KI\UpontBundle\Form\Ponthub\MovieType;
use KI\UpontBundle\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;

class MoviesController extends BaseController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Movie', 'Ponthub');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les films",
     *  output="KI\UpontBundle\Entity\Ponthub\Movie",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function getMoviesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un film",
     *  output="KI\UpontBundle\Entity\Ponthub\Movie",
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
    public function getMovieAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Modifie un film",
     *  input="KI\UpontBundle\Form\Ponthub\MovieType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function patchMovieAction($slug)
    {
        return $this->patch($slug, $this->get('security.context')->isGranted('ROLE_PONTHUB'));
    }

    /**
     * @ApiDoc(
     *  description="Télécharge un fichier sur Ponthub, et log le téléchargement",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Get("/movies/{slug}/download")
     */
    public function downloadMovieAction($slug)
    {
        $item = $this->getOne($slug);
        $user = $this->container->get('security.context')->getToken()->getUser();

        // Si l'utilisateur n'a pas déjà téléchargé ce fichier on le rajoute
        if (!$item->getUsers()->contains($user))
            $item->addUser($user);

        $this->em->flush();

        return $this->redirect($item->fileUrl());
    }

    /**
     * @ApiDoc(
     *  description="Retourne la liste des gens qui likent",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Get("/movies/{slug}/like")
     */
    public function getLikeMovieAction($slug) { return $this->getLikes($slug); }

    /**
     * @ApiDoc(
     *  description="Retourne la liste des gens qui dislikent",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Get("/movies/{slug}/dislike")
     */
    public function getDislikeMovieAction($slug) { return $this->getDislikes($slug); }

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
     *  section="Ponthub"
     * )
     * @Post("/movies/{slug}/like")
     */
    public function likeMovieAction($slug) { return $this->like($slug); }

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
     *  section="Ponthub"
     * )
     * @Post("/movies/{slug}/dislike")
     */
    public function dislikeMovieAction($slug) { return $this->dislike($slug); }

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
     *  section="Ponthub"
     * )
     * @Delete("/movies/{slug}/like")
     */
    public function deleteLikeMovieAction($slug) { return $this->deleteLike($slug); }

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
     *  section="Ponthub"
     * )
     * @Delete("/movies/{slug}/dislike")
     */
    public function deleteDislikeMovieAction($slug) { return $this->deleteDislike($slug); }
}
