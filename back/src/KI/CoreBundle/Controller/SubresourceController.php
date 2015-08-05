<?php

namespace KI\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Fonctions générales pour servir une sous ressource de type REST (exemple: Serie -> Episode)
class SubresourceController extends ResourceController
{
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
            $repo = $this->manager->getRepository('KI'.$this->bundle.'Bundle:'.$this->className.$name);
            return $repo->findBy(array(strtolower($this->className) => $item));
        }
    }

    /**
     * Route GET générique pour une sous ressource
     * @param  string  $slug Le slug de l'entité parente
     * @param  string  $name Le nom de la classe fille
     * @param  string  $id   L'identifiant de l'entité fille
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     */
    protected function getOneSub($slug, $name, $id, $auth = false)
    {
        // On n'en a pas besoin ici mais on vérifie que l'item parent existe bien
        $item = $this->findBySlug($slug);
        $this->switchClass($name);
        return $this->getOne($id, $auth);
    }

    /**
     * Route PATCH générique pour une sous ressource
     * @param  string  $slug Le slug de l'entité parente
     * @param  string  $name Le nom de la classe fille
     * @param  string  $id   L'identifiant de l'entité fille
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     */
    protected function patchSub($slug, $name, $id, $auth = false)
    {
        $this->findBySlug($slug);
        $this->switchClass($name);
        return $this->patch($id, $auth);
    }

    /**
     * Route DELETE générique pour une sous ressource
     * @param  string  $slug Le slug de l'entité parente
     * @param  string  $name Le nom de la classe fille
     * @param  string  $id   L'identifiant de l'entité fille
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     */
    protected function deleteSub($slug, $name, $id, $auth = false)
    {
        $this->findBySlug($slug);
        $this->switchClass($name);
        return $this->delete($id, $auth);
    }
}
