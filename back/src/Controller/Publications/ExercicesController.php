<?php

namespace App\Controller\Publications;

use App\Controller\SubresourceController;
use App\Entity\Course;
use App\Entity\Exercice;
use App\Form\CourseType;
use App\Form\ExerciceType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExercicesController extends SubresourceController
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
     * @Route("/courses/{slug}/exercices")
     * @Method("GET")
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
     * @Route("/courses/{slugParent}/exercices/{slugSub}")
     * @Method("GET")
     */
    public function getCourseExerciceAction($slugParent, $slugSub)
    {
        $exercice = $this->getOneSub($slugParent, 'Exercice', $slugSub);

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
     * @Route("/courses/{slugParent}/exercices/{slugSub}/download")
     * @Method("GET")
     */
    public function downloadCourseExerciceAction($slugParent, $slugSub)
    {
        $this->switchClass(Exercice::class, ExerciceType::class);
        $exercice = $this->findBySlug($slugSub);
        $this->switchClass();

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
     * @Route("/courses/{slug}/exercices")
     * @Method("POST")
     */
    public function postCourseExerciceAction($slug, Request $request)
    {
        $course = $this->findBySlug($slug);

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
            $this->get('ki_publication.listener.exercice')->postPersist($data['item']);
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
     * @Route("/courses/{slugParent}/exercices/{slugSub}")
     * @Method("PATCH")
     */
    public function patchCourseExerciceAction($slugParent, $slugSub)
    {
        $data = $this->patchSub($slugParent, 'Exercice', $slugSub, $this->is('MODO'));

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
     * @Route("/courses/{slugParent}/exercices/{slugSub}")
     * @Method("DELETE")
     */
    public function deleteCourseExerciceAction($slugParent, $slugSub)
    {
        $exercice = $this->getOneSub($slugParent, 'Exercice', $slugSub);

        $this->deleteSub($slugParent, 'Exercice', $slugSub, $this->user == $exercice->getUploader() || $this->is('MODO'));

        return $this->json(null, 204);
    }
}
