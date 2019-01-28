<?php

namespace App\Controller;

use App\Entity\Pontlyvalent;
use App\Entity\User;
use App\Form\PontlyvalentType;
use App\Helper\PaginateHelper;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PontlyvalentsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Pontlyvalent::class, PontlyvalentType::class);
    }

    private function checkPontlyvalentOpen()
    {
        $targetPromo = $this->getConfig('pontlyvalent.promo');
        $isTooYoung = strcmp($this->user->getPromo(), $targetPromo) > 0;

        if ($isTooYoung) {
            throw new BadRequestHttpException('Ton tour n\'est pas encore arrivé, petit ' . $this->user->getPromo() . ' !');
        }

        if (!$this->getConfig('pontlyvalent.open')) {
            throw new BadRequestHttpException('Le pontlyvalent est fermé !');
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les commentaires",
     *  output="App\Entity\Pontlyvalent",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/pontlyvalent", methods={"GET"})
     */
    public function getPontlyvalentsAction(PaginateHelper $paginateHelper)
    {
        if ($this->is('MODO') || $this->isClubMember('bde')) {
            $filters = [];
        } else {
            $filters = [
                'author' => $this->user
            ];
        }

        return $this->json($paginateHelper->paginate(
            $this->repository,
            $filters
        ));
    }

    /**
     * @ApiDoc(
     *  description="Liste les commentaires sur un user",
     *  output="App\Entity\Pontlyvalent",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{targetUsername}/pontlyvalent", methods={"GET"})
     */
    public function getPontlyvalentAction($targetUsername)
    {
        $this->checkPontlyvalentOpen();

        $target = $this->manager->getRepository(User::class)->findOneByUsername($targetUsername);

        $pontlyvalent = $this->repository->getPontlyvalent($target, $this->user);

        if (count($pontlyvalent) != 1)
            throw new NotFoundHttpException();

        return $this->json($pontlyvalent[0]);
    }

    /**
     * @ApiDoc(
     *  description="Ecrit un commentaire sur quelqu'un",
     *  input="App\Form\PontlyvalentType",
     *  output="App\Entity\Pontlyvalent",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{targetUsername}/pontlyvalent", methods={"POST"})
     */
    public function postPontlyvalentAction(Request $request, $targetUsername)
    {
        $this->checkPontlyvalentOpen();

        /**
         * @var User $target
         */
        $target = $this->manager->getRepository(User::class)->findOneByUsername($targetUsername);

        $targetPromo = $this->getConfig('pontlyvalent.promo');
        if ($target->getPromo() != $targetPromo) {
            throw new BadRequestHttpException('Ce n\'est pas un ' . $targetPromo . ' !');
        }

        $pontlyvalent = $this->repository->getPontlyvalent($target, $this->user);
        if (count($pontlyvalent) == 0) {
            $pontlyvalent = new Pontlyvalent();
            $pontlyvalent->setTarget($target);
            $pontlyvalent->setAuthor($this->user);
        } else {
            $pontlyvalent = $pontlyvalent[0];
        }

        $pontlyvalent->setDate(time());

        $form = $this->createForm(PontlyvalentType::class, $pontlyvalent, ['method' => 'POST']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($pontlyvalent);
            $this->manager->flush();

            return $this->json($pontlyvalent, 201);
        } else {
            $this->manager->detach($pontlyvalent);
            return $this->json($form, 400);
        }
    }

    /**
     * @ApiDoc(
     *  description="Supprime un commentaire",
     *  input="App\Form\PontlyvalentType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{targetUsername}/pontlyvalent", methods={"DELETE"})
     */
    public function deletePontlyvalentAction($targetUsername)
    {
        $this->checkPontlyvalentOpen();

        $target = $this->manager->getRepository(User::class)->findOneByUsername($targetUsername);

        $pontlyvalent = $this->repository->getPontlyvalent($target, $this->user);

        if (count($pontlyvalent) != 1) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }

        $this->manager->remove($pontlyvalent[0]);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
