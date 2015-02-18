<?php

namespace KI\UpontBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends \FOS\RestBundle\Controller\FOSRestController
{
    protected $className;
    protected $class;
    protected $namespace;
    protected $form;
    protected $repo;
    protected $em;
    protected $save;
    protected $user = null;

    // Initialise le controleur de base pour la classe $class
    // On peut éventuellement préciser un sous chemin de $namespace
    public function initialize($class, $namespace = null)
    {
        $this->className = $class;
        $this->namespace = $namespace === null ? '' : $namespace . '\\';

        // Fully qualified class names
        $this->class = 'KI\UpontBundle\Entity\\' . $this->namespace . $this->className;
        $this->form = 'KI\UpontBundle\Form\\'. $this->namespace . $this->className. 'Type';
        $this->em = $this->getDoctrine()->getManager();
        $this->repo = $this->em->getRepository('KIUpontBundle:' . $this->namespace . $this->className);

        if ($token = $this->container->get('security.context')->getToken())
            $this->user = $token->getUser();
    }

    // Permet de changer le repo actuel. Si $class non précisé, revient au précédent
    protected function switchClass($class = null)
    {
        // On garde en mémoire la classe précédente
        $this->save = $this->className;
        if ($class === null)
            $class = $this->save;

        // À priori, une sous ressource garde le même namespace
        $this->initialize($class, str_replace('\\', '', $this->namespace));
    }










    // Fonctions de génération de réponse

    public function restResponse($data, $code = 200, array $headers = array())
    {
        return new \Symfony\Component\HttpFoundation\Response(
            $this->get('jms_serializer')->serialize($data, 'json'),
            $code,
            $headers
        );
    }

