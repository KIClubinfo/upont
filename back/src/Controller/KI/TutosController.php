<?php

namespace App\Controller\KI;

use App\Controller\ResourceController;
use App\Entity\Tuto;
use App\Form\TutoType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TutosController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Tuto::class, TutoType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les tutos",
     *  output="App\Entity\Tuto",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos", methods={"GET"})
     */
    public function getTutosAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un tuto",
     *  output="App\Entity\Tuto",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos/{slug}", methods={"GET"})
     */
    public function getTutoAction($slug)
    {
        $tuto = $this->getOne($slug);

        return $this->json($tuto);
    }

    /**
     * @ApiDoc(
     *  description="Crée un tuto",
     *  input="App\Form\TutoType",
     *  output="App\Entity\Tuto",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos", methods={"POST"})
     */
    public function postTutoAction()
    {
        $data = $this->post();

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un tuto",
     *  input="App\Form\TutoType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos/{slug}", methods={"PATCH"})
     */
    public function patchTutoAction($slug)
    {
        $data = $this->patch($slug);

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un tuto",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/tutos/{slug}", methods={"DELETE"})
     */
    public function deleteTutoAction($slug)
    {
        $this->delete($slug);

        return $this->json(null, 204);
    }
}
