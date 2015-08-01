<?php

namespace KI\PublicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('KIPublicationBundle:Default:index.html.twig', array('name' => $name));
    }
}
