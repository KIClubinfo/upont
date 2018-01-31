<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

// Fonctions générales pour servir une ressource de type REST, CommentsController en est un exemple
class ResourceController extends LikeableController
{
    /**
     * Route GET (liste) générique
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     */
    public function getAll($auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);

        $paginateHelper = $this->get('App\Helper\PaginateHelper');

        $resultData = $paginateHelper->paginate($this->repository);

        return $this->json($resultData, 200);
    }

    /**
     * Route GET générique
     * @param  string  $slug Le slug de l'entité à récupérer
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return object
     */
    protected function getOne($slug, $auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);
        $item =  $this->findBySlug($slug);
        return $item;
    }

    /**
     * Permet de valider un formulaire, rend la main sur l'entité ainsi créée
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return array         Le formulaire traité
     */
    protected function post($auth = false, $flush = true)
    {
        $this->trust($this->is('MODO') || $auth);
        $formHelper = $this->get('App\Helper\FormHelper');
        return $formHelper->formData(new $this->class(), 'POST', $flush);
    }

    /**
     * Route PATCH générique
     * @param  string  $slug Le slug de l'entité à modifier
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return array
     */
    protected function patch($slug, $auth = false, $flush = true)
    {
        $this->trust($this->is('MODO') || $auth);
        $item = $this->findBySlug($slug);

        $formHelper = $this->get('App\Helper\FormHelper');
        return $formHelper->formData($item, 'PATCH', $flush);
    }

    /**
     * Route DELETE générique
     * @param  string  $slug Le slug de l'entité à supprimer
     * @param  boolean $auth Un override éventuel pour le check des permissions
     */
    protected function delete($slug, $auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $item = $this->findBySlug($slug);
        $this->manager->remove($item);
        $this->manager->flush();
    }

    /**
     * Route PATCH générique pour un objet
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return array
     */
    protected function patchItem($item, $auth = false, $flush = true)
    {
        $this->trust($this->is('MODO') || $auth);

        $formHelper = $this->get('App\Helper\FormHelper');
        return $formHelper->formData($item, 'PATCH', $flush);
    }

    /**
     * Route DELETE générique pour une sous ressource
     * @param  boolean $auth Un override éventuel pour le check des permissions
     */
    protected function deleteItem($item, $auth = false)
    {
        $this->trust($this->is('MODO') || $auth);

        $this->manager->remove($item);
        $this->manager->flush();
    }
}
