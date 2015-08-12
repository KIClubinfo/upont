<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExperiencesController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Experience', 'User');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les experiences",
     *  output="KI\UserBundle\Entity\Experience",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getExperiencesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une expérience",
     *  output="KI\UserBundle\Entity\Experience",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getExperienceAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée une expérience",
     *  input="KI\UserBundle\Form\ExperienceType",
     *  output="KI\UserBundle\Entity\Experience",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function postExperienceAction()
    {
        $return = $this->postData($this->get('security.context')->isGranted('ROLE_USER'));

        if ($return['code'] == 201) {
            // On modifie légèrement la ressource qui vient d'être créée
            $return['item']->setUser($this->container->get('security.context')->getToken()->getUser());
        }

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une expérience",
     *  input="KI\UserBundle\Form\ExperienceType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function patchExperienceAction($slug) { return $this->patch($slug); }

    /**
     * @ApiDoc(
     *  description="Supprime une expérience",
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
    public function deleteExperienceAction($slug) { return $this->delete($slug); }

    /**
     * @ApiDoc(
     *  description="Retourne toutes les expériences postées par un utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/users/{slug}/experiences")
     */
    public function getExperiencesUserAction($slug)
    {
        $repo = $this->manager->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($slug);

        if (!$user instanceof \KI\UserBundle\Entity\User) {
            $up = new NotFoundHttpException('Utilisateur non trouvé');
            throw $up; // ahah
        }

        return $this->restResponse($this->repository->findByUser($user));
    }
}
