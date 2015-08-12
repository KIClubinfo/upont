<?php

namespace KI\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use FOS\RestBundle\View\View as RestView;

// Fonctions générales pour servir une ressource de type REST, CommentsController en est un exemple
class ResourceController extends LikeableController
{
    /**
     * Route GET (liste) générique
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    public function getAll($auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);

        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($this->repository));

        $results = $this->repository->findBy($findBy, $sortBy, $limit, $offset);
        return $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);
    }

    /**
     * Route GET générique
     * @param  string  $slug Le slug de l'entité à récupérer
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function getOne($slug, $auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);
        return $this->findBySlug($slug);
    }

    /**
     * Permet de valider un formulaire, rend la main sur l'entité ainsi créée
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return array         Le formulaire traité
     */
    protected function postData($auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $formHelper = $this->get('ki_core.helper.form');
        return $formHelper->formData(new $this->class(), 'POST');
    }

    /**
     * Permet d'afficher un formulaire une fois celui-ci validé
     * @param  array  $data   Le formulaire validé
     * @param  object $parent L'objet parent si appliquable
     * @return Response
     */
    protected function postView($data, $parent = null)
    {
        $formHelper = $this->get('ki_core.helper.form');
        return $formHelper->formView($data, $parent);
    }

    /**
     * Route POST générique effectuant directement validation et affichage
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function post($auth = false)
    {
        return $this->postView($this->postData($auth));
    }

    /**
     * Route PATCH générique
     * @param  string  $slug Le slug de l'entité à modifier
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function patch($slug, $auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $item = $this->findBySlug($slug);

        $formHelper = $this->get('ki_core.helper.form');
        return $this->postView($formHelper->formData($item, 'PATCH'));
    }

    /**
     * Route DELETE générique
     * @param  string  $slug Le slug de l'entité à supprimer
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     * @Route\View()
     */
    protected function delete($slug, $auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $item = $this->findBySlug($slug);
        $this->manager->remove($item);
        $this->manager->flush();
    }
}
