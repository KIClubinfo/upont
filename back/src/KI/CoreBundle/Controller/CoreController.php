<?php

namespace KI\CoreBundle\Controller;

use KI\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CoreController extends Controller
{
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
     * Permet de savoir si un utilisateur a un rôle ou non
     * @param  string $role
     * @return boolean
     */
    protected function is($role)
    {
        return $this->get('security.authorization_checker')->isGranted('ROLE_'.$role);
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
}
