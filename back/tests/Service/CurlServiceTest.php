<?php

namespace Tests\App\Services;

use App\Service\CurlService;
use App\Tests\WebTestCase;

class CurlServiceTest extends WebTestCase
{
    public function testCurl()
    {
        $curlService = static::$kernel->getContainer()->get(CurlService::class);
        $response = $curlService->curl('https://www.google.fr');
        $this->assertInternalType('string', $response);
        $this->assertContains('<!doctype html>', $response);
    }
}
