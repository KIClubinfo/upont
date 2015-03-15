<?php

namespace KI\UpontBundle\Controller\Core;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Fonctions générales pour servir une ressource de type REST
class ResourceController extends \KI\UpontBundle\Controller\Core\LikeableController
{
    // Actions REST

    protected function paginate($repo)
    {
        $request = $this->getRequest()->query;
        list($page, $limit, $sort, $sortBy) = $this->getPaginateData($request);

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
        if ($request->has('filterBy') && $request->has('filterValue'))
            $findBy = array($request->get('filterBy') => $request->get('filterValue'));

        return array($findBy, $sortBy, $limit, ($page - 1)*$limit, $page, $totalPages, $count);
    }

    protected function getPaginateData($request) {
        $page  = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 100;
        $sort  = $request->has('sort') ? $request->get('sort') : null;

        if ($sort === null) {
            $sortBy = array('id' => 'DESC');
        } else {
            $order = preg_match('#^\-.*#isU', $sort) ? 'DESC' : 'ASC';
            $field = preg_replace('#^\-#isU', '', $sort);
            $sortBy = array($field => $order);
        }
        return array($page, $limit, $sort, $sortBy);
    }

    public function generatePages($results, $limit, $page, $totalPages, $count, $context = null)
    {
        foreach ($results as $key => $result) {
            $results[$key] = $this->retrieveLikes($result);

            // Cas spécial pour les événements : on ne veut pas afficher les événements
            // perso de tout le monde
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

        if ($page > 1)
            $links[] = $baseUrl.($page - 1).'&limit='.$limit.'>;rel=previous';
        $links[] = $baseUrl.'1'.'&limit='.$limit.'>;rel=first';
        $links[] = $baseUrl.$page.'&limit='.$limit.'>;rel=self';
        $links[] = $baseUrl.$totalPages.'&limit='.$limit.'>;rel=last';
        if ($page < $totalPages)
            $links[] = $baseUrl.($page + 1).'&limit='.$limit.'>;rel=next';

        // À refacto quand la PR sur le JMSSerializerBundle sera effectuée
        // (voir BaseController::restResponseContext pour plus de détails)
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
     * @Route\View()
     */
    public function getAll()
    {
        list($findBy, $sortBy, $limit, $offset, $page, $totalPages, $count) = $this->paginate($this->repo);
        $results = $this->repo->findBy($findBy, $sortBy, $limit, $offset);
        return $this->generatePages($results, $limit, $page, $totalPages, $count);
    }

    /**
     * @Route\View()
     */
    protected function getOne($slug)
    {
        $item = $this->findBySlug($slug);

        return $this->retrieveLikes($item);
    }

    protected function retrieveLikes($item)
    {
        // Si l'entité a un système de like/dislike, précise si l'user actuel (un)like
        if (property_exists($item, 'like')) {
            $item->setLike($item->getLikes()->contains($this->user));
            $item->setDislike($item->getDislikes()->contains($this->user));
        }
        if (property_exists($item, 'attend')) {
            $item->setAttend($item->getAttendees()->contains($this->user));
            $item->setPookie($item->getPookies()->contains($this->user));
        }
        return $item;
    }



    // Création de ressources
    protected function processForm($item, $method = 'PATCH')
    {
        $form = $this->createForm(new $this->form(), $item, array('method' => $method));
        $form->handleRequest($this->getRequest());
        $code = 400;

        if ($form->isValid()) {
            if ($method == 'POST') {
                $this->em->persist($item);
                $code = 201;
            } else {
                $code = 204;
            }
        } else
            $this->em->detach($item);

        return array('form' => $form, 'item' => $item, 'code' => $code);
    }

    protected function partialPost($auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_MODO') && !$auth)
            throw new AccessDeniedException();
        return $this->processForm(new $this->class(), 'POST');
    }

    protected function postView($data)
    {
        if ($data['code'] == 400) {
            return RestView::create($data['form'], 400);
        } else if ($data['code'] == 204) {
            $this->em->flush();
            return RestView::create(null, 204);
        } else {
            $this->em->flush();
            return RestView::create($data['item'],
                201,
                array(
                    'Location' => $this->generateUrl(
                        'get_'.strtolower($this->className),
                        array('slug' => $data['item']->getSlug()),
                        true
                    )
                )
            );
        }
    }

    /**
     * @Route\View()
     */
    protected function post()
    {
        return $this->postView($this->partialPost());
    }

    /**
     * @Route\View()
     */
    protected function put($slug, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_MODO') && !$auth)
            throw new AccessDeniedException('Accès refusé');
        $item = $this->findBySlug($slug);
        return $this->postView($this->processForm($item, 'PUT'));
    }

    /**
     * @Route\View()
     */
    protected function patch($slug, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_MODO') && !$auth)
            throw new AccessDeniedException('Accès refusé');
        $item = $this->findBySlug($slug);
        return $this->postView($this->processForm($item, 'PATCH'));
    }

    /**
     * @Route\View()
     */
    protected function delete($slug, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_MODO') && !$auth)
            throw new AccessDeniedException('Accès refusé');
        $item = $this->findBySlug($slug);
        $this->em->remove($item);
        $this->em->flush();
    }
}
