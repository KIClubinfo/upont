<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Fonctions génériques
abstract class BaseController extends Controller
{
    protected $class;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $manager;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;
    protected $form;
    protected $saveClass;
    protected $saveForm;
    /**
     * @var User
     */
    protected $user = null;

    /**
     * Initialise le controleur
     */
    public function setUser()
    {
        $token = $this->get('security.token_storage')->getToken();
        $this->user = $token ? $token->getUser() : null;
    }

    /**
     * Génère une réponse au format JSON en parsant les propriétés avec le FOSRestBundle
     * @param  mixed $data    Le contenu à renvoyer
     * @param  int   $status    Le code d'erreur HTTP à renvoyer
     * @param  array $headers Des headers spécifiques si nécéssaire
     * @return Response
     */
    public function json($data, $status = 200, $headers = [], $context = [])
    {
        return new JsonResponse(
            $this->get('jms_serializer')->serialize($data, 'json'),
            $status,
            $headers,
            true
        );
    }

    /**
     * Génère une réponse plain text
     * @param  mixed $data    Le contenu à renvoyer
     * @param  int   $code    Le code d'erreur HTTP à renvoyer
     * @param  array $headers Des headers spécifiques si nécéssaire
     * @return Response
     */
    public function htmlResponse($data, $code = 200, array $headers = [])
    {
        return new Response($data, $code, $headers);
    }

    /**
     * Génère la réponse relative au traitement d'un formulaire
     * @param  array  $data   Le formulaire traité
     * @param  object $parent Éventuellement l'objet parent
     * @return Response
     */
    public function formJson($data)
    {
        switch ($data['code']) {
            case 400:
                return $this->json($data['form'], $data['code']);
            case 204:
            default:
                return $this->json($data['item'], $data['code']);
        }
    }

    /**
     * Retourne une configuration de uPont
     * @param  string  $path   La clé de configuration
     * @return string
     */
    public function getConfig($path)
    {
        $config = $this->container->getParameter('upont');

        if (!empty($path)) {
            $keys = preg_split('/[\.]/', $path);
            foreach ($keys as $key) {
                if (isset($config[$key])) {
                    $config = $config[$key];
                } else {
                    throw new \OutOfBoundsException;
                }
            }
        }

        return $config;
    }

    /**
     * Initialise le controleur de base
     * @param string $class  Le nom de la classe sur laquelle se baser
     * @param string $bundle Le nom du bundle dans lequel se trouve cette classe
     */
    public function initialize($class, $form)
    {
        $this->class      = $class;

        $this->manager    = $this->getDoctrine()->getManager();
        $this->repository = $this->manager->getRepository($class);
        $this->form       = $form;

        $this->setUser();
    }

    /**
     * Permet de changer le type d'objet sur lequel s'appuie le controleur
     * @param  string $class Le nom de la nouvelle classe. Si laissé à null,
     * revient à la classe précédent(celle-ci est sauvegardée à chaque changement)
     */
    protected function switchClass($class = null, $form = null)
    {
        // On garde en mémoire la classe précédente
        if ($class === null) {
            $class = $this->saveClass;
            $form = $this->saveForm;
        } else {
            $this->saveClass = $this->class;
            $this->saveForm = $this->form;
        }

        // À priori, une sous ressource garde le même namespace
        $this->initialize($class, $form);
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
            $item = $this->repository->findOneById($slug);
        } else {
            if ($this->class == User::class) {
                $item = $this->repository->findOneByUsername($slug);
            } else {
                $item = $this->repository->findOneBySlug($slug);
            }
        }
        if (!$item instanceof $this->class) {
            throw new NotFoundHttpException('Objet '.$this->class.' non trouvé');
        }

        return $item;
    }

    /**
     * Sert à checker si l'user actuel est membre du club au nom duquel il poste
     * @param  string $club
     * @return boolean
     */
    protected function isClubMember($club = null)
    {
        if ($this->is('ADMISSIBLE')) {
            return false;
        }

        // On vérifie que la requete est valide.
        // Si aucun club n'est précisé, c'est qu'on publie à son nom
        // (par exemple message perso) donc ok
        $request = $this->get('request_stack')->getCurrentRequest()->request;
        if (!$request->has('authorClub') && $club === null) {
            return $this->is('USER');
        }

        $repo = $this->manager->getRepository(Club::class);
        $club = $repo->findOneBySlug($request->has('authorClub') ? $request->get('authorClub') : $club);

        if (!$club) {
            return false;
        }

        // On vérifie que l'utilisateur fait bien partie du club
        return $this->get('App\Service\PermissionService')->isClubMember($this->user, $club);
    }

    /**
     * Sert à checker si l'user courant est membre du foyer actuel
     * @return boolean
     */
    protected function isFoyerMember()
    {
        return $this->isClubMember('foyer')
        && in_array($this->user->getPromo(), $this->getConfig('foyer.trust'));
    }

    /**
     * Permet de savoir si un utilisateur a un rôle ou non
     * @param  string $role
     * @return boolean
     */
    protected function is($role)
    {
        return $this->get('security.authorization_checker')->isGranted('ROLE_' . $role);
    }

    /**
     * Éjecte tous les utilisateurs ne respectant pas la condition
     * @param  boolean $bool
     * @return boolean
     */
    protected function trust($bool)
    {
        if ($this->user && $bool || $this->is('ADMIN')) {
            return;
        }

        throw new AccessDeniedException('Accès refusé');
    }
}
