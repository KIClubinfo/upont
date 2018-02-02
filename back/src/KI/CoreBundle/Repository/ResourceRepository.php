<?php
namespace KI\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

abstract class ResourceRepository extends EntityRepository
{
    public function findByDql($dql, $objectReferer, $findBy) {
        foreach ($findBy as $key => $values) {
            if ($key != 'page' && $key != 'limit' && $key != 'sort') {
                $values = explode(',', $values);
                $andCount = 0;
                $and = '';
                foreach($values as $value) {
                    if($andCount > 0){
                        $and .= ' OR ';
                    }
                    $and .= ($objectReferer . '.' . $key . ' = ' . (is_string($value) ? "\'" . $value . "\'" : $value));

                    $andCount++;
                }
                $dql .= " AND (" . $and . ")";
            }
        }

        return $dql;
    }
}
