<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Course;
use App\Entity\Exercice;
use App\Form\CourseType;
use App\Form\ExerciceType;
use App\Listener\ExerciceListener;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExercicesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Course::class, CourseType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les annales",
     *  output="App\Entity\Exercice",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}/exercices", methods={"GET"})
     */
    public function getCourseExercicesAction(Course $course)
    {
        $exercices = $course->getExercices();

        return $this->json($exercices);
    }

    /**
     * @ApiDoc(
     *  description="Retourne une annale",
     *  output="App\Entity\Exercice",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}/exercices/{exercice_slug}", methods={"GET"})
     * @ParamConverter("exercice", options={"mapping": {"exercice_slug": "slug"}})
     */
    public function getCourseExerciceAction(Course $course, Exercice $exercice)
    {
        return $this->json($exercice);
    }

    /**
     * @ApiDoc(
     *  description="Télécharge une annale au format PDF",
     *  output="App\Entity\Exercice",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Publications"
     * )
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
     * @ApiDoc(
     *  description="Crée une annale",
     *  input="App\Form\ExerciceType",
     *  output="App\Entity\Exercice",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
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
     * @ApiDoc(
     *  description="Modifie une annale",
     *  input="App\Form\ExerciceType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}/exercices/{exercice_slug}", methods={"PATCH"})
     * @ParamConverter("exercice", options={"mapping": {"exercice_slug": "slug"}})
     */
    public function patchCourseExerciceAction(Course $course, Exercice $exercice)
    {
        $data = $this->patchItem($exercice);

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une annale",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/courses/{slug}/exercices/{exercice_slug}", methods={"DELETE"})
     * @ParamConverter("exercice", options={"mapping": {"exercice_slug": "slug"}})
     */
    public function deleteCourseExerciceAction(Course $course, Exercice $exercice)
    {
        $this->deleteItem($exercice);

        return $this->json(null, 204);
    }
}
