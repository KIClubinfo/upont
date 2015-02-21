<?php

namespace KI\UpontBundle\Controller\Publications;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExercicesController extends \KI\UpontBundle\Controller\Core\SubresourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Course', 'Publications');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les annales",
     *  output="KI\UpontBundle\Entity\Publications\Exercices",
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
     *  output="KI\UpontBundle\Entity\Publications\Exercice",
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
     *  output="KI\UpontBundle\Entity\Publications\Exercice",
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

        if (!file_exists($exercice->getAbsolutePath()))
            throw new NotFoundHttpException('Fichier PDF non trouvé');

        // On lit le fichier PDF
        return new \Symfony\Component\HttpFoundation\Response(file_get_contents($exercice->getAbsolutePath()), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition: attachment; filename="' . $exercice->getCourse()->getDepartment() . '' . $exercice->getName() . '"'
        ));
    }

    /**
     * @ApiDoc(
     *  description="Crée une annale",
     *  input="KI\UpontBundle\Form\Publications\ExerciceType",
     *  output="KI\UpontBundle\Entity\Publications\Exercice",
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
     public function postCourseExerciceAction($slug) {
        $request = $this->getRequest();
        $course = $this->findBySlug($slug);
        $uploader = $this->container->get('security.context')->getToken()->getUser();

        $this->switchClass('Exercice');
        $return = $this->partialPost($this->get('security.context')->isGranted('ROLE_USER'));

        if ($return['code'] != 400) {
            // On règle tout comme on veut
            $return['item']->setDate(time());
            $return['item']->setUploader($uploader);
            $return['item']->setCourse($course);
            $return['item']->setValid($this->get('security.context')->isGranted('ROLE_MODO'));

            // On upload le fichier
            if (!$request->files->has('file'))
                throw new BadRequestHttpException('Aucun fichier présent');

            $this->em->flush();

            // On crée une notification
            // TODO récupérer par une requête avec JOIN, sans faire de boucle for ensuite
            $allUsers = $this->em->getRepository('KIUpontBundle:Users\User')->findAll();
            $users = array();

            foreach ($allUsers as $candidate) {
                if ($candidate->getCourses()->contains($return['item']))
                    $users[] = $candidate;
            }

            $this->notify(
                'notif_followed_annal',
                $return['item']->getName(),
                'Une annale pour le cours ' . $course->getName() . ' est maintenant disponible',
                'to',
                $users
            );

            $request->files->get('file')->move($return['item']->getBasePath(), $return['item']->getId() . '.pdf');
        }
        $this->switchClass();

        // FIXME route bizarre, ne comprends pas
        return $this->subPostView($return, $slug, 'get_course_exercice');
    }

    /**
     * @ApiDoc(
     *  description="Modifie une annale",
     *  input="KI\UpontBundle\Form\Publications\ExerciceType",
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
        return $this->patchSub($slug, 'Exercice', $id, $this->get('security.context')->isGranted('ROLE_MODO'));
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
        return $this->deleteSub($slug, 'Exercice', $id, $this->get('security.context')->isGranted('ROLE_MODO'));
    }
}
