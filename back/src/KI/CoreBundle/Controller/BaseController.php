<?php

namespace KI\CoreBundle\Controller;

use KI\UserBundle\Controller\CoreController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use KI\UserBundle\Entity\Notification;
use KI\UserBundle\Entity\Club;

// Fonctions génériques
class BaseController extends CoreController
{
    protected $class;
    protected $bundle;
    protected $className;
    protected $manager;
    protected $repository;
    protected $form;
    protected $save;

    protected $repo;
    protected $em;
    /**
     * Initialise le controleur de base
     * @param string $class  Le nom de la classe sur laquelle se baser
     * @param string $bundle Le nom du bundle dans lequel se trouve cette classe
     */
    public function initialize($class, $bundle)
    {
        $this->class      = 'KI\\'.$bundle.'Bundle\Entity\\'.$class;
        $this->bundle     = $bundle;
        $this->className  = $class;

        $this->manager    = $this->getDoctrine()->getManager();
        $this->repository = $this->manager->getRepository('KI'.$bundle.'Bundle:'.$class);
        $this->form       = 'KI\\'.$bundle.'Bundle\Form\\'.$class.'Type';

        parent::setUser();

        // TODO erase legacy
        $this->repo = $this->repository;
        $this->em = $this->manager;
    }

    // Permet de changer le repo actuel. Si $class non précisé, revient au précédent
    protected function switchClass($class = null)
    {
        // On garde en mémoire la classe précédente
        if ($class === null)
            $class = $this->save;
        else
            $this->save = $this->className;

        // À priori, une sous ressource garde le même namespace
        $this->initialize($class, str_replace('\\', '', $this->namespace));
    }

    /**
     * Génère une réponse au format JSON en parsant les propriétés avec le FOSRestBundle
     * @param  mixed $data    Le contenu à renvoyer
     * @param  int   $code    Le code d'erreur HTTP à renvoyer
     * @param  array $headers Des headers spécifiques si nécéssaire
     * @return Response
     */
    public function restResponse($data, $code = 200, array $headers = array())
    {
        return new Response(
            $this->get('jms_serializer')->serialize($data, 'json'),
            $code,
            $headers
        );
    }

    /**
     * Génère une réponse au format JSON en parsant les propriétés avec le FOSRestBundle
     * @param  array $data    Le contenu à renvoyer
     * @param  int   $code    Le code d'erreur HTTP à renvoyer
     * @param  array $headers Des headers spécifiques si nécéssaire
     * @return JsonResponse
     */
    public function jsonResponse($data, $code = 200, array $headers = array())
    {
        return new JsonResponse($data, $code, $headers);
    }

    /**
     * Génère une réponse plain text
     * @param  mixed $data    Le contenu à renvoyer
     * @param  int   $code    Le code d'erreur HTTP à renvoyer
     * @param  array $headers Des headers spécifiques si nécéssaire
     * @return Response
     */
    public function htmlResponse($data, $code = 200, array $headers = array())
    {
        return new Response($data, $code, $headers);
    }

    /**
     * Recherche une entité selon son slug
     * @param  string $slug
     * @return mixed
     * @throws NotFoundHttpException Si l'entité n'est pas trouvée
     */
    protected function findBySlug($slug)
    {
        if (!method_exists($this->class, 'setSlug')) {
            $item = $this->repo->findOneById($slug);
        } else {
            if ($this->className == 'User') {
                $item = $this->repo->findOneByUsername($slug);
            } else {
                $item = $this->repo->findOneBySlug($slug);
            }
        }
        if (!$item instanceof $this->class) {
            throw new NotFoundHttpException('Objet '.$this->className.' non trouvé');
        }

        return $item;
    }
}
