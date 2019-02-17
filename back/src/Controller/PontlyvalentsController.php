<?php

namespace App\Controller;

use App\Entity\Pontlyvalent;
use App\Entity\User;
use App\Form\PontlyvalentType;
use App\Helper\PaginateHelper;
use App\Repository\PontlyvalentRepository;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Liste les commentaires",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/users/pontlyvalent", methods={"GET"})
     */
    public function getPontlyvalentsAction(PontlyvalentRepository $pontlyvalentRepository, PaginateHelper $paginateHelper)
    {
        if ($this->is('MODO') || $this->isClubMember('bde')) {
            $filters = [];
        } else {
            $filters = [
                'author' => $this->user
            ];
        }

        return $this->json($paginateHelper->paginate(
            $pontlyvalentRepository,
            $filters
        ));
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Liste les commentaires sur un user",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/users/{targetUsername}/pontlyvalent", methods={"GET"})
     */
    public function getPontlyvalentAction($targetUsername, UserRepository $userRepository, PontlyvalentRepository $pontlyvalentRepository)
    {
        $this->checkPontlyvalentOpen();

        $target = $userRepository->findOneByUsername($targetUsername);

        $pontlyvalent = $pontlyvalentRepository->getPontlyvalent($target, $this->user);

        if (count($pontlyvalent) != 1)
            throw new NotFoundHttpException();

        return $this->json($pontlyvalent[0]);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Ecrit un commentaire sur quelqu'un",
     *     @SWG\Parameter(
     *         name="text",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/users/{targetUsername}/pontlyvalent", methods={"POST"})
     */
    public function postPontlyvalentAction(Request $request, $targetUsername, UserRepository $userRepository, PontlyvalentRepository $pontlyvalentRepository)
    {
        $this->checkPontlyvalentOpen();

        /**
         * @var User $target
         */
        $target = $userRepository->findOneByUsername($targetUsername);

        $targetPromo = $this->getConfig('pontlyvalent.promo');
        if ($target->getPromo() != $targetPromo) {
            throw new BadRequestHttpException('Ce n\'est pas un ' . $targetPromo . ' !');
        }

        $pontlyvalent = $pontlyvalentRepository->getPontlyvalent($target, $this->user);
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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Supprime un commentaire",
     *     @SWG\Parameter(
     *         name="text",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/users/{targetUsername}/pontlyvalent", methods={"DELETE"})
     */
    public function deletePontlyvalentAction($targetUsername, UserRepository $userRepository, PontlyvalentRepository $pontlyvalentRepository)
    {
        $this->checkPontlyvalentOpen();

        $target = $userRepository->findOneByUsername($targetUsername);

        $pontlyvalent = $pontlyvalentRepository->getPontlyvalent($target, $this->user);

        if (count($pontlyvalent) != 1) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }

        $this->manager->remove($pontlyvalent[0]);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
