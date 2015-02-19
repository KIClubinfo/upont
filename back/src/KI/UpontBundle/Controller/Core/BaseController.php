<?php

namespace KI\UpontBundle\Controller\Core;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Fonctions génériques
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

    // Recherche une entité selon son slug
    protected function findBySlug($slug)
    {
        if (preg_match('#^[0-9]+$#', $slug)) {
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
}
