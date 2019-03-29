<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Course;
use App\Entity\Exercice;
use App\Form\CourseType;
use App\Form\ExerciceType;
use App\Listener\ExerciceListener;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ExercicesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Course::class, CourseType::class);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Liste les annales",
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
     * @Route("/courses/{slug}/exercices", methods={"GET"})
     */
    public function getCourseExercicesAction(Course $course)
    {
        $exercices = $course->getExercices();

        return $this->json($exercices);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retourne une annale",
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
     * @Route("/courses/{slug}/exercices/{exercice_slug}", methods={"GET"})
     * @ParamConverter("exercice", options={"mapping": {"exercice_slug": "slug"}})
     */
    public function getCourseExerciceAction(Course $course, Exercice $exercice)
    {
        return $this->json($exercice);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Télécharge une annale au format PDF",
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
     * @Route("/courses/{slug}/exercices/{exercice_slug}/download", methods={"GET"})
     * @ParamConverter("exercice", options={"mapping": {"exercice_slug": "slug"}})
     */
    public function downloadCourseExerciceAction(Course $course, Exercice $exercice)
    {
        if (!file_exists($exercice->getAbsolutePath())) {
            throw new NotFoundHttpException('Fichier PDF non trouvé');
        }

        // On lit le fichier PDF
        $response = new Response();
        $filepath = $exercice->getAbsolutePath();
        $course = $exercice->getCourse();
        $filename = '(' . $course->getDepartment() . ') ' . $course->getName() . ' - ' . $exercice->getName() . '.pdf';

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filepath));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
        $response->headers->set('Content-length', filesize($filepath));

        $response->sendHeaders();
        return $response->setContent(readfile($filepath));
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Crée une annale",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file"
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
     * @Route("/courses/{slug}/exercices", methods={"POST"})
     */
    public function postCourseExerciceAction(Course $course, Request $request, ExerciceListener $exerciceListener)
    {
        $this->switchClass(Exercice::class, ExerciceType::class);
        $data = $this->post($this->is('USER'), false);

        if ($data['code'] != 400) {
            // On règle tout comme on veut
            $data['item']->setCourse($course);
            $data['item']->setValid($this->is('MODO'));

            // On vérifie que le fichier est là
            if (!$request->files->has('file')) {
                throw new BadRequestHttpException('Aucun fichier présent');
            }

            // On sauvegarde tout avec le fichier au passage
            $this->manager->flush();
            $exerciceListener->postPersist($data['item']);
        }
        $this->switchClass();

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Modifie une annale",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file",
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
     * @Route("/courses/{slug}/exercices/{exercice_slug}", methods={"PATCH"})
     * @ParamConverter("exercice", options={"mapping": {"exercice_slug": "slug"}})
     */
    public function patchCourseExerciceAction(Course $course, Exercice $exercice)
    {
        $data = $this->patchItem($exercice);

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime une annale",
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
     * @Route("/courses/{slug}/exercices/{exercice_slug}", methods={"DELETE"})
     * @ParamConverter("exercice", options={"mapping": {"exercice_slug": "slug"}})
     */
    public function deleteCourseExerciceAction(Course $course, Exercice $exercice)
    {
        $this->deleteItem($exercice);

        return $this->json(null, 204);
    }
}
