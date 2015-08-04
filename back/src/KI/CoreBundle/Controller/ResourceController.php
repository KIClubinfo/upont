<?php

namespace KI\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;

// Fonctions générales pour servir une ressource de type REST, CommentsController en est un exemple
class ResourceController extends \KI\CoreBundle\Controller\LikeableController
{
    // Actions REST

    protected function paginate($repo)
    {
        $request = $this->getRequest()->query;
        list($page, $limit, $sortBy) = $this->getPaginateData($request);

        // On compte le nombre total d'entrées dans la BDD
        $qb = $repo->createQueryBuilder('o');
        $qb->select('count(o.id)');
        $count = $qb->getQuery()->getSingleScalarResult();

        // On vérifie que l'utilisateur ne fasse pas de connerie avec les variables
        $totalPages = ceil($count/$limit);
        if ($limit > 10000)
            $limit = 10000;
        if ($limit < 1)
            $limit = 1;
        if ($page > $totalPages)
            $page = $totalPages;
        if ($page < 1)
            $page = 1;

        // Définition des filtres
        $findBy = array();
        foreach ($request->all() as $key => $value) {
            if ($key != 'page' && $key != 'limit' && $key != 'sort' && $key != 'filterBy' && $key != 'filterValue')
                $findBy[$key] = $value;
        }
        // Maintenu pour la compatibilité
        if ($request->has('filterBy') && $request->has('filterValue'))
            $findBy[$request->get('filterBy')] = $request->get('filterValue');

        return array($findBy, $sortBy, $limit, ($page - 1)*$limit, $page, $totalPages, $count);
    }

    protected function getPaginateData($request) {
        $page  = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 100;
        $sort  = $request->has('sort') ? $request->get('sort') : null;

        if ($sort === null) {
            $sortBy = array('id' => 'DESC');
        } else {
            $sortBy = array();

            foreach (explode(',', $sort) as $value) {
                $order = preg_match('#^\-.*#isU', $value) ? 'DESC' : 'ASC';
                $field = preg_replace('#^\-#isU', '', $value);
                $sortBy[$field] = $order;
            }
        }
        return array($page, $limit, $sortBy);
    }

    /**
     * @param double $totalPages
     */
    public function generatePages($results, $limit, $page, $totalPages, $count, $context = null)
    {
        foreach ($results as $key => $result) {
            // Cas spécial pour les événements :
            // on ne veut pas afficher les événements perso de tout le monde
            if ($this->className == 'Event' && $results[$key]->getAuthorClub() === null)
                unset($results[$key]);
        }

        // On prend l'url de la requête
        $baseUrl = '<'.str_replace($this->getRequest()->getBaseUrl(), '', $this->getRequest()->getRequestUri());
        // On enlève tous les paramètres GET de type "page" et "limit" précédents s'il y en avait
        $baseUrl = preg_replace('#[\?&](page|limit)=\d+#', '', $baseUrl);
        $baseUrl .= !preg_match('#\?#', $baseUrl) ? '?' : '&';

        // On va générer les notres pour les links
        $baseUrl .= 'page=';
        $links = array();

        $links[] = $baseUrl.'1'.'&limit='.$limit.'>;rel=first';
        if ($page > 1)
            $links[] = $baseUrl.($page - 1).'&limit='.$limit.'>;rel=previous';
        $links[] = $baseUrl.$page.'&limit='.$limit.'>;rel=self';
        if ($page < $totalPages)
            $links[] = $baseUrl.($page + 1).'&limit='.$limit.'>;rel=next';
        $links[] = $baseUrl.$totalPages.'&limit='.$limit.'>;rel=last';

        // À refacto quand la PR sur le JMSSerializerBundle sera effectuée
        // (voir BaseController::restContextResponse pour plus de détails)
        if ($context) {
            return $this->restContextResponse(
                $results,
                200,
                array(
                    'Links' => implode(',', $links),
                    'Total-count' => $count
                ),
                $context
            );
        }

        return $this->restResponse(
            $results,
            200,
            array(
                'Links' => implode(',', $links),
                'Total-count' => $count
            )
        );
    }

    /**
     * Route GET (liste) générique
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    public function getAll($auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);
        list($findBy, $sortBy, $limit, $offset, $page, $totalPages, $count) = $this->paginate($this->repo);
        $results = $this->repository->findBy($findBy, $sortBy, $limit, $offset);
        return $this->generatePages($results, $limit, $page, $totalPages, $count);
    }

    /**
     * Route GET générique
     * @param  string  $slug Le slug de l'entité à récupérer
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function getOne($slug, $auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);
        return $this->findBySlug($slug);
    }

    /**
     * Permet de valider un formulaire, rend la main sur l'entité ainsi créée
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return array         Le formulaire traité
     */
    protected function partialPost($auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $formHelper = $this->get('ki_core.helper.form');
        return $formHelper->processForm(new $this->class(), 'POST');
    }

    /**
     * Permet d'afficher un formulaire une fois celui-ci validé
     * @param  array $data Le formulaire validé
     * @return Response
     */
    protected function postView($data)
    {
        $formHelper = $this->get('ki_core.helper.form');
        return $formHelper->postView($data);
    }

    /**
     * Route POST générique effectuant directement validation et affichage
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function post($auth = false)
    {
        return $this->postView($this->partialPost($auth));
    }

    /**
     * Route PATCH générique
     * @param  string  $slug Le slug de l'entité à modifier
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function patch($slug, $auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $item = $this->findBySlug($slug);

        $formHelper = $this->get('ki_core.helper.form');
        return $this->postView($formHelper->processForm($item, 'PATCH'));
    }

    /**
     * Route DELETE générique
     * @param  string  $slug Le slug de l'entité à supprimer
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function delete($slug, $auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $item = $this->findBySlug($slug);
        $this->manager->remove($item);
        $this->manager->flush();
    }
}
