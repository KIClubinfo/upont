<?php

namespace KI\CoreBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class PaginateHelper
{
    protected $manager;
    protected $request;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function setRequest(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * En fonction de la requête, récupère les données utiles à la pagination
     * @param  EntityRepository $repository Le repository sur lequel effectuer les comptes
     * @return array                        Les données de pagination (nombre de pages, etc.)
     */
    public function paginateData(EntityRepository $repository, array $findBy = [])
    {
        $queryBuilder = $repository->createQueryBuilder('o');
        $request = $this->request->query;

        // On s'assure de bien recevoir des arrays
        foreach ($findBy as $key => $value) {
            $findBy[$key] = array($value);
        }

        // On récupère les paramètres de la requête
        $page  = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 100;
        $sort  = $request->has('sort') ? $request->get('sort') : null;

        if ($sort === null) {
            $sortBy = ['id' => 'DESC'];
        } else {
            $sortBy = [];

            foreach (explode(',', $sort) as $value) {
                $order = preg_match('/^\-.*/isU', $value) ? 'DESC' : 'ASC';
                $field = preg_replace('/^\-/isU', '', $value);
                $sortBy[$field] = $order;
            }
        }

        foreach ($request->all() as $key => $values) {
            if ($key != 'page' && $key != 'limit' && $key != 'sort') {
                $findBy[$key] = explode(',', $values);
            }
        }

        // On compte le nombre total d'entrées dans la BDD
        $queryBuilder->select('count(o.id)');
        foreach ($findBy as $key => $values){
            $andCount = 0;
            $and = '';
            foreach($values as $value){
                if($andCount > 0){
                    $and .= ' OR ';
                }
                $and .= 'o.' . $key . ' = :' . $key . $andCount;
                $queryBuilder->setParameter($key . $andCount, $value);

                $andCount++;
            }
            $queryBuilder->andWhere($and);
        }
        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        // On vérifie que l'utilisateur ne fasse pas de connerie avec les variables
        $totalPages = ceil($count/$limit);
        $limit = min($limit, 10000);
        $limit = max($limit, 1);
        $page  = min($page, $totalPages);
        $page  = max($page, 1);

        return [
            'findBy'     => $findBy,
            'sortBy'     => $sortBy,
            'limit'      => $limit,
            'offset'     => ($page - 1)*$limit,
            'page'       => $page,
            'totalPages' => $totalPages,
            'count'      => $count
        ];
    }

    /**
     * Génère les headers de pagination et renvoie la réponse
     * @param  array   $results    Les résultats à paginer
     * @param  integer $limit      Le nombre d'objets par page
     * @param  integer $page       Le numéro de la page en cours
     * @param  integer $totalPages Le nombre total de pages
     * @param  integer $count      Le nombre total d'objets
     * @return Response
     */
    public function paginateView($results, $limit, $page, $totalPages, $count)
    {
        // On prend l'url de la requête
        $baseUrl = '<'.str_replace($this->request->getBaseUrl(), '', $this->request->getRequestUri());

        // On enlève tous les paramètres GET de type "page" et "limit" précédents s'il y en avait
        $baseUrl = preg_replace('/[\?&](page|limit)=\d+/', '', $baseUrl);
        $baseUrl .= !preg_match('/\?/', $baseUrl) ? '?' : '&';

        // On va générer les notres pour les links
        $baseUrl .= 'page=';
        $links = [];

        // First
        $links[] = $baseUrl.'1'.'&limit='.$limit.'>;rel=first';

        // Previous
        if ($page > 1) {
            $links[] = $baseUrl.($page - 1).'&limit='.$limit.'>;rel=previous';
        }

        // Self
        $links[] = $baseUrl.$page.'&limit='.$limit.'>;rel=self';

        // Next
        if ($page < $totalPages) {
            $links[] = $baseUrl.($page + 1).'&limit='.$limit.'>;rel=next';
        }

        // Last
        $links[] = $baseUrl.$totalPages.'&limit='.$limit.'>;rel=last';

        return [
            $results,
            $links,
            $count
        ];
    }
}
