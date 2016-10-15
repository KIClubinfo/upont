<?php

namespace KI\CoreBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DefaultController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'User');
    }

    /**
     * @ApiDoc(
     *  description="Procédure de nettoyage de la BDD à lancer régulièrement",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/clean")
     * @Method("GET")
     */
    public function cleanAction()
    {
        $this->get('ki_core.helper.cleaning')->clean();
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Avoir du café",
     *  statusCodes={
     *   418="Je suis une théière",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/coffee")
     * @Method("GET")
     */
    public function coffeeAction()
    {
        $engine = $this->get('templating');
        $content = $engine->render('KICoreBundle::coffee.html.twig');
        return $this->htmlResponse($content, 418);
    }

    /**
     * @ApiDoc(
     *  description="Let's get dirty !",
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/dirty")
     * @Method("GET")
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/login", name="login")
     * @Method("POST")
     */
    public function loginAction()
    {
        return $this->json(null);
    }

    /**
     * @ApiDoc(
     *  description="Mettre le serveur en mode maintenance",
     *  parameters={
     *   {
     *    "name"="until",
     *    "dataType"="integer",
     *    "required"=false,
     *    "description"="La date prévue de remise en route du service (timestamp)"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     * @Route("/maintenance")
     * @Method("POST")
     */
    public function maintenanceLockAction(Request $request)
    {
        $this->trust($this->is('ADMIN'));
        $until = $request->request->has('until') ? (string)$request->request->get('until') : '';
        $this->get('ki_core.service.maintenance')->lock($until);
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Sortir le serveur du mode maintenance",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     * @Route("/maintenance")
     * @Method("DELETE")
     */
    public function maintenanceUnlockAction()
    {
        $this->trust($this->is('ADMIN'));
        $this->get('ki_core.service.maintenance')->unlock();
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Pinger l'API",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/ping", name="ping")
     * @Method({"HEAD", "GET"})
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/search")
     * @Method("POST")
     */
    public function searchAction(Request $request)
    {
        $this->trust($this->is('USER'));

        if (!$request->request->has('search')) {
            throw new BadRequestHttpException('Critère de recherche manquant');
        }
        $search = $request->request->get('search');
        $searchService = $this->get('ki_core.service.search');
        list($category, $criteria) = $searchService->analyzeRequest($search);

        return $this->json($searchService->search($category, $criteria));
    }

    /**
     * @ApiDoc(
     *  description="Retourne la version de uPont",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/version")
     * @Method("GET")
     */
    public function versionAction()
    {
        return $this->json($this->get('ki_core.service.version')->getVersion());
    }

    /**
     * @ApiDoc(
     *  description="Retourne la configuration de uPont",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route("/config")
     * @Method("GET")
     */
    public function configAction(Request $request)
    {
        return $this->json([
            "studentNetwork" => IpUtils::checkIp($request->getClientIp(), [
                "172.24.0.0-172.24.0.255",
                "172.24.20.0-172.24.60.255",
                "172.24.100.0-172.24.200.255",
                "195.221.194.14-195.221.194.14",
            ])
        ]);
    }
}
