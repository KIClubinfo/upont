<?php

namespace KI\CoreBundle\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginateHelper
{
    protected $manager;
    protected $request;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function setRequest(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }
}
