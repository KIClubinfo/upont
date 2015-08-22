<?php

namespace KI\PublicationBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\CoreBundle\Controller\SubresourceController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExercicesController extends SubresourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Course', 'Publication');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les annales",
     *  output="KI\PublicationBundle\Entity\Exercice",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     */
    public function getCourseExercicesAction($slug) { return $this->getAllSub($slug, 'Exercice'); }

    /**
     * @ApiDoc(
     *  description="Retourne une annale",
     *  output="KI\PublicationBundle\Entity\Exercice",
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
    public function getCourseExerciceAction($slug, $id) { return $this->getOneSub($slug, 'Exercice', $id); }

    /**
     * @ApiDoc(
     *  description="Télécharge une annale au format PDF",
     *  output="KI\PublicationBundle\Entity\Exercice",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Publications"
     * )
     * @Route\Get("/courses/{slug}/exercices/{id}/download")
     */
    public function downloadCourseExerciceAction($slug, $id)
    {
        $this->switchClass('Exercice');
        $exercice = $this->findBySlug($id);
        $this->switchClass();

        if (!file_exists($exercice->getAbsolutePath())) {
            throw new NotFoundHttpException('Fichier PDF non trouvé');
        }

        // On lit le fichier PDF
        $response = new Response();
        $filepath = $exercice->getAbsolutePath();
        $course   = $exercice->getCourse();
        $filename = '('.$course->getDepartment().') '.$course->getName().' - '.$exercice->getName().'.pdf';

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filepath));
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'";');
        $response->headers->set('Content-length', filesize($filepath));

        $response->sendHeaders();
        return $response->setContent(readfile($filepath));
    }

    /**
     * @ApiDoc(
     *  description="Crée une annale",
     *  input="KI\PublicationBundle\Form\ExerciceType",
     *  output="KI\PublicationBundle\Entity\Exercice",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Publications"
     * )
     * @Route\Post("/courses/{slug}/exercices")
     */
    public function postCourseExerciceAction($slug, Request $request)
    {
        $course = $this->findBySlug($slug);

        $this->switchClass('Exercice');
        $return = $this->postData($this->is('USER'));

        if ($return['code'] != 400) {
            // On règle tout comme on veut
            $return['item']->setCourse($course);
            $return['item']->setValid($this->is('MODO'));

            // On vérifie que le fichier est là
            if (!$request->files->has('file')) {
                throw new BadRequestHttpException('Aucun fichier présent');
            }

            // On sauvegarde tout avec le fichier au passage
            $this->manager->flush();
            $this->get('ki_publication.listener.exercice')->postPersist($return['item']);
        }
        $this->switchClass();

        return $this->postView($return, $course);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une annale",
     *  input="KI\PublicationBundle\Form\ExerciceType",
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
    public function patchCourseExerciceAction($slug, $id)
    {
        return $this->patchSub($slug, 'Exercice', $id, $this->is('MODO'));
    }

    /**
     * @ApiDoc(
     *  description="Supprime une annale",
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
    public function deleteCourseExerciceAction($slug, $id)
    {
        $exercice = $this->getOneSub($slug, 'Exercice', $id);
        return $this->deleteSub($slug, 'Exercice', $id, $this->user == $exercice->getUploader() || $this->is('MODO'));
    }
}
