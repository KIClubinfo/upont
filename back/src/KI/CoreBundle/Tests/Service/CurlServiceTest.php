<?php

namespace KI\CoreBundle\Tests\Services;

use KI\CoreBundle\Tests\WebTestCase;

class CurlServiceTest extends WebTestCase
{
    protected $container;
    protected $service;
    public function __construct()
    {
        parent::__construct();
        $this->container = static::$kernel->getContainer();
        $this->service = $this->container->get('ki_core.service.curl');
    }
    public function testCurl()
    {
        $response = $this->service->curl('https://www.google.fr');
        $this->assertInternalType('string', $response);
        $this->assertContains('<!doctype html>', $response);
    }
}
