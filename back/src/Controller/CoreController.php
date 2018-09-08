<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Helper\CleaningHelper;
use App\Service\SearchService;
use App\Service\VersionService;

class CoreController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        // TODO remove this nonsense
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @ApiDoc(
     *  description="Procédure de nettoyage de la BDD à lancer régulièrement",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     * @Route("/clean", methods={"GET"})
     */
    public function cleanAction(CleaningHelper $cleaningHelper)
    {
        $cleaningHelper->clean();
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Avoir du café",
     *  statusCodes={
     *   418="Je suis une théière",
     *  },
     *  section="Général"
     * )
     * @Route("/coffee", methods={"GET"})
     */
    public function coffeeAction()
    {
        $engine = $this->get('templating');
        $content = $engine->render('coffee.html.twig');
        return $this->htmlResponse($content, 418);
    }

    /**
     * @ApiDoc(
     *  description="Let's get dirty !",
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     * @Route("/dirty", methods={"GET"})
     */
    public function dirtyAction()
    {
        return $this->redirect('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    }

    /**
     * Ceci sert juste à documenter cette route, le reste est géré par le LexikJWTAuthenticationBundle
     * @ApiDoc(
     *  description="Se loger et recevoir un JSON Web Token",
     *  requirements={
     *   {
     *    "name"="username",
     *    "dataType"="string",
     *    "description"="Le nom d'utilisateur (format : sept premières lettres du nom et première lettre du prénom)"
     *   },
     *   {
     *    "name"="password",
     *    "dataType"="string",
     *    "description"="Le mot de passe en clair"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Mauvaise combinaison username/password ou champ nom rempli",
     *   502="Erreur Proxy : l'utilisateur se connecte pour la première fois, mais le proxy DSI n'est pas configuré",
     *  },
     *  section="Général"
     * )
     * @Route("/login", name="login", methods={"POST"})
     */
    public function loginAction()
    {
        return $this->json(null);
    }

    /**
     * @ApiDoc(
     *  description="Pinger l'API",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     * @Route("/ping", name="ping", methods={"HEAD", "GET"})
     */
    public function pingAction()
    {
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Recherche au travers de tout le site",
     *  requirements={
     *   {
     *    "name"="search",
     *    "dataType"="string",
     *    "description"="Le critère de recherche"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     * @Route("/search", methods={"POST"})
     */
    public function searchAction(SearchService $searchService, Request $request)
    {
        $this->trust($this->is('USER'));

        if (!$request->request->has('search')) {
            throw new BadRequestHttpException('Critère de recherche manquant');
        }
        $search = $request->request->get('search');
        list($category, $criteria) = $searchService->analyzeRequest($search);

        return $this->json($searchService->search($category, $criteria));
    }

    /**
     * @ApiDoc(
     *  description="Retourne la version de uPont",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *  },
     *  section="Général"
     * )
     * @Route("/version", methods={"GET"})
     */
    public function versionAction(VersionService $versionService)
    {
        return $this->json($versionService->getVersion());
    }

    /**
     * @ApiDoc(
     *  description="Retourne la configuration de uPont",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *  },
     *  section="Général"
     * )
     * @Route("/config", methods={"GET"})
     */
    public function configAction(Request $request)
    {
        return $this->json(array_merge([
            'ip' => $request->getClientIp(),
            'studentNetwork' => IpUtils::checkIp($request->getClientIp(), [
               '127.0.0.1',
                '172.24.0.0/24',
                '172.24.20.0/22',
                '172.24.24.0/21',
                '172.24.32.0/20',
                '172.24.48.0/21',
                '172.24.56.0/22',
                '172.24.60.0/24',
                '172.24.100.0/22',
                '172.24.104.0/21',
                '172.24.112.0/20',
                '172.24.128.0/18',
                '172.24.192.0/21',
                '172.24.200.0/24',
                '195.221.194.14',
                '195.221.194.42',
                '195.221.194.43',
            ])
        ], $this->container->getParameter('upont')));
    }
}
