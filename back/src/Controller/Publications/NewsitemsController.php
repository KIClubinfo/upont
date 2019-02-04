<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Newsitem;
use App\Form\NewsitemType;
use App\Listener\NewsitemListener;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsitemsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Newsitem::class, NewsitemType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les news",
     *  output="App\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems", methods={"GET"})
     */
    public function getNewsitemsAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne une news",
     *  output="App\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems/{slug}", methods={"GET"})
     */
    public function getNewsitemAction($slug)
    {
        $newsitem = $this->getOne($slug, $this->is('EXTERIEUR'));

        return $this->json($newsitem);
    }

    /**
     * @ApiDoc(
     *  description="Crée une news",
     *  input="App\Form\NewsitemType",
     *  output="App\Entity\Newsitem",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems", methods={"POST"})
     */
    public function postNewsitemAction(NewsitemListener $newsitemListener)
    {
        $data = $this->post($this->isClubMember());

        if ($data['code'] == 201) {
            $newsitemListener->postPersist($data['item']);
        }

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une news",
     *  input="App\Form\NewsitemType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems/{slug}", methods={"PATCH"})
     */
    public function patchNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        $data = $this->patch($slug, $this->isClubMember($club));

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une news",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems/{slug}", methods={"DELETE"})
     */
    public function deleteNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;

        $this->delete($slug, $this->isClubMember($club));

        return $this->json(null, 204);
    }
}
