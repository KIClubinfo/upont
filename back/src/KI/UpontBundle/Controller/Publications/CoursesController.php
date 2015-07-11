<?php

namespace KI\UpontBundle\Controller\Publications;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\UpontBundle\Entity\Users\CourseUser;
use KI\UpontBundle\Form\Users\CourseUserType;

class CoursesController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Course', 'Publications');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les cours disponibles",
     *  output="KI\UpontBundle\Entity\Publications\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getCoursesAction()
    {
        if ($this->getRequest()->query->has('exercices'))
            return $this->getAll(null, 'exercices');
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un cours",
     *  output="KI\UpontBundle\Entity\Publications\Course",
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
    public function getCourseAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée un cours",
     *  input="KI\UpontBundle\Form\Publications\CourseType",
     *  output="KI\UpontBundle\Entity\Publications\Course",
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
    public function postCourseAction() { return $this->post(); }

    /**
     * @ApiDoc(
     *  description="Modifie un cours",
     *  input="KI\UpontBundle\Form\Publications\CourseType",
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
    public function patchCourseAction($slug) { return $this->patch($slug); }

    /**
     * @ApiDoc(
     *  description="Supprime un cours",
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
    public function deleteCourseAction($slug)
    {
        // Les cours possèdent plein de sous propriétés, il faut faire gaffe à toutes les supprimer
        $course = $this->getOne($slug);
        $repo = $this->em->getRepository('KIUpontBundle:Users\CourseUser');

        foreach ($repo->findByCourse($course) as $courseUser)
            $this->em->remove($courseUser);

        return $this->delete($slug);
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un utilisateur au cours",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Post("/courses/{slug}/attend")
     */
    public function postCourseUserAction($slug) {
        $course = $this->findBySlug($slug);

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->em->getRepository('KIUpontBundle:Users\CourseUser');
        $link = $repoLink->findBy(array('course' => $course, 'user' => $this->user));

        // On crée la relation si elle n'existe pas déjà
        if (count($link) != 0)
            throw new BadRequestHttpException('La relation entre Course et User existe déjà');

        // Création de l'entité relation
        $link = new CourseUser();
        $link->setCourse($course);
        $link->setUser($this->user);

        if ($this->getRequest()->request->has('group')) {
            $link->setGroup($this->getRequest()->request->get('group'));

            if (!in_array($link->getGroup(), $link->getCourse()->getGroups()))
                throw new BadRequestHttpException('Ce groupe n\'existe pas.');
        }

        $this->em->persist($link);
        $this->em->flush();

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Retire la demande d'inscription",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Delete("/courses/{slug}/attend")
     */
    public function deleteCourseUserAction($slug) {
        $user = $this->get('security.context')->getToken()->getUser();
        $course = $this->findBySlug($slug);

        $repoLink = $this->em->getRepository('KIUpontBundle:Users\CourseUser');
        $link = $repoLink->findBy(array('course' => $course, 'user' => $user));

        if (count($link) != 1)
            throw new NotFoundHttpException('Relation entre Course et User non trouvée');

        $this->em->remove($link[0]);
        $this->em->flush();

        return $this->jsonResponse(null, 204);
    }
}
