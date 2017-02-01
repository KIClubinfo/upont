<?php

namespace KI\UserBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Entity\Pontlyvalent;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Form\PontlyvalentType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PontlyvalentsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Pontlyvalent', 'User');
    }

    private function checkPontlyvalentOpen(){
        $lastPromo = $this->getConfig('promos.latest');

        if ($this->user->getPromo() == $lastPromo) {
            throw new BadRequestHttpException('Ton tour n\'est pas encore arrivé, petit ' . $lastPromo . ' !');
        }

        if($this->getConfig('pontlyvalent.open')) {
            throw new BadRequestHttpException('Le pontlyvalent est fermé !');
        }
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les commentaires",
     *  output="KI\UserBundle\Entity\Pontlyvalent",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/pontlyvalent")
     * @Method("GET")
     */
    public function getPontlyvalentsAction()
    {
        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($this->repository));

        if ($this->is('MODO') || $this->isClubMember('bde')) {
            $results = $this->repository->findBy($findBy);
        } else {
            $results = $this->repository->findBy([
                'author' => $this->user
            ]);
        }

        return $this->json($results);
    }

    /**
     * @ApiDoc(
     *  description="Liste les commentaires sur un user",
     *  output="KI\UserBundle\Entity\Pontlyvalent",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{targetUsername}/pontlyvalent")
     * @Method("GET")
     */
    public function getPontlyvalentAction($targetUsername)
    {
        $this->checkPontlyvalentOpen();

        $target = $this->manager->getRepository('KIUserBundle:User')->findOneByUsername($targetUsername);

        $pontlyvalent = $this->repository->getPontlyvalent($target, $this->user);

        if(count($pontlyvalent) != 1)
            throw new NotFoundHttpException();

        return $this->json($pontlyvalent[0]);
    }

    /**
     * @ApiDoc(
     *  description="Ecrit un commentaire sur quelqu'un",
     *  input="KI\UserBundle\Form\PontlyvalentType",
     *  output="KI\UserBundle\Entity\Pontlyvalent",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{targetUsername}/pontlyvalent")
     * @Method("POST")
     */
    public function postPontlyvalentAction(Request $request, $targetUsername)
    {
        $this->checkPontlyvalentOpen();

        /**
         * @var User $target
         */
        $target = $this->manager->getRepository('KIUserBundle:User')->findOneByUsername($targetUsername);

        $targetPromo = $this->getConfig('promos.assos');
        if ($target->getPromo() != $targetPromo) {
            throw new BadRequestHttpException('Ce n\'est pas un ' . $targetPromo . ' !');
        }

        $pontlyvalent = $this->repository->getPontlyvalent($target, $this->user);
        if (count($pontlyvalent) == 0) {
            $pontlyvalent = new Pontlyvalent();
            $pontlyvalent->setTarget($target);
            $pontlyvalent->setAuthor($this->user);
        } else {
            $pontlyvalent = $pontlyvalent[0];
        }

        $pontlyvalent->setDate(time());

        $form = $this->createForm(PontlyvalentType::class, $pontlyvalent, ['method' => 'POST']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->manager->persist($pontlyvalent);
            $this->manager->flush();

            return $this->json($pontlyvalent, 201);
        } else {
            $this->manager->detach($pontlyvalent);
            return $this->json($form, 400);
        }
    }

    /**
     * @ApiDoc(
     *  description="Supprime un commentaire",
     *  input="KI\UserBundle\Form\PontlyvalentType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/users/{targetUsername}/pontlyvalent")
     * @Method("DELETE")
     */
    public function deletePontlyvalentAction($targetUsername)
    {
        $this->checkPontlyvalentOpen();

        $target = $this->manager->getRepository('KIUserBundle:User')->findOneByUsername($targetUsername);

        $pontlyvalent = $this->repository->getPontlyvalent($target, $this->user);

        if (count($pontlyvalent) != 1) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }

        $this->manager->remove($pontlyvalent[0]);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
