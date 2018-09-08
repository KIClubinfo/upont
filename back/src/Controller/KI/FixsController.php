<?php

namespace App\Controller\KI;

use App\Controller\ResourceController;
use App\Entity\Fix;
use App\Form\FixType;
use App\Service\NotifyService;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FixsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Fix::class, FixType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les tâches de dépannage",
     *  output="App\Entity\Fix",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/fixs", methods={"GET"})
     */
    public function getFixsAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne une tâche de dépannage",
     *  output="App\Entity\Fix",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/fixs/{slug}", methods={"GET"})
     */
    public function getFixAction($slug)
    {
        $fix = $this->getOne($slug);

        return $this->json($fix);
    }

    /**
     * @ApiDoc(
     *  description="Crée une tâche de dépannage",
     *  input="App\Form\FixType",
     *  output="App\Entity\Fix",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/fixs", methods={"POST"})
     */
    public function postFixAction()
    {
        $data = $this->post($this->is('USER'));

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une tâche de dépannage",
     *  input="App\Form\FixType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/fixs/{slug}", methods={"PATCH"})
     */
    public function patchFixAction(NotifyService $notifyService, $slug)
    {
        $data = $this->patch($slug, $this->isClubMember('ki'));

        $fix = $data['item'];

        if ($fix->getFix()) {
            $notifyService->notify(
                'notif_fixs',
                'Demande de dépannage',
                'Ta demande de dépannage a été actualisée par le KI !',
                'to',
                [$fix->getUser()]
            );
        }

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une tâche de dépannage",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Clubinfo"
     * )
     * @Route("/fixs/{slug}", methods={"DELETE"})
     */
    public function deleteFixAction($slug)
    {
        $fix = $this->getOne($slug);
        $this->delete($slug,
            $this->user->getUsername() == $fix->getUser()->getUsername() || $this->isClubMember('ki')
        );

        return $this->json(null, 204);
    }
}
