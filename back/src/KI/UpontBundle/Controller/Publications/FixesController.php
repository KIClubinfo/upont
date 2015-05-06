<?php

namespace KI\UpontBundle\Controller\Publications;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FixesController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Fix', 'Publications');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les tâches de dépannage",
     *  output="KI\UpontBundle\Entity\Publications\Fix",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getFixesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une tâche de dépannage",
     *  output="KI\UpontBundle\Entity\Publications\Fix",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getFixAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée une tâche de dépannage",
     *  input="KI\UpontBundle\Form\Publications\FixType",
     *  output="KI\UpontBundle\Entity\Publications\Fix",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function postFixAction()
    {
        $return = $this->partialPost($this->get('security.context')->isGranted('ROLE_USER'));

        if ($return['code'] == 201) {
            // On modifie légèrement la ressource qui vient d'être créée
            $user = $this->get('security.context')->getToken()->getUser();
            $return['item']->setUser($user);
        }

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une tâche de dépannage",
     *  input="KI\UpontBundle\Form\Publications\FixType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function patchFixAction($slug) { return $this->patch($slug); }

    /**
     * @ApiDoc(
     *  description="Supprime une tâche de dépannage",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function deleteFixAction($slug) { return $this->delete($slug); }

    /**
     * @ApiDoc(
     *  description="Ajoute un respo à la tâche de dépannage",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Post("/fixes/{slug}/respos/{id}")
     */
    public function addRespoAction($slug, $id)
    {
        $fix = $this->findBySlug($slug);

        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $respo = $repo->findOneByUsername($id);

        if (!$respo instanceof \KI\UpontBundle\Entity\Users\User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if ($fix->getListRespos()->contains($respo)) {
            throw new BadRequestHttpException('Cette personne est déjà responsable de cette tâche');
        } else {
            $fix->addListRespo($respo);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }


    /**
     * @ApiDoc(
     *  description="Supprime un respo de la tâche de dépannage",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Delete("/fixes/{slug}/respos/{id}")
     */
    public function deleteRespoAction($slug, $id)
    {
        $fix = $this->findBySlug($slug);

        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $respo = $repo->findOneByUsername($id);

        if (!$respo instanceof \KI\UpontBundle\Entity\Users\User)
            throw new NotFoundHttpException('Utilisateur non trouvé');

        if (!$fix->getListRespos()->contains($respo)) {
            throw new BadRequestHttpException('Cette personne n\'est pas responsable de cette tâche');
        } else {
            $fix->removeListRespo($respo);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retourne les respos liés à la tâche",
     *  output="KI\UpontBundle\Entity\Users/User",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Get("/fixes/{slug}/respos")
     */
    public function getRespoAction($slug) { return $this->findBySlug($slug)->getListRespos(); }
}