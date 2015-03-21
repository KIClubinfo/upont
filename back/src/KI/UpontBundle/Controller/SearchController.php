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
            case 'User':
                $results = $this->searchRepo('Users\\'.$category, $criteria);
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
            $class = preg_replace('#.*\\\#', '', get_class($result));
            $item = array(
                'name' => $name,
                'slug' => $result->getSlug(),
                'type' => $class
            );

            // Pour les épisodes et les musiques on se réfère à l'entité parent
            if ($class == 'Episode')
                $item['parent'] = $result->getSerie()->getSlug();
            if ($class == 'Music')
                $item['parent'] = $result->getAlbum()->getSlug();

            $return[] = $item;
            // On trie par pertinence
            similar_text($name, $criteria, $percent);
            $score[] = $percent;
        }
        array_multisort($score, SORT_DESC, $return);
        return $return;
    }

    // On fouille un repo à la recherche d'entités correspondantes au nom
    private function searchRepo($repoName, $criteria, $field = 'e.name') {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:'.$repoName);
        $qb = $repo->createQueryBuilder('e');
        $searches = explode(' ', $criteria);

        // Si on a affaire au repo user on utilise un critère spécial
        if ($repoName == 'Users\User') {
            $field = $qb->expr()->concat('e.firstName',
                      $qb->expr()->concat($qb->expr()->literal(' '),
                       $qb->expr()->concat('e.lastName',
                        $qb->expr()->concat($qb->expr()->literal(' '), 'COALESCE(e.nickname, \'\')'))));
        }
        // Si on a affaire au repo club on peut chercher sur le nom complet
        if ($repoName == 'Users\Club') {
            $field = $qb->expr()->concat('e.name',
                      $qb->expr()->concat($qb->expr()->literal(' '), 'e.fullName'));
        }

        // On définit les paramètres de recherche
        $cqb = array();
        foreach ($searches as $key => $value) {
            $cqb[] = $qb->expr()->like($field, $qb->expr()->literal('%'.$value.'%'));
        }
        $qb->andWhere(call_user_func_array(array($qb->expr(), 'orx'), $cqb));
        $results = $qb->setMaxResults(10)
                      ->getQuery()
                      ->getResult();

        return $this->format($results, $criteria);
    }
}
