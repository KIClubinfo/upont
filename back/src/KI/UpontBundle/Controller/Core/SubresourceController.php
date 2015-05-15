<?php

namespace KI\UpontBundle\Controller\Core;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Fonctions générales pour servir une sous ressource de type REST
class SubresourceController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    // Gestion des sous ressources

    /**
     * Renvoie une sous ressource
     * Si $manyToMany = true, renvoie le paramètre $name de l'entité relié par une relation Many To Many
     * Sinon, renvoie les éléments d'une relation avec attribut en se basant sur la classe conjointe $name
     * @Route\View()
     */
    protected function getAllSub($slug, $name, $manyToMany = true, $auth = false)
    {
        if (isset($this->user) && $this->get('security.context')->isGranted('ROLE_EXTERIEUR') && !$auth)
            throw new AccessDeniedException();

        $item = $this->findBySlug($slug);

        if ($manyToMany) {
            $method = 'get'.ucfirst($name).'s';
            return $item->$method();
        } else {
            $repo = $this->em->getRepository('KIUpontBundle:'.$this->namespace.$this->className.$name);
            return $repo->findBy(array(strtolower($this->className) => $item));
        }
    }

    /**
     * @Route\View()
     */
    protected function getOneSub($slug, $name, $id, $auth = false)
    {
        if (isset($this->user) && $this->get('security.context')->isGranted('ROLE_EXTERIEUR') && !$auth)
            throw new AccessDeniedException();

        $item = $this->findBySlug($slug);

        if (preg_match('#^\d+$#', $id))
            $filter = 'id';
        else
            $filter = 'slug';

        $this->switchClass($name);
        $return = $this->repo->findOneBy(array(strtolower($this->save) => $item, $filter => $id));

        if (!$return instanceof $this->class)
            throw new NotFoundHttpException('Objet '.$this->className.' non trouvée');

        $this->switchClass();
        return $return;
    }

    /**
     * @Route\View()
     */
    protected function patchSub($slug, $name, $id, $auth = false)
    {
        if (isset($this->user) &&
            (!$this->get('security.context')->isGranted('ROLE_MODO')
                || $this->get('security.context')->isGranted('ROLE_ADMISSIBLE')
                || $this->get('security.context')->isGranted('ROLE_EXTERIEUR'))
            && !$auth)
            throw new AccessDeniedException('Accès refusé');

        // On n'en a pas besoin ici mais on vérifie que l'item parent existe bien
        $this->findBySlug($slug);

        $this->switchClass($name);
        $item = $this->findBySlug($id);
        $return = $this->processForm($item);
        $this->switchClass();
        return $this->postView($return);
    }

    /**
     * @Route\View()
     */
    protected function deleteSub($slug, $name, $id, $auth = false)
    {
        if (isset($this->user) &&
            (!$this->get('security.context')->isGranted('ROLE_MODO')
                || $this->get('security.context')->isGranted('ROLE_ADMISSIBLE')
                || $this->get('security.context')->isGranted('ROLE_EXTERIEUR'))
            && !$auth)
            throw new AccessDeniedException('Accès refusé');

        // On n'en a pas besoin ici mais on vérifie que l'item parent existe bien
        $this->findBySlug($slug);

        $this->switchClass($name);
        $item = $this->findBySlug($id);
        $this->em->remove($item);
        $this->switchClass();

        $this->em->flush();
    }

    protected function subPostView($data, $slug, $route)
    {
        if ($data['code'] == 400) {
            return RestView::create($data['form'], 400);
        } else if ($data['code'] == 204) {
            $this->em->flush();
            return RestView::create(null, 204);
        } else {
            $this->em->flush();
            return RestView::create($data['item'],
                201,
                array(
                    'Location' => $this->generateUrl(
                        $route,
                        array('slug' => $slug, 'id' => $data['item']->getSlug()),
                        true
                    )
                )
            );
        }
    }
}
