<?php

namespace KI\UpontBundle\Controller\Users;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FacegamesController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Facegame', 'Users');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les jeux",
     *  output="KI\UpontBundle\Entity\Users\Facegame",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getFacegamesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un jeu",
     *  output="KI\UpontBundle\Entity\Users\Facegame",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getFacegameAction($id, $auth = false)
    {
        if (isset($this->user) && $this->get('security.context')->isGranted('ROLE_EXTERIEUR') && !$auth)
            throw new AccessDeniedException();
        $game = $this->repo->findOneById($id);
        if (!isset($game))
            throw new NotFoundHttpException('Jeu non trouvé');
        return $game;
    }

    // On remplit la listUsers selon les paramètres rentrés
    protected function postListUsersAction($return)
    {
        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $list = $return['item']->getListUsers();
        $max = 20;
        $nbUsers = 5;
        while(count($list) < $nbUsers) {
            $id = rand(1, $max);
            $user = $repo->findOneById($id);
            if(isset($user)
                && $user->getUsername() != $return['item']->getUser()->getUsername()
                && $user->getImage() != null)
                $list[] = $repo->findOneById($id)->getUsername();
        }

        $return['item']->setListUsers($list);
    }

    /**
     * @ApiDoc(
     *  description="Crée un jeu",
     *  input="KI\UpontBundle\Form\Users\FacegameType",
     *  output="KI\UpontBundle\Entity\Users\Facegame",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function postFacegameAction() {
        $return = $this->partialPost($this->get('security.context')->isGranted('ROLE_USER'));
        if ($return['code'] == 400)
            return RestView::create($return['form'], 400);

        else if ($return['code'] == 201) {
            // On modifie légèrement la ressource qui vient d'être créée
            $return['item']->setDate(time());
            $return['item']->setUser($this->container->get('security.context')->getToken()->getUser());

            $this->postListUsersAction($return);

            $this->em->flush();
            return RestView::create($return['item'],
                201,
                array(
                    'Location' => $this->generateUrl(
                        'get_'.strtolower($this->className),
                        array('id' => $return['item']->getId()),
                        true
                    )
                )
            );
        }

        else {
            $this->em->flush();
            return RestView::create(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Supprime un jeu",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function deleteFacegameAction($id, $auth = false)
    {
        if (isset($this->user) &&
            ((!$this->get('security.context')->isGranted('ROLE_MODO')
                || $this->get('security.context')->isGranted('ROLE_ADMISSIBLE')
                || $this->get('security.context')->isGranted('ROLE_EXTERIEUR'))
            && !$auth))
            throw new AccessDeniedException('Accès refusé');
        $item = $this->repo->findOneById($id);
        if (!isset($item))
            throw new NotFoundHttpException('Jeu non trouvé');
        $this->em->remove($item);
        $this->em->flush();
    }
}
