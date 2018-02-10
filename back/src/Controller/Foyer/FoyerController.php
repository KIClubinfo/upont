<?php

namespace App\Controller\Foyer;

use App\Controller\BaseController;
use App\Entity\Transaction;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\TransactionRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FoyerController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        // TODO remove this nonsense
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @ApiDoc(
     *  description="Retourne des statistiques générales sur le Foyer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Foyer"
     * )
     * @Route("/statistics/foyer/dashboard")
     * @Method("GET")
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

        foreach ($promoBalances as $promoBalance){
            $statistics['promoBalances']['labels'][] = trim($promoBalance['promo']);
            $statistics['promoBalances']['data'][] = round($promoBalance['promoBalance'], 2);
        }

        $soldBeers = $transactionRepository->getSoldBeers();

        foreach ($soldBeers as $soldBeer){
            $statistics['soldBeers']['labels'][] = trim($soldBeer['name']);
            $statistics['soldBeers']['data'][] = $soldBeer['soldBeer'];
        }

        return $this->json($statistics);
    }

    /**
     * @ApiDoc(
     *  description="Retourne des statistiques Foyer de l'utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   409="La requête ne peut être traitée à l’état actuel, problème de reconnaisance de nom",
     *  },
     *  section="Foyer"
     * )
     * @Route("/statistics/foyer/{username}")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Retourne des statistiques générales sur le Foyer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Foyer"
     * )
     * @Route("/statistics/foyer")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Retourne le csv des personnes ayant un compte foyer négatif",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Foyer"
     * )
     * @Route("/foyer/debts")
     * @Method("GET")
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
     * @ApiDoc(
     *  description="Retourne le csv de la répartition de l'argent par promo",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Foyer"
     * )
     * @Route("/foyer/promo-balance")
     * @Method("GET")
     */
    public function getFoyerPromoBalanceAction()
    {
        $this->trust($this->isFoyerMember());

        $response = new StreamedResponse(function () {
            $results = $this->repository->getPromoBalances();
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
