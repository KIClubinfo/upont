<?php

namespace App\Controller\Ponthub;

use App\Entity\Other;
use App\Form\OtherType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OthersController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Other::class, OtherType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les fichiers autres",
     *  output="App\Entity\Other",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/others", methods={"GET"})
     */
    public function getOthersAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un fichier autre",
     *  output="App\Entity\Other",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/others/{slug}", methods={"GET"})
     */
    public function getOtherAction($slug)
    {
        $other = $this->getOne($slug);

        return $this->json($other);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un fichier autre",
     *  input="App\Form\OtherType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/others/{slug}", methods={"PATCH"})
     */
    public function patchOtherAction($slug)
    {
        $data = $this->patch($slug, $this->is('JARDINIER'));

        return $this->formJson($data);
    }


    /**
     * @ApiDoc(
     *  description="Supprime un autre",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/others/{slug}", methods={"DELETE"})
     */
    public function deleteOtherAction($slug)
    {
        $this->delete($slug, $this->is('JARDINIER'));

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Télécharge un fichier sur Ponthub, et log le téléchargement",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/others/{slug}/download", methods={"GET"})
     */
    public function downloadOtherAction($slug)
    {
        $item =  $this->getOne($slug, !$this->is('EXTERIEUR'));
        return $this->download($item);
    }
}
