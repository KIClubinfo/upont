<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Entity\Pontlyvalent;
use KI\UserBundle\Form\PontlyvalentType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        return $this->getAll($this->is('MODO'));
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
    public function getPontlyvalentAction($slug) {
        $repo = $this->manager->getRepository('KIUserBundle:User');
        $target = $repo->findOneByUsername($slug);

        $repoPontly = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        if ($this->is('MODO')) {
            return $repoPontly->findBy(array('target' => $target));
        } else {
            return $repoPontly->findBy(array(
                'target' => $target,
                'author' => $this->user
                ));
        }
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
    public function postPontlyvalentAction($slug) {
        $request = $this->getRequest()->request;

        if (!$request->has('text')) {
            throw new BadRequestHttpException('Texte de commentaire manquant');
        }

        // On vérifie que l'auteur n'a pas déjà écrit sur cet utilisateur
        $repo = $this->manager->getRepository('KIUserBundle:User');
        $target = $repo->findOneByUsername($slug);
        $author = $this->user;

        $repoPontly = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        $pontlyvalent = $repoPontly->findBy(array(
            'target' => $target,
            'author' => $author
            ));

        if (count($pontlyvalent) != 0) {
            throw new BadRequestHttpException('Tu as déjà commenté sur cette personne');
        }

        $pontlyvalent = new Pontlyvalent();
        $pontlyvalent->setTarget($target);
        $pontlyvalent->setAuthor($author);
        $pontlyvalent->setText($request->get('text'));
        $pontlyvalent->setDate(time());

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
        $request = $this->getRequest()->request;

        if (!$request->has('text') || $request->get('text') == null)
            throw new BadRequestHttpException('Texte de commentaire manquant');

        $repo = $this->manager->getRepository('KIUserBundle:User');
        $target = $repo->findOneByUsername($slug);

        $repoPontly = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        $pontlyvalent = $repoPontly->findOneBy(array(
            'target' => $target,
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
        $request = $this->getRequest()->request;

        $repo = $this->manager->getRepository('KIUserBundle:User');
        $target = $repo->findOneByUsername($slug);

        $repoPontly = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
        $pontlyvalent = $repoPontly->findBy(array(
            'target' => $target,
            'author' => $this->user
            ));

        if (count($pontlyvalent) != 1) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }

        $this->manager->remove($pontlyvalent[0]);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
    }
}
