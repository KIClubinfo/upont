<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\CleaningHelper;
use App\Service\SearchService;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CoreController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        // TODO remove this nonsense
        $this->initialize(User::class, null);
    }


    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Procédure de nettoyage de la BDD à lancer régulièrement",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/clean", methods={"GET"})
     */
    public function cleanAction(CleaningHelper $cleaningHelper)
    {
        $cleaningHelper->clean();
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Avoir du café",
     *     @SWG\Response(
     *         response="418",
     *         description="Je suis une théière"
     *     )
     * )
     *
     * @Route("/coffee", methods={"GET"})
     */
    public function coffeeAction()
    {
        $engine = $this->get('templating');
        $content = $engine->render('coffee.html.twig');
        return $this->htmlResponse($content, 418);
    }

    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Let's get dirty !",
     *     @SWG\Response(
     *         response="202",
     *         description="Requête traitée mais sans garantie de résultat"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/dirty", methods={"GET"})
     */
    public function dirtyAction()
    {
        return $this->redirect('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    }

    /**
     * Ceci sert juste à documenter cette route, le reste est géré par le LexikJWTAuthenticationBundle
     * @Operation(
     *     tags={"Général"},
     *     summary="Se loger et recevoir un JSON Web Token",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Mauvaise combinaison username/password ou champ nom rempli"
     *     ),
     *     @SWG\Response(
     *         response="502",
     *         description="Erreur Proxy : l'utilisateur se connecte pour la première fois, mais le proxy DSI n'est pas configuré"
     *     )
     * )
     *
     * @Route("/login", name="login", methods={"POST"})
     */
    public function loginAction()
    {
        return $this->json(null);
    }

    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Pinger l'API",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/ping", name="ping", methods={"HEAD", "GET"})
     */
    public function pingAction()
    {
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Recherche au travers de tout le site",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
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
     * @Operation(
     *     tags={"Général"},
     *     summary="Retourne la version de uPont",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     )
     * )
     *
     * @Route("/version", methods={"GET"})
     */
    public function versionAction(ParameterBagInterface $params)
    {
        $versionInfo = $params->get('version-info');
        return $this->json($versionInfo);
    }

    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Retourne la configuration de uPont",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     )
     * )
     *
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
