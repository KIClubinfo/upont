<?php

namespace KI\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;

// Fonctions générales pour servir une sous ressource de type REST (exemple: Serie -> Episode)
class SubresourceController extends ResourceController
{
    /**
     * Renvoie toutes les sous ressources associées à un objet
     * @param  string  $slug   Le slug de l'entité parente
     * @param  string  $name   Le nom de la classe fille
     * @param  boolean $simple Si vrai, considère une relation ManyToMany normale,
     *                         Sinon considère qu'il y a des attributs et donc
     *                         qu'il y a une entité intermédiaire
     * @param  boolean $auth   Un override éventuel pour le check des permissions
     * @Route\View()
     */
    protected function getAllSub($slug, $name, $simple = true, $auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);
        $item = $this->findBySlug($slug);

        if ($simple) {
            $method = 'get'.ucfirst($name).'s';
            return $item->$method();
        } else {
            // Récupère le repository de l'entité intermédiaire
            $repository = $this->manager->getRepository('KI'.$this->bundle.'Bundle:'.$this->className.$name);
            return $repository->findBy(array(strtolower($this->className) => $item));
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
        $parent = $this->findBySlug($slug);

        $this->switchClass($name);
        $out = $this->getOne($id, $auth);
        $this->switchClass();

        return $out;
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
        $parent = $this->findBySlug($slug);

        $this->switchClass($name);
        $out = $this->patch($id, $auth);
        $this->switchClass();

        return $out;
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
        $parent = $this->findBySlug($slug);

        $this->switchClass($name);
        $out = $this->delete($id, $auth);
        $this->switchClass();

        return $out;
    }
}
