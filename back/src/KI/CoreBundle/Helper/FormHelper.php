<?php

namespace KI\CoreBundle\Helper;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View as RestView;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;

// Valide les formulaires pour une entité et affiche la réponse à la demande
class FormHelper
{
    protected $manager;
    protected $formFactory;
    protected $router;
    protected $request;

    public function __construct(EntityManager $manager, FormFactory $formFactory, Router $router)
    {
        $this->manager     = $manager;
        $this->formFactory = $formFactory;
        $this->router      = $router;
    }

    public function setRequest(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Traite un formulaire
     * @param  mixed  $item   L'item éventuellement existant à processer
     * @param  string $method POST ou PATCH
     * @return array          Des détails sur le résultat de l'opération
     */
    public function formData($item, $method = 'PATCH')
    {
        // On devine le formulaire à partir du chemin de la classe
        $formName = str_replace('Entity', 'Form', get_class($item)).'Type';
        $form = $this->formFactory->create($formName, $item, ['method' => $method]);
        $form->handleRequest($this->request);
        $code = 400;

        if ($form->isValid()) {
            if ($method == 'POST') {
                $this->manager->persist($item);
                $code = 201;
            } else {
                $code = 204;
            }
        } else {
            $this->manager->detach($item);
        }

        return ['form' => $form, 'item' => $item, 'code' => $code];
    }

    /**
     * Génère la réponse relative au traitement d'un formulaire
     * @param  array  $data   Le formulaire traité
     * @param  object $parent Éventuellement l'objet parent
     * @return Response
     */
    public function formView($data, $parent = null)
    {
        switch ($data['code']) {
        case 400:
            return RestView::create($data['form'], 400);
        case 204:
            $this->manager->flush();
            return RestView::create(null, 204);
        default:
            $this->manager->flush();

            // Génère la route
            $className = $this->namespaceToClassname($data['item']);
            if ($parent === null) {
                $route = 'get_'.$className;
                $params = [
                    'slug' => $data['item']->getSlug()
                ];
            } else {
                $parentClass = $this->namespaceToClassname($parent);
                $route = 'get_'.$parentClass.'_'.$className;
                $params = [
                    'slug' => $parent->getSlug(),
                    'id'   => $data['item']->getSlug()
                ];
            }

            return RestView::create($data['item'], 201, [
                'Location' => $this->router->generate($route, $params, true)
            ]);
        }
    }

    /**
     * Récupère le nom de classe d'un objet et le met au format d'une route
     * @param  object $object L'objet en question
     * @return string         Le nom de la classe en minuscules
     */
    private function namespaceToClassname($object)
    {
        $names = explode('\\', get_class($object));
        $className = $names[count($names) - 1];
        return strtolower($className);
    }
}
