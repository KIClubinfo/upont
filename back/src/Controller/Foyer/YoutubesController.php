<?php

namespace App\Controller\Foyer;

use App\Controller\ResourceController;
use App\Entity\Youtube;
use App\Form\YoutubeType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class YoutubesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Youtube::class, YoutubeType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les liens Youtube",
     *  output="App\Entity\Youtube",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes", methods={"GET"})
     */
    public function getYoutubesAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un lien Youtube",
     *  output="App\Entity\Youtube",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes/{slug}", methods={"GET"})
     */
    public function getYoutubeAction($slug)
    {
        $youtube = $this->getOne($slug);

        return $this->json($youtube);
    }

    /**
     * @ApiDoc(
     *  description="Crée un lien Youtube",
     *  input="App\Form\YoutubeType",
     *  output="App\Entity\Youtube",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes", methods={"POST"})
     */
    public function postYoutubeAction()
    {
        $data = $this->post($this->is('USER'));

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un lien Youtube",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes/{slug}", methods={"DELETE"})
     */
    public function deleteYoutubeAction($slug)
    {
        $author = $this->findBySlug($slug)->getUser();
        $this->delete($slug, $this->user == $author);

        return $this->json(null, 204);
    }
}
