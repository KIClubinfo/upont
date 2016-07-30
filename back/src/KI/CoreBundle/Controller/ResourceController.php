<?php

namespace KI\CoreBundle\Controller;

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

        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($this->repository));

        $results = $this->repository->findBy($findBy, $sortBy, $limit, $offset);
        list($results, $links, $count) = $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);

        return $this->json($results, 200, [
            'Links' => implode(',', $links),
            'Total-count' => $count
        ]);
    }

    /**
     * Route GET générique
     * @param  string  $slug Le slug de l'entité à récupérer
     * @param  boolean $auth Un override éventuel pour le check des permissions
     * @return Response
     */
    protected function getOne($slug, $auth = false)
    {
        $this->trust(!$this->is('EXTERIEUR') || $auth);
        $item =  $this->findBySlug($slug);
        return $this->json($item);
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
     */
    protected function delete($slug, $auth = false)
    {
        $this->trust($this->is('MODO') || $auth);
        $item = $this->findBySlug($slug);
        $this->manager->remove($item);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
