<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Course;
use App\Entity\CourseUser;
use App\Form\CourseType;
use App\Helper\CourseHelper;
use App\Helper\CourseParserHelper;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class CoursesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Course::class, CourseType::class);
    }

    /**
     * @ApiDoc(
     *  description="Parse l'emploi du temps emploidutemps.enpc.fr",
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     * @Route("/courses")
     * @Method("HEAD")
     */
    public function parseCoursesAction(CourseParserHelper $courseParserHelper)
    {
        $courseParserHelper->updateCourses();
        return $this->json(null, 202);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les cours disponibles",
     *  output="App\Entity\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses")
     * @Method("GET")
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
     *  output="App\Entity\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}")
     * @Method("GET")
     */
    public function getCourseAction($slug)
    {
        $course =  $this->getOne($slug);

        return $this->json($course);
    }

    /**
     * @ApiDoc(
     *  description="Crée un cours",
     *  input="App\Form\CourseType",
     *  output="App\Entity\Course",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses")
     * @Method("POST")
     */
    public function postCourseAction()
    {
        $data = $this->post();

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un cours",
     *  input="App\Form\CourseType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}")
     * @Method("PATCH")
     */
    public function patchCourseAction($slug)
    {
        $data = $this->patch($slug);

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un cours",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}")
     * @Method("DELETE")
     */
    public function deleteCourseAction($slug)
    {
        // Les cours possèdent plein de sous propriétés, il faut faire gaffe à toutes les supprimer
        $course = $this->getOne($slug);
        $repository = $this->manager->getRepository(CourseUser::class);

        foreach ($repository->findByCourse($course) as $courseUser) {
            $this->manager->remove($courseUser);
        }

        $this->delete($slug);

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un utilisateur au cours",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}/attend")
     * @Method("POST")
     */
    public function postCourseUserAction(CourseHelper $courseHelper, Request $request, $slug)
    {
        $course = $this->findBySlug($slug);

        $group = $request->request->get('group', 0);
        $courseHelper->linkCourseUser($course, $this->user, $group);
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
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}/attend")
     * @Method("DELETE")
     */
    public function deleteCourseUserAction(CourseHelper $courseHelper, $slug)
    {
        $course = $this->findBySlug($slug);
        $courseHelper->unlinkCourseUser($course, $this->user);
        return $this->json(null, 204);
    }


}
