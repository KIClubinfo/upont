<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginateHelper
{
    protected $manager;
    /* @var Request */
    protected $request;

    public function __construct(EntityManagerInterface $manager)
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
    public function paginate(EntityRepository $repository, array $findBy = [])
    {
        $queryBuilder = $repository->createQueryBuilder('o');
        $request = $this->request->query;

        // On s'assure de bien recevoir des arrays
        foreach ($findBy as $key => $value) {
            $findBy[$key] = is_array($value) ? $value : array($value);
        }

        // On récupère les paramètres de la query
        $page = $request->has('page') ? (int)$request->get('page') : 1;
        $limit = $request->has('limit') ? (int)$request->get('limit') : 100;
        $sort = $request->has('sort') ? $request->get('sort') : null;

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

        $requestFindBy = [];
        foreach ($request->all() as $key => $values) {
            if ($key != 'page' && $key != 'limit' && $key != 'sort') {
                $findBy[$key] = explode(',', $values);
                $requestFindBy[$key] = $values;
            }
        }

        // On compte le nombre total d'entrées dans la BDD
        $queryBuilder->select('count(o.id)');
        foreach ($findBy as $key => $values) {
            $andCount = 0;
            $and = '';
            foreach ($values as $value) {
                if ($andCount > 0) {
                    $and .= ' OR ';
                }
                $and .= 'o.' . $key . ' = :' . $key . $andCount;
                $queryBuilder->setParameter($key . $andCount, $value);

                $andCount++;
            }
            $queryBuilder->andWhere($and);
        }

        foreach ($sortBy as $field => $order) {
            $queryBuilder->addOrderBy('o.' . $field, $order);
        }

        $count = (int)$queryBuilder->getQuery()->getSingleScalarResult();

        // On vérifie que l'utilisateur ne fasse pas de connerie avec les variables
        $totalPages = (int)ceil($count / $limit);
        $limit = min($limit, 10000);
        $limit = max($limit, 1);
        $page = min($page, $totalPages);
        $page = max($page, 1);

        $results = $queryBuilder->select('o')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery()
            ->getResult();

        return [
            'data' => $results,
            'pagination_params' => array_merge([
                'sort' => $sort,

                'limit' => $limit,
                'page' => $page,
            ], $requestFindBy),
            'pagination_infos' => [
                'first_page' => 1,
                'previous_page' => $page > 1 ? $page - 1 : null,
                'current_page' => $page,
                'next_page' => $page < $totalPages ? $page + 1 : null,
                'last_page' => $totalPages,
                'total_pages' => $totalPages,

                'count' => $count,
            ]
        ];
    }
}
