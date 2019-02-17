<?php

namespace App\Controller\Foyer;

use App\Controller\BaseController;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\TransactionRepository;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class FoyerController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Retourne des statistiques générales sur le Foyer",
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
     * @Route("/statistics/foyer/dashboard", methods={"GET"})
     */
    public function getFoyerStatisticsDashboardAction(TransactionRepository $transactionRepository)
    {
        $this->trust($this->isFoyerMember());

        $statistics = [
            'promoBalances' => [
                'labels' => [],
                'data' => [],
            ],
            'soldBeers' => [
                'labels' => [],
                'data' => [],
            ],
        ];

        $promoBalances = $transactionRepository->getPromoBalances();

        foreach ($promoBalances as $promoBalance) {
            $statistics['promoBalances']['labels'][] = trim($promoBalance['promo']);
            $statistics['promoBalances']['data'][] = round($promoBalance['promoBalance'], 2);
        }

        $soldBeers = $transactionRepository->getSoldBeers();

        foreach ($soldBeers as $soldBeer) {
            $statistics['soldBeers']['labels'][] = trim($soldBeer['name']);
            $statistics['soldBeers']['data'][] = $soldBeer['soldBeer'];
        }

        return $this->json($statistics);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Retourne des statistiques Foyer de l'utilisateur",
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
     *         response="409",
     *         description="La requête ne peut être traitée à l’état actuel, problème de reconnaisance de nom"
     *     )
     * )
     *
     * @Route("/statistics/foyer/{username}", methods={"GET"})
     */
    public function getFoyerStatisticsAction(User $user, TransactionRepository $transactionRepository)
    {
        $this->trust(!$this->is('EXTERIEUR'));

        if (!$user->getStatsFoyer()) {
            return $this->json(null, 200);
        }
        $statistics = $transactionRepository->getUserStatistics($user);

        return $this->json($statistics);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Retourne des statistiques générales sur le Foyer",
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
     * @Route("/statistics/foyer", methods={"GET"})
     */
    public function getFoyerStatisticsMainAction(TransactionRepository $transactionRepository)
    {
        $this->trust(!$this->is('EXTERIEUR'));

        $statistics = [
            'hallOfFame' => $transactionRepository->getHallOfFame(),
        ];

        return $this->json($statistics);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Retourne le csv des personnes ayant un compte foyer négatif",
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
     * @Route("/foyer/debts", methods={"GET"})
     */
    public function getFoyerDebtsAction()
    {
        $this->trust($this->isFoyerMember());

        $response = new StreamedResponse(function () {
            $results = $this->repository->getDebtsIterator();
            $handle = fopen('php://output', 'r+');

            fputcsv($handle, ['username', 'email', 'promo', 'firstName', 'lastName', 'balance']);

            foreach ($results as $row) {
                fputcsv($handle, $row[$results->key()]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename="dettes.csv"');

        return $response;
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Retourne le csv de la répartition de l'argent par promo",
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
     * @Route("/foyer/promo-balance", methods={"GET"})
     */
    public function getFoyerPromoBalanceAction(TransactionRepository $transactionRepository)
    {
        $this->trust($this->isFoyerMember());

        $response = new StreamedResponse(function () use ($transactionRepository) {
            $results = $transactionRepository->getPromoBalances();
            $handle = fopen('php://output', 'r+');

            fputcsv($handle, ['promo', 'balance']);

            foreach ($results as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename="promo-balance.csv"');

        return $response;
    }
}
