<?php

namespace KI\FoyerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use KI\CoreBundle\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DefaultController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'User');
    }

    /**
     * @ApiDoc(
     *  description="Retourne des statistiques Foyer de l'utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   409="La requête ne peut être traitée à l’état actuel, problème de reconnaisance de nom",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Get("/statistics/foyer/{slug}")
     */
    public function getFoyerStatisticsAction($slug)
    {
        $this->trust(!$this->is('EXTERIEUR'));

        $user = $this->findBySlug($slug);

        if (!$user->getStatsFoyer()) {
            return $this->jsonResponse(null, 200);
        }
        $statisticsHelper = $this->get('ki_foyer.helper.statistics');
        $statistics = $statisticsHelper->getUserStatistics($user);

        return $this->restResponse($statistics);
    }

    /**
     * @ApiDoc(
     *  description="Retourne des statistiques générales sur le Foyer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Get("/statistics/foyer")
     */
    public function getFoyerStatisticsMainAction()
    {
        $this->trust(!$this->is('EXTERIEUR'));

        $statistics = [
            'hallOfFame' => $this->manager->getRepository('KIFoyerBundle:Transaction')->getHallOfFame(),
        ];

        return $this->restResponse($statistics);
    }

    /**
     * @ApiDoc(
     *  description="Retourne le csv des personnes ayant un compte foyer négatif",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Get("/foyer/debts")
     */
    public function getFoyerDebtsAction()
    {
        $this->trust($this->isClubMember('foyer') || $this->is('ADMIN'));

        $response = new StreamedResponse(function() {
            $results = $this->repository->createQueryBuilder('u')
                                        ->select('u.username, u.email, u.promo, u.firstName, u.lastName, u.balance')
                                        ->where('u.balance < 0')
                                        ->orderBy('u.balance')
                                        ->getQuery()
                                        ->iterate();
            $handle = fopen('php://output', 'r+');

            fputcsv($handle, ['username', 'email', 'promo', 'firstName', 'lastName', 'balance']);

            foreach ($results as $row) {
                fputcsv($handle, $row[$results->key()]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition','attachment; filename="dettes.csv"');

        return $response;
    }
}
