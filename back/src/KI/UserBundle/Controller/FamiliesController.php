<?php

namespace KI\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FamiliesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Family', 'User');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les familles",
     *  output="KI\UserBundle\Entity\Family",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/families")
     * @Method("GET")
     */
    public function getFamiliesAction()
    {
        return $this->getAll($this->is('EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Retourne une famille",
     *  output="KI\UserBundle\Entity\Family",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/families/{slug}")
     * @Method("GET")
     */
    public function getFamilyAction($slug)
    {
        $family = $this->getOne($slug, $this->is('EXTERIEUR'));

        return $this->json($family);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une famille",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/families/{slug}")
     * @Method("DELETE")
     */
    public function deleteClubAction($slug)
    {
        $repoUser = $this->manager->getRepository('KIUserBundle:User');
        $family = $this->findBySlug($slug);
        $link = $repoUser->findBy(['family' => $family]);

        foreach ($link as $user) {
            $family->removeUsers($slug);
            $this->manager->persist($family);
            $this->manager->flush();
        }

        $this->delete($family);

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Liste les membres d'une famille",
     *  output="KI\UserBundle\Entity\Family",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/families/{slug}/users")
     * @Method("GET")
     */
    public function getFamilyUsersAction($slug)
    {
        $family = $this->repository->findBySlug($slug);
        $members = $family->getUsers();

        return $this->json($members);
    }
}
