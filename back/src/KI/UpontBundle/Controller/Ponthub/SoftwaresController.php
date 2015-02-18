<?php

namespace KI\UpontBundle\Controller\Ponthub;

use FOS\RestBundle\Controller\Annotations as Route;
use KI\UpontBundle\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SoftwaresController extends BaseController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Software', 'Ponthub');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les logiciels",
     *  output="KI\UpontBundle\Entity\Ponthub\Software",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function getSoftwaresAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un logiciel",
     *  output="KI\UpontBundle\Entity\Ponthub\Software",
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
    public function getSoftwareAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Modifie un jeu",
     *  input="KI\UpontBundle\Form\Ponthub\SoftwareType",
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
    public function patchSoftwareAction($slug)
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
     * @Route\Get("/softwares/{slug}/download")
     */
    public function downloadSoftwareAction($slug)
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
     * @Route\Get("/softwares/{slug}/like")
     */
    public function getLikeSoftwareAction($slug) { return $this->getLikes($slug); }

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
     * @Route\Get("/softwares/{slug}/dislike")
     */
    public function getDislikeSoftwareAction($slug) { return $this->getDislikes($slug); }

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
     * @Route\Post("/softwares/{slug}/like")
     */
    public function likeSoftwareAction($slug) { return $this->like($slug); }

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
     * @Route\Post("/softwares/{slug}/dislike")
     */
    public function dislikeSoftwareAction($slug) { return $this->dislike($slug); }

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
     * @Route\Delete("/softwares/{slug}/like")
     */
    public function deleteLikeSoftwareAction($slug) { return $this->deleteLike($slug); }

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
     * @Route\Delete("/softwares/{slug}/dislike")
     */
    public function deleteDislikeSoftwareAction($slug) { return $this->deleteDislike($slug); }
}
