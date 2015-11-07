<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Entity\Pontlyvalent;
use KI\UserBundle\Form\PontlyvalentType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PontlyvalentsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Pontlyvalent', 'User');
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
     * @Route\Get("/users/pontlyvalent")
     */
    public function getPontlyvalentsAction()
    {
        $pontlyvalentHelper = $this->helper();

        if (!($this->is('MODO') || $this->isClubMember('bde'))) {
            $pontlyvalentRepository = $this->manager->getRepository('KIUserBundle:Pontlyvalent');

            $paginateHelper = $this->get('ki_core.helper.paginate');
            extract($paginateHelper->paginateData($this->repository));
            $results = $pontlyvalentRepository->findBy(array(
                'author' => $this->user
            ));
        }

        return $this->getAll(true);
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
     * @Route\Get("/users/{slug}/pontlyvalent")
     */
    public function getPontlyvalentAction($slug)
    {
        $pontlyvalentHelper = $this->helper($slug);

        $pontlyvalentRepository = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        return $pontlyvalentRepository->findBy(array(
            'target' => $pontlyvalentHelper['target'],
            'author' => $this->user
        ));
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
     * @Route\Post("/users/{slug}/pontlyvalent")
     */
    public function postPontlyvalentAction($slug)
    {
        $pontlyvalentHelper = $this->helper($slug);

        $request = $pontlyvalentHelper['request'];
        if (!$request->has('text')) {
            throw new BadRequestHttpException('Texte de commentaire manquant');
        }

        // On vérifie que l'auteur n'a pas déjà écrit sur cet utilisateur
        $author = $this->user;

        $pontlyvalentRepository = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        $pontlyvalent = $pontlyvalentRepository->findBy(array(
            'target' => $pontlyvalentHelper['target'],
            'author' => $author
        ));

        if (count($pontlyvalent) != 0) {
            throw new BadRequestHttpException('Tu as déjà commenté sur cette personne');
        }

        $pontlyvalent = new Pontlyvalent();
        $pontlyvalent->setTarget($pontlyvalentHelper['target']);
        $pontlyvalent->setAuthor($author);
        $pontlyvalent->setText($request->get('text'));

        $this->manager->persist($pontlyvalent);
        $this->manager->flush();

        return $this->jsonResponse(null, 201);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un commentaire",
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
     * @Route\Patch("/users/{slug}/pontlyvalent")
     */
    public function patchPontlyvalentAction($slug)
    {
        $pontlyvalentHelper = $this->helper($slug);

        $request = $pontlyvalentHelper['request'];
        if (!$request->has('text') || $request->get('text') == null) {
            throw new BadRequestHttpException('Texte de commentaire manquant');
        }

        $pontlyvalentRepository = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        $pontlyvalent = $pontlyvalentRepository->findOneBy(array(
            'target' => $pontlyvalentHelper['target'],
            'author' => $this->user
        ));

        if (!isset($pontlyvalent)) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }

        $pontlyvalent->setDate(time());
        $pontlyvalent->setText($request->get('text'));
        $this->manager->persist($pontlyvalent);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
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
     * @Route\Delete("/users/{slug}/pontlyvalent")
     */
    public function deletePontlyvalentAction($slug)
    {
        $pontlyvalentHelper = $this->helper($slug);

        $pontlyvalentRepository = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        $pontlyvalent = $pontlyvalentRepository->findBy(array(
            'target' => $pontlyvalentHelper['target'],
            'author' => $this->user
        ));

        if (count($pontlyvalent) != 1) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }

        $this->manager->remove($pontlyvalent[0]);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
    }

    private function helper($slug = null)
    {
        if ($this->user->getPromo() == '018') {
            throw new AccessDeniedException('Ton tour n\'est pas encore arrivé, petit 018 !');
        }

        if (isset($slug)) {
            $userRepository = $this->manager->getRepository('KIUserBundle:User');
            $target = $userRepository->findOneByUsername($slug);
            if ($target->getPromo() != '017') {
                throw new AccessDeniedException('Ce n\'est pas un 017 !');
            }

        return array(
            'target' => $target,
            'request' => $this->getRequest()->request,
            );
        }
    }
}
