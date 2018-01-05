<?php

namespace App\Controller;

// Fonctions générales pour servir une sous ressource de type REST (exemple: Serie -> Episode)
class SubresourceController extends ResourceController
{
    /**
     * Route GET générique pour une sous ressource
     * @param  string  $slug Le slug de l'entité parente
     * @param  string  $name Le nom de la classe fille
     * @param  string  $id   L'identifiant de l'entité fille
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return object
     */
    protected function getOneSub($slug, $name, $id, $auth = false)
    {
        $this->findBySlug($slug);

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
     * @return array
     */
    protected function patchSub($slug, $name, $id, $auth = false)
    {
        $this->findBySlug($slug);

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
     */
    protected function deleteSub($slug, $name, $id, $auth = false)
    {
        $this->findBySlug($slug);

        $this->switchClass($name);
        $this->delete($id, $auth);
        $this->switchClass();
    }
}
