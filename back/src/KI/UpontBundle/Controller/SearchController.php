<?php

namespace KI\UpontBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends \KI\UpontBundle\Controller\Core\BaseController
{
    /**
     * @ApiDoc(
     *  description="Recherche au travers de tout le site",
     *  requirements={
     *   {
     *    "name"="search",
     *    "dataType"="string",
     *    "description"="Le critère de recherche"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function searchAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException('Accès refusé');

        if (!$request->request->has('search'))
            throw new BadRequestHttpException('Critère de recherche manquant');

        $search = $request->request->get('search');
        $match = array();

        if (!preg_match('#(.*)/(.*)#', $search, $match))
            throw new BadRequestHttpException('Syntaxe de la recherche erronée');
        $category = $match[1];
        $criteria = $match[2];

        if (empty($criteria))
            throw new BadRequestHttpException('Syntaxe de la recherche erronée');

        switch ($category) {
            case 'Movie':
            case 'Serie':
            case 'Episode':
            case 'Album':
            case 'Music':
            case 'Game':
            case 'Software':
            case 'Other':
                $results = $this->searchRepo('Ponthub\\'.$category, $criteria);
                break;
            case 'Ponthub':
                $results = $this->searchRepo('Ponthub\PonthubFile', $criteria);
                break;
            case 'Post':
            case 'Event':
            case 'Exercice':
            case 'Course':
                $results = $this->searchRepo('Publications\\'.$category, $criteria);
                break;
            case 'News':
                $results = $this->searchRepo('Publications\Newsitem', $criteria);
                break;
            case 'Club':
                $results = $this->searchRepo('Users\\'.$category, $criteria);
                break;
            case 'User':
                $results = $this->searchUser($criteria);
                break;
            case 'Actor':
            case 'Genre':
            case 'Tag':
                $results = array();
                break;

            default:
                throw new BadRequestHttpException('Syntaxe de la recherche erronée');
        }

        return $this->jsonResponse($results);
    }

    // On formate et ordonne les données pour le retour
    private function format($results, $criteria) {
        $return = $score = array();
        $percent = 0;
        foreach ($results as $result) {
            $name = $result->getName();
            $return[] = array(
                'name' => $name,
                'slug' => $result->getSlug(),
            );
            // On trie par pertinence
            similar_text($name, $criteria, $percent);
            $score[] = $percent;
        }
        array_multisort($score, SORT_DESC, $return);
        return $return;
    }

    // On fouille un repo à la recherche d'entités correspondantes au nom
    private function searchRepo($repo, $criteria) {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:'.$repo);
        $qb = $repo->createQueryBuilder('e');
        $searches = explode(' ', $criteria);

        // On définit les paramètres de recherche
        $cqb = array();
        foreach ($searches as $key => $value) {
            $cqb[] = $qb->expr()->like('e.name', $qb->expr()->literal('%'.$value.'%'));
        }
        $qb->andWhere(call_user_func_array(array($qb->expr(), 'orx'), $cqb));
        $results = $qb->setMaxResults(10)
                      ->getQuery()
                      ->getResult();

        return $this->format($results, $criteria);
    }

    private function searchUser($criteria) {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:Users\User');
        $qb = $repo->createQueryBuilder('e');
        $searches = explode(' ', $criteria);

        // On définit les paramètres de recherche
        $concat = $qb->expr()->concat('e.firstName', $qb->expr()->concat($qb->expr()->literal(' '), 'e.lastName'));
        $cqb = array();
        foreach ($searches as $key => $value) {
            $cqb[] = $qb->expr()->like($concat, $qb->expr()->literal('%'.$value.'%'));
        }
        $qb->andWhere(call_user_func_array(array($qb->expr(), 'orx'), $cqb));
        $results = $qb->setMaxResults(10)
                      ->getQuery()
                      ->getResult();

        return $this->format($results, $criteria);
    }
}
