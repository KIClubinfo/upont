<?php

namespace KI\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @Route\Get("/clean")
     */
    public function cleanAction()
    {
        $this->get('ki_core.helper.cleaning')->clean();
        return $this->jsonResponse(null, 204);
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
     * @Route\Get("/coffee")
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
     * @Route\Get("/dirty")
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
     * @Route\Post("/login")
     */
    public function loginAction()
    {
        return $this->restResponse(null);
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
     * @Route\Post("/maintenance")
     */
    public function maintenanceLockAction(Request $request)
    {
        $this->trust($this->is('ADMIN'));
        $until = $request->request->has('until') ? (string)$request->request->get('until') : '';
        $this->get('ki_core.service.maintenance')->lock($until);
        return $this->jsonResponse(null, 204);
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
     * @Route\Delete("/maintenance")
     */
    public function maintenanceUnlockAction()
    {
        $this->trust($this->is('ADMIN'));
        $this->get('ki_core.service.maintenance')->unlock();
        return $this->jsonResponse(null, 204);
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
     * @Route\Head("/ping")
     * @Route\Get("/ping")
     */
    public function pingAction()
    {
        return $this->jsonResponse(null, 204);
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
     * @Route\Post("/search")
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

        return $this->jsonResponse($searchService->search($category, $criteria));
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
     * @Route\Get("/version")
     */
    public function versionAction()
    {
        return $this->jsonResponse($this->get('ki_core.service.version')->getVersion());
    }
}
