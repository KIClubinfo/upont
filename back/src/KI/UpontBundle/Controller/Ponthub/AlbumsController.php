<?php

namespace KI\UpontBundle\Controller\Ponthub;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AlbumsController extends \KI\UpontBundle\Controller\Core\SubresourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Album', 'Ponthub');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les albums de musique",
     *  output="KI\UpontBundle\Entity\Ponthub\Album",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function getAlbumsAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un album de musique",
     *  output="KI\UpontBundle\Entity\Ponthub\Album",
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
    public function getAlbumAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Modifie un album de musique",
     *  input="KI\UpontBundle\Form\Ponthub\AlbumType",
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
    public function patchAlbumAction($slug)
    {
        return $this->patch($slug, $this->get('security.context')->isGranted('ROLE_PONTHUB'));
    }

    /**
     * @ApiDoc(
     *  description="Liste les musiques associées",
     *  output="KI\UpontBundle\Entity\Ponthub\Music",
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
    public function getAlbumMusicsAction($slug) { return $this->getAllSub($slug, 'Music'); }

    /**
     * @ApiDoc(
     *  description="Retourne une musique associée",
     *  output="KI\UpontBundle\Entity\Ponthub\Music",
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
    public function getAlbumMusicAction($slug, $id) { return $this->getOneSub($slug, 'Music', $id); }

    /**
     * @ApiDoc(
     *  description="Modifie une musique associée",
     *  input="KI\UpontBundle\Form\Ponthub\MusicType",
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
    public function patchAlbumMusicAction($slug, $id)
    {
        return $this->patchSub($slug, 'Music', $id, $this->get('security.context')->isGranted('ROLE_PONTHUB'));
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
     * @Route\Get("/albums/{slug}/musics/{id}/download")
     */
    public function downloadMusicAction($slug, $id)
    {
        $item = $this->getOneSub($slug, 'Music', $id);
        $user = $this->container->get('security.context')->getToken()->getUser();

        // Si l'utilisateur n'a pas déjà téléchargé ce fichier on le rajoute
        if (!$item->getUsers()->contains($user))
            $item->addUser($user);

        $this->em->flush();

        return $this->redirect($item->fileUrl());
    }
}