    public function jsonResponse($data, $code = 200, array $headers = array())
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse($data, $code, $headers);
    }

    public function htmlResponse($data, $code = 200, array $headers = array())
    {
        return new \Symfony\Component\HttpFoundation\Response($data, $code, $headers);
    }











    // Actions REST

    // Recherche de ressources

    // Recherche une entité selon son slug
    protected function findBySlug($slug)
    {
        if (preg_match('#^\d+$#', $slug)) {
            $item = $this->repo->findOneById($slug);
        } else {
            if ($this->className != 'User')
                $item = $this->repo->findOneBySlug($slug);
            else
                $item = $this->repo->findOneByUsername($slug);
        }
        if (!$item instanceof $this->class)
            throw new NotFoundHttpException('Objet ' . $this->className . ' non trouvé');

        return $item;
    }

    /**
     * @Route\View()
     */
    protected function getAll()
    {
        // On pagine les résultats
        $request = $this->getRequest()->query;
        $page  = $request->has('page')  ? $request->get('page')  : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 100;
        $sort  = $request->has('sort')  ? $request->get('sort')  : null;

        if ($sort === null) {
            $sortBy = array('id' => 'DESC');
        } else {
            $order = preg_match('#^\-.*#isU', $sort) ? 'DESC' : 'ASC' ;
            $field = preg_replace('#^\-#isU', '', $sort);
            $sortBy = array($field => $order);
        }

        // On compte le nombre total d'entrées dans la BDD
        $qb = $this->repo->createQueryBuilder('o');
        $qb->select('count(o.id)');
        $count = $qb->getQuery()->getSingleScalarResult();

        // On vérifie que l'utilisateur ne fasse pas de connerie avec les variables
        $totalPages = ceil($count / $limit);
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

        // On génère les résultats et les liens
        $results = $this->repo->findBy($findBy, $sortBy, $limit, ($page-1)*$limit);
        foreach($results as $key => $result)
            $results[$key] = $this->retrieveLikes($result);
        $baseUrl = '<' . str_replace($this->getRequest()->getBaseUrl(), '', $this->getRequest()->getRequestUri()) . '?page=';
        $links = array(
            $baseUrl . $page . '&limit=' . $limit . '>;rel=self',
            $baseUrl . '1' . '&limit=' . $limit . '>;rel=first',
            $baseUrl . $totalPages . '&limit=' . $limit . '>;rel=last'
        );

        if ($page > 1)
            $links[] = $baseUrl . ($page - 1) . '&limit=' . $limit . '>;rel=previous';
        if ($page < $totalPages)
            $links[] = $baseUrl . ($page + 1) . '&limit=' . $limit . '>;rel=next';

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
        }
        else
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
                        'get_' . strtolower($this->className),
                        array('slug' => $data['item']->getSlug()),
                        true
                    )
                )
            );
        }
    }

    // Sert à checker si l'utilisateur actuel est membre du club au nom duquel il poste
    protected function checkClubMembership($slug = null)
    {
        $request = $this->getRequest()->request;

        // On vérifie que la requete est valide
        // Si aucun club n'est précisé, c'est qu'on publie en son nom donc ok
        if (!$request->has('authorClub') && $slug === null)
            return $this->get('security.context')->isGranted('ROLE_USER');

        $repo = $this->em->getRepository('KIUpontBundle:Users\Club');
        $club = $repo->findOneBySlug($request->has('authorClub') ? $request->get('authorClub') : $slug);

        if (!$club)
            return false;

        // On vérifie que l'utilisateur fait bien partie du club
        $repo = $this->em->getRepository('KIUpontBundle:Users\ClubUser');
        $clubUser = $repo->findOneBy(array('club' => $club, 'user' => $this->user));

        if ($clubUser)
            return true;
        return false;
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















    // Fonctions relatives aux likes/dislikes

    /**
     * @Route\View()
     */
    protected function like($slug, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER') && !$auth)
            throw new AccessDeniedException('Accès refusé');
        $item = $this->findBySlug($slug);

        // Si l'utilisateur n'a pas déjà liké ce fichier on le rajoute
        if (!$item->getLikes()->contains($this->user))
            $item->addLike($this->user);
        // Si l'utilisateur avait précédemment unliké, on l'enlève
        if ($item->getDislikes()->contains($this->user))
            $item->removeDislike($this->user);

        $this->em->flush();
    }

    /**
     * @Route\View()
     */
    protected function dislike($slug, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER') && !$auth)
            throw new AccessDeniedException('Accès refusé');
        $item = $this->findBySlug($slug);

        // Si l'utilisateur n'a pas déjà unliké ce fichier on le rajoute
        if (!$item->getDislikes()->contains($this->user))
            $item->addDislike($this->user);
        // Si l'utilisateur avait précédemment liké, on l'enlève
        if ($item->getLikes()->contains($this->user))
            $item->removeLike($this->user);

        $this->em->flush();
    }

    /**
     * @Route\View()
     */
    protected function getLikes($slug)
    {
        $item = $this->findBySlug($slug);

        return $item->getLikes();
    }

    /**
     * @Route\View()
     */
    protected function getDislikes($slug)
    {
        $item = $this->findBySlug($slug);

        return $item->getDislikes();
    }

    /**
     * @Route\View()
     */
    protected function deleteLike($slug, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER') && !$auth)
            throw new AccessDeniedException('Accès refusé');
        $item = $this->findBySlug($slug);

        // Si l'utilisateur a déjà unliké on l'enlève
        if ($item->getLikes()->contains($this->user))
            $item->removeLike($this->user);
        $this->em->flush();
    }

    /**
     * @Route\View()
     */
    protected function deleteDislike($slug, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException('Accès refusé');
        $item = $this->findBySlug($slug);

        // Si l'utilisateur a déjà unliké on l'enlève
        if ($item->getDislikes()->contains($this->user))
            $item->removeDislike($this->user);
        $this->em->flush();
    }




















    // Gestion des sous ressources

    /**
     * Renvoie une sous ressource
     * Si $manyToMany = true, renvoie le paramètre $name de l'entité relié par une relation Many To Many
     * Sinon, renvoie les éléments d'une relation avec attribut en se basant sur la classe conjointe $name
     * @Route\View()
     */
    protected function getAllSub($slug, $name, $manyToMany = true)
    {
        $item = $this->findBySlug($slug);

        if ($manyToMany) {
            $method = 'get' . ucfirst($name) . 's';
            return $item->$method();
        }
        else {
            $repo = $this->em->getRepository('KIUpontBundle:' . $this->namespace . $this->className . $name);
            return $repo->findBy(array(strtolower($this->className) => $item));
        }
    }

    /**
     * @Route\View()
     */
    protected function getOneSub($slug, $name, $id)
    {
        $item = $this->findBySlug($slug);

        if (preg_match('#^\d+$#', $id))
            $filter = 'id';
        else
            $filter = 'slug';

        $this->switchClass($name);
        $return = $this->repo->findOneBy(array(strtolower($this->save) => $item, $filter => $id));

        if(!$return instanceof $this->class)
            throw new NotFoundHttpException('Objet ' . $this->className . ' non trouvée');

        $this->switchClass();
        return $return;
    }

    /**
     * @Route\View()
     */
    protected function patchSub($slug, $name, $id, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_MODO') && !$auth)
            throw new AccessDeniedException('Accès refusé');

        // On n'en a pas besoin ici mais on vérifie que l'item parent existe bien
        $this->findBySlug($slug);

        $this->switchClass($name);
        $item = $this->findBySlug($id);
        $return = $this->processForm($item);
        $this->switchClass();
        return $this->postView($return);
    }

    /**
     * @Route\View()
     */
    protected function deleteSub($slug, $name, $id, $auth = false)
    {
        if (!$this->get('security.context')->isGranted('ROLE_MODO') && !$auth)
            throw new AccessDeniedException('Accès refusé');

        // On n'en a pas besoin ici mais on vérifie que l'item parent existe bien
        $this->findBySlug($slug);

        $this->switchClass($name);
        $item = $this->findBySlug($id);
        $this->em->remove($item);
        $this->switchClass();

        $this->em->flush();
    }

    protected function subPostView($data, $slug, $route)
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
                        $route,
                        array('slug' => $slug, 'id' => $data['item']->getSlug()),
                        true
                    )
                )
            );
        }
    }
}
