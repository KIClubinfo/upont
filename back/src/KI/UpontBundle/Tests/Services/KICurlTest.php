<?php

namespace KI\UpontBundle\Tests\Services;

use KI\UpontBundle\Tests\WebTestCase;
use KI\UpontBundle\Services\KIImages;

class KICurlTest extends WebTestCase
{
    protected $container;
    protected $service;
    
    public function __construct()
    {
        parent::__construct();
        $this->container = static::$kernel->getContainer();
        $this->service = $this->container->get('ki_upont.curl');
    }
    
    public function testCurl()
    {
        $response = $this->service->curl('https://www.google.fr');
        $this->assertContains('<!doctype html>', $response);
    }
}
