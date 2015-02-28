<?php

namespace KI\UpontBundle\Controller\Core;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\UpontBundle\Entity\Core\StatsDaily;
use KI\UpontBundle\Entity\Core\StatsGlobal;
use KI\UpontBundle\Entity\Core\Log;

class LogsController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Log', 'Core');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retourne les logs.",
     *  output="KI\UpontBundle\Entity\Core\Log",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function getLogsAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Parse les logs de la semaine, en fait des statistiques et archive les logs",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function patchLogsAction()
    {
        // On charge les stats de la semaine
        $logs = $this->repo->findAll();

        // Et les stats globales
        $users = $this->getUsersPromo();
        $promos = $this->getAvailablePromos();
        $stats = $this->getGlobalStats($promos);
        $day = null;
        $daily = array();
        $uniqueUsers = array();
        $now = strftime('%j', time());

        foreach ($logs as $log) {
            // On fait attention à ne jamais rentrer un log d'aujourd'hui
            // cela assure que les logs d'un jour seront tous stockés dans une
            // seule entrée de stats (les jours sont lus tout d'un coup et ne
            // sont jamais séparés en deux runs)
            if (strftime('%j', $log->getDate()) == $now)
                continue;

            // Si on change de jour on crée une nouvelle entrée
            if ($day != strftime('%j', $log->getDate())) {
                // On persiste les stats du jour précédent
                if ($day !== null) {
                    foreach ($promos as $promo) {
                        $daily[$promo]->setConnectionsUnique(count($uniqueUsers[$promo]));
                        $this->em->persist($daily[$promo]);
                    }
                }

                // On crée les nouvelles stats
                foreach ($promos as $promo) {
                    $stat = new StatsDaily();
                    $stat->setPromo($promo);
                    $stat->setConnections(0);
                    $stat->setConnectionsUnique(0);
                    $stat->setYear((int) strftime('%Y', $log->getDate()));
                    $stat->setWeek((int) strftime('%W', $log->getDate()));
                    $stat->setDay((int) strftime('%j', $log->getDate()));
                    $daily[$promo] = $stat;
                    $uniqueUsers[$promo] = array();
                }

                $day = strftime('%j', $log->getDate());
            }

            // On ignore les connexions non authentifiées
            $user = $log->getUsername();
            if ($user === '')
                continue;
            $promo = $users[$user];

            if (!in_array($user, $uniqueUsers[$promo]))
                $uniqueUsers[$promo][] = $user;

            // On incrémente les statistiques numéraires
            $this->increment($daily[$promo], $log);
            $this->increment($stats[$promo], $log);

            // On supprime l'entrée de log, on n'en a plus besoin
            $this->em->remove($log);
        }

        // On persiste les dernières stats
        if (!empty($daily)) {
            foreach ($promos as $promo) {
                $daily[$promo]->setConnectionsUnique(count($uniqueUsers[$promo]));
                $this->em->persist($daily[$promo]);
            }
        }

        $this->em->flush();
        return $this->jsonResponse(null, 204);
    }

    // Retourne la liste des différentes promos
    private function getAvailablePromos()
    {
        $results = array();
        $promos = $this->em
        ->getRepository('KIUpontBundle:Users\User')
        ->createQueryBuilder('u')
        ->select('u.promo')
        ->distinct()
        ->getQuery()
        ->getResult();

        foreach ($promos as $promo) {
                    $results[] = $promo['promo'];
        }
        return $results;
    }

    // Retourne un tableau associatif username => promo
    private function getUsersPromo()
    {
        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $users = $repo->findAll();
        $results = array();

        foreach ($users as $user) {
                    $results[$user->getUsername()] = $user->getPromo();
        }
        return $results;
    }

    // Retourne les stats globales, les crée si une nouvelle promo arrive
    private function getGlobalStats($promos)
    {
        // On récupère les différentes promos
        $results = array();

        // On récupère les stats globales
        $repo = $this->em->getRepository('KIUpontBundle:Core\StatsGlobal');

        foreach ($promos as $promo) {
            $stat = $repo->findOneByPromo($promo);

            // S'il y a une nouvelle promo, on crée une nouvelle stat globale
            if (!$stat instanceof StatsGlobal) {
                $stat = new StatsGlobal();
                $stat->setPromo($promo);
                $stat->setConnections(0);
                $this->em->persist($stat);
            }

            $results[$promo] = $stat;
        }

        $this->em->flush();
        return $results;
    }

    // Incrémente une statistique
    private function increment(&$stat, Log $log)
    {
        $stat->setConnections($stat->getConnections() + 1);
        $stat->increment('httpVerbs', $log->getMethod());
        $stat->increment('httpCodes', $log->getCode());
        $stat->increment('browsers', $log->getBrowser());
        $stat->increment('systems', $log->getSystem());
        $stat->increment('hours', (int) strftime('%H', $log->getDate()));
    }
}
