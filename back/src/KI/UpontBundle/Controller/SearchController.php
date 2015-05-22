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
            $results = array('files' => $this->searchRepo('Ponthub\\'.$category, $criteria));
            break;
        case 'Ponthub':
            $results = array('files' => $this->searchRepo('Ponthub\PonthubFile', $criteria));
            break;
        case 'Post':
        case 'Event':
        case 'Exercice':
        case 'Course':
            $results = array('posts' => $this->searchRepo('Publications\\'.$category, $criteria));
            break;
        case 'News':
            $results = array('posts' => $this->searchRepo('Publications\Newsitem', $criteria));
            break;
        case 'Club':
            $results = array('clubs' => $this->searchRepo('Users\Club', $criteria, 'e.name, e.fullName'));
            break;
        case 'User':
            $results = array('users' => $this->searchUser($criteria));
            break;
        case 'Actor':
        case 'Genre':
        case 'Tag':
            $results = array();
            break;

        case '':
            $results = array(
                'files' => $this->searchRepo('Ponthub\PonthubFile', $criteria),
                'posts' => $this->searchRepo('Publications\Post', $criteria),
                'clubs' => $this->searchRepo('Users\Club', $criteria, 'e.name, e.fullName'),
                'users' => $this->searchUser($criteria)
            );
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

            // Si une image existe on la rajoute
            if (method_exists($result, 'imageUrl') && $result->imageUrl() != null)
                $item['image_url'] = $result->imageUrl();

            $return[] = $item;
            // On trie par pertinence
            similar_text(strtolower($name), strtolower($criteria), $percent);
            $score[] = $percent;
        }
        array_multisort($score, SORT_DESC, $return);
        return $return;
    }

    // On fouille un repo à la recherche d'entités correspondantes au nom
    private function searchRepo($repoName, $search, $fields = 'e.name') {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:'.$repoName);
        $qb = $repo->createQueryBuilder('e');

        // Si on a affaire au repo club on peut chercher sur le nom complet
        if ($repoName == 'Users\Club') {
            $qb
                ->orWhere('SOUNDEX(e.fullName) = SOUNDEX(:search)')
                ->orwhere('e.fullName LIKE :searchlike');
        }

        $results = $qb
            ->orwhere('SOUNDEX(e.name) = SOUNDEX(:search)')
            ->orwhere('e.name LIKE :searchlike')
            ->setParameter('search', $search)
            ->setParameter('searchlike', '%'.$search.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->format($results, $search);
    }

    // La recherche d'user demande une fonction particulière (champs différents, acronyme...
    private function searchUser($search) {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:Users\User');
        $qb = $repo->createQueryBuilder('e');

        $results = $qb
            ->orwhere('SOUNDEX(
                CONCAT(e.firstName, CONCAT( \' \',
                    CONCAT(e.lastName, CONCAT(\' \',
                        CONCAT(e.acronyme(), CONCAT(\' \', COALESCE(e.nickname, \'\')))
                        ))
                    ))
                )
                = SOUNDEX(:search)')
            ->orwhere('CONCAT(e.firstName, CONCAT( \' \',
                CONCAT(e.lastName, CONCAT(\' \',
                    CONCAT(e.acronyme(), CONCAT(\' \', COALESCE(e.nickname, \'\')))
                        ))
                    ))
                LIKE :searchlike')
            ->setParameter('search', $search)
            ->setParameter('searchlike', '%'.$search.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->format($results, $search);
    }
}
