<?php

namespace App\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// Valide les formulaires pour une entité et affiche la réponse à la demande
class FormHelper
{
    protected $manager;
    protected $formFactory;
    protected $router;
    protected $request;

    public function __construct(EntityManager $manager, FormFactory $formFactory, Router $router)
    {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function setRequest(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Traite un formulaire
     * @param  mixed $item L'item éventuellement existant à processer
     * @param  string $method POST ou PATCH
     * @return array          Des détails sur le résultat de l'opération
     */
    public function formData($item, $method, $flush = true)
    {
        // On devine le formulaire à partir du chemin de la classe
        $formName = str_replace('Entity', 'Form', get_class($item)) . 'Type';
        $form = $this->formFactory->create($formName, $item, ['method' => $method]);
        $form->handleRequest($this->request);
        $code = 400;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($method == 'POST') {
                $this->manager->persist($item);
                $code = 201;
            } else {
                $code = 204;
            }
            if ($flush)
                $this->manager->flush();
        } else {
            $this->manager->detach($item);
        }

        return ['form' => $form, 'item' => $item, 'code' => $code];
    }
}
