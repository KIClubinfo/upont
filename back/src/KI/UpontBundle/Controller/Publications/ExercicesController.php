<?php

namespace KI\UpontBundle\Controller\Publications;

use KI\UpontBundle\Controller\BaseController;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExercicesController extends BaseController
{

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Exercice', 'Publications');
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
    public function getExercicesAction() { return $this->getAll(); }

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
    public function getExerciceAction($slug) { return $this->getOne($slug); }

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
     * @Get("/exercices/{slug}/download")
     */
    public function downloadExerciceAction($slug)
    {
        $exercice = $this->findBySlug($slug);

        // On lit le fichier PDF
        return new Response(file_get_contents($exercice->getAbsolutePath()), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition: attachment; filename="' . $exercice->getDepartment() . '' . $exercice->getName() . '"'
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
     */
     public function postExerciceAction(Request $request) {
        $uploader = $this->container->get('security.context')->getToken()->getUser();
        $return = $this->partialPost($this->get('security.context')->isGranted('ROLE_USER'));

        if ($return['code'] != 400) {
            // On règle tout comme on veut
            $return['item']->setDate(time());
            $return['item']->setUploader($uploader);
            $return['item']->setValid($this->get('security.context')->isGranted('ROLE_MODO'));

            // On upload le fichier
            if (!$request->files->has('file'))
                throw new BadRequestHttpException('Aucun fichier présent');

            $this->em->flush();
            $request->files->get('file')->move($return['item']->getBasePath(), $return['item']->getId() . '.pdf');
        }

        return $this->postView($return);
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
    public function patchExerciceAction($slug)
    {
        return $this->patch($slug, $this->get('security.context')->isGranted('ROLE_MODO'));
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
    public function deleteExerciceAction($slug)
    {
        return $this->delete($slug, $this->get('security.context')->isGranted('ROLE_MODO'));
    }
}
