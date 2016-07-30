<?php

namespace KI\PublicationBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use KI\CoreBundle\Controller\ResourceController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class CoursesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Course', 'Publication');
    }

    /**
     * @ApiDoc(
     *  description="Parse l'emploi du temps emploidutemps.enpc.fr",
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     * @Route\Head("/courses")
     */
    public function parseCoursesAction()
    {
        $this->get('ki_publication.helper.courseparser')->updateCourses();
        return $this->json(null, 202);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les cours disponibles",
     *  output="KI\PublicationBundle\Entity\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getCoursesAction(Request $request)
    {
        if ($request->query->has('exercices')) {
            return $this->getAll(null, 'exercices');
        }
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un cours",
     *  output="KI\PublicationBundle\Entity\Course",
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
     *  input="KI\PublicationBundle\Form\CourseType",
     *  output="KI\PublicationBundle\Entity\Course",
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
     *  input="KI\PublicationBundle\Form\CourseType",
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
        $repository = $this->manager->getRepository('KIPublicationBundle:CourseUser');

        foreach ($repository->findByCourse($course) as $courseUser) {
            $this->manager->remove($courseUser);
        }

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
    public function postCourseUserAction($slug, Request $request) {
        $course = $this->findBySlug($slug);

        $group = $request->request->get('group', 0);
        $this->get('ki_publication.helper.course')->linkCourseUser($course, $this->user, $group);
        return $this->json(null, 204);
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
        $course = $this->findBySlug($slug);
        $this->get('ki_publication.helper.course')->unlinkCourseUser($course, $this->user);
        return $this->json(null, 204);
    }
}
