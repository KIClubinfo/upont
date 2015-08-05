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
        $form = $this->formFactory->create(new $formName(), $item, array('method' => $method));
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

        return array('form' => $form, 'item' => $item, 'code' => $code);
    }

    /**
     * Génère la réponse relative au traitement d'un formulaire
     * @param  array $data Le formulaire traité
     * @return Response
     */
    public function formView($data)
    {
        // Devine le nom de la classe à partir de l'entité
        $names = explode('\\', get_class($data['item']));
        $className = $names[count($names)-1];

        switch ($data['code']) {
        case 400:
            return RestView::create($data['form'], 400);
        case 204:
            $this->manager->flush();
            return RestView::create(null, 204);
        default:
            $this->manager->flush();
            return RestView::create($data['item'], 201, array(
                'Location' => $this->router->generate(
                    'get_'.strtolower($className),
                    array('slug' => $data['item']->getSlug()),
                    true
                )
            ));
        }
    }
}
