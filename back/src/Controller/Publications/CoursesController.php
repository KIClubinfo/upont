<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Course;
use App\Form\CourseType;
use App\Helper\CourseHelper;
use App\Helper\CourseParserHelper;
use App\Repository\CourseUserRepository;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CoursesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Course::class, CourseType::class);
    }

    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Parse l'emploi du temps emploidutemps.enpc.fr",
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
     * @Route("/courses", methods={"HEAD"})
     */
    public function parseCoursesAction(CourseParserHelper $courseParserHelper)
    {
        $courseParserHelper->updateCourses();
        return $this->json(null, 202);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Liste les cours disponibles",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
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
     * @Route("/courses", methods={"GET"})
     */
    public function getCoursesAction(Request $request)
    {
        if ($request->query->has('exercices')) {
            return $this->getAll(null);
        }
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retourne un cours",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/courses/{slug}", methods={"GET"})
     */
    public function getCourseAction($slug)
    {
        $course = $this->getOne($slug);

        return $this->json($course);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Crée un cours",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="department",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="semester",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="ects",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="active",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
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
     * @Route("/courses", methods={"POST"})
     */
    public function postCourseAction()
    {
        $data = $this->post();

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Modifie un cours",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="department",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="semester",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="ects",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number",
     *     ),
     *     @SWG\Parameter(
     *         name="active",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
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
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/courses/{slug}", methods={"PATCH"})
     */
    public function patchCourseAction($slug)
    {
        $data = $this->patch($slug);

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime un cours",
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
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/courses/{slug}", methods={"DELETE"})
     */
    public function deleteCourseAction($slug, CourseUserRepository $courseUserRepository)
    {
        // Les cours possèdent plein de sous propriétés, il faut faire gaffe à toutes les supprimer
        $course = $this->getOne($slug);

        foreach ($courseUserRepository->findByCourse($course) as $courseUser) {
            $this->manager->remove($courseUser);
        }

        $this->delete($slug);

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Ajoute un utilisateur au cours",
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
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/courses/{slug}/attend", methods={"POST"})
     */
    public function postCourseUserAction(CourseHelper $courseHelper, Request $request, $slug)
    {
        $course = $this->findBySlug($slug);

        $group = $request->request->get('group', 0);
        $courseHelper->linkCourseUser($course, $this->user, $group);
        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retire la demande d'inscription",
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
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/courses/{slug}/attend", methods={"DELETE"})
     */
    public function deleteCourseUserAction(CourseHelper $courseHelper, $slug)
    {
        $course = $this->findBySlug($slug);
        $courseHelper->unlinkCourseUser($course, $this->user);
        return $this->json(null, 204);
    }
}
