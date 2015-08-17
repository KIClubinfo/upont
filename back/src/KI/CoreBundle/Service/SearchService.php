<?php

namespace KI\CoreBundle\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KI\CoreBundle\Entity\Image;

class SearchService
{
    protected $manager;
    protected $userRepository;

    public function __construct(EntityManager $manager, EntityRepository $userRepository)
    {
        $this->manager        = $manager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param  string $search La recherche complète venant de la requête
     * @return array  $category, $criteria
     * @throws BadRequestHttpException Si le format n'est pas bon
     */
    public function analyzeRequest($search)
    {
        $match = array();

        if (!preg_match('/(.*)\/(.*)/', $search, $match)) {
            throw new BadRequestHttpException('Syntaxe de la recherche erronée');
        }

        return array($match[1], $match[2]);
    }

    /**
     * Recherche au travers des repos suivant la catégorie voulue
     * @param  string $category La catégorie d'objets recherchés
     * @param  string $criteria Le critère de recherche
     * @return array            Un tableau de résultats formaté
     * @see $this->format()
     * @throws BadRequestHttpException Si la catégorie d'objets recherchés n'existe pas
     */
    public function search($category, $criteria)
    {
        switch ($category) {
        case 'User':
            return array('users' => $this->searchUser($criteria));
        case '':
            return array(
                'clubs'   => $this->searchRepository('KIUserBundle:Club', $criteria, 'fullName'),
                'courses' => $this->searchRepository('KIPublicationBundle:Course', $criteria),
                'files'   => $this->searchRepository('KIPonthubBundle:PonthubFile', $criteria),
                'posts'   => $this->searchRepository('KIPublicationBundle:Post', $criteria),
                'users'   => $this->searchUser($criteria),
            );
        default:
            throw new BadRequestHttpException('Syntaxe de la recherche erronée');
        }

        return $results;
    }

    // On fouille un repo à la recherche d'entités correspondantes au nom
    protected function searchRepository($repositoryName, $criteria, $additionnalField = null) {
        $repository = $this->manager->getRepository($repositoryName);
        $qb = $repository->createQueryBuilder('e');

        // Si on a affaire au repo club on peut chercher sur le nom complet
        if ($additionnalField !== null) {
            $qb
                ->orWhere('SOUNDEX(e.'.$additionnalField.') = SOUNDEX(:search)')
                ->orwhere('e.'.$additionnalField.' LIKE :searchlike')
            ;
        }

        $results = $qb
            ->orwhere('SOUNDEX(e.name) = SOUNDEX(:search)')
            ->orwhere('e.name LIKE :searchlike')
            ->andwhere('e.name <> \'message\'')
            ->setParameter('search', $criteria)
            ->setParameter('searchlike', '%'.$criteria.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;

        return $this->format($results, $criteria);
    }

    // La recherche d'user demande une fonction particulière (champs différents, acronyme...)
    protected function searchUser($criteria) {
        $qb = $this->userRepository->createQueryBuilder('e');

        $results = $qb
            ->orwhere('SOUNDEX(CONCAT(e.firstName, CONCAT(\' \', CONCAT(e.lastName, CONCAT(\' \', COALESCE(e.nickname, \'\')))))) = SOUNDEX(:search)')
            ->orwhere('CONCAT(e.firstName, CONCAT(\' \', CONCAT(e.lastName, CONCAT(\' \', COALESCE(e.nickname, \'\'))))) LIKE :searchlike')
            ->setParameter('search', $criteria)
            ->setParameter('searchlike', '%'.$criteria.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;

        return $this->format($results, $criteria);
    }

    // On formate et ordonne les données pour le retour
    protected function format($results, $criteria) {
        $return = $score = array();
        $percent = 0;

        foreach ($results as $result) {
            $name = $result->getName();
            $class = preg_replace('/.*\\\/', '', get_class($result));
            $item = array(
                'name' => $name,
                'slug' => $result->getSlug(),
                'type' => $class
            );

            // On sort les objets non actifs
            if ($class == 'Course' && ($result->getActive() === null || !$result->getActive())) {
                continue;
            }

            // Pour les épisodes et les musiques on ajoute une référence à l'entité parent
            if ($class == 'Episode') {
                $item['parent'] = $result->getSerie()->getSlug();
            }
            if ($class == 'Music') {
                $item['parent'] = $result->getAlbum()->getSlug();
            }

            // Si une image existe on la rajoute
            if (method_exists($result, 'imageUrl') && $result->imageUrl() !== null) {
                $item['image_url'] = $result->imageUrl();
            }

            $return[] = $item;

            // On trie par pertinence
            similar_text(strtolower($name), strtolower($criteria), $percent);
            $score[] = $percent;
        }
        array_multisort($score, SORT_DESC, $return);

        return $return;
    }
}
