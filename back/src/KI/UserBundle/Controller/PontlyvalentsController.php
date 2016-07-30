<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Entity\Pontlyvalent;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
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
        $this->helper();

        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($this->repository));

        $pontlyvalentRepository = $this->manager->getRepository('KIUserBundle:Pontlyvalent');

        if ($this->is('MODO') || $this->isClubMember('bde')) {
            $results = $pontlyvalentRepository->findBy($findBy);
        } else {
            $results = $pontlyvalentRepository->findBy([
                'author' => $this->user
            ]);
        }

        return $paginateHelper->paginateView($results, 10000, $page, $totalPages, $count);
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
        return $this->helper($slug)['pontlyvalent'];
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
    public function postPontlyvalentAction(Request $request, $slug)
    {
        $pontlyvalentHelper = $this->helper($slug);

        if (!$request->request->has('text') || $text = trim($request->request->get('text')) === '') {
            throw new BadRequestHttpException('Texte de commentaire manquant');
        }

        $pontlyvalent = $pontlyvalentHelper['pontlyvalent'];
        if (count($pontlyvalent) != 0) {
            throw new BadRequestHttpException('Tu as déjà commenté sur cette personne');
        }

        $pontlyvalent = new Pontlyvalent();
        $pontlyvalent->setTarget($pontlyvalentHelper['target']);
        $pontlyvalent->setAuthor($this->user);
        $pontlyvalent->setText($text);

        $this->manager->persist($pontlyvalent);
        $this->manager->flush();

        return $this->json(null, 201);
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
    public function patchPontlyvalentAction(Request $request, $slug)
    {
        $pontlyvalent = $this->helper($slug)['pontlyvalent'][0];
        if (!isset($pontlyvalent)) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }
        if (!$request->request->has('text') || $text = trim($request->request->get('text')) === '') {
            throw new BadRequestHttpException('Texte de commentaire manquant');
        }

        $pontlyvalent->setDate(time());
        $pontlyvalent->setText($text);
        $this->manager->persist($pontlyvalent);
        $this->manager->flush();

        return $this->json(null, 204);
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
        $pontlyvalent = $this->helper($slug)['pontlyvalent'];
        if (count($pontlyvalent) != 1) {
            throw new NotFoundHttpException('Commentaire non trouvé');
        }

        $this->manager->remove($pontlyvalent[0]);
        $this->manager->flush();

        return $this->json(null, 204);
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

            $pontlyvalentRepository = $this->manager->getRepository('KIUserBundle:Pontlyvalent');
            $pontlyvalent = $pontlyvalentRepository->findBy([
                'target' => $target,
                'author' => $this->user
            ]);

        return [
            'target' => $target,
            'pontlyvalent' => $pontlyvalent,
        ];
        }
    }
}
