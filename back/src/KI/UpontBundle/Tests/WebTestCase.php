<?php

namespace KI\UpontBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use Liip\FunctionalTestBundle\Test\WebTestCase as LiipWebTestCase;

abstract class WebTestCase extends LiipWebTestCase
{
    protected $container;
    protected $client;
    protected $authorizationHeaderPrefix = 'Bearer';
    protected $queryParameterName = 'bearer';

    // Crée un client autentifié
    public function __construct()
    {
        parent::__construct();

        $client = static::createClient();
        $client->request('POST', $this->getUrl('login'), array('username' => 'trancara', 'password' => 'password'));
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        $client = static::createClient();
        $this->assertArrayHasKey('token', $data);
        $client->setServerParameter('HTTP_Authorization', sprintf('%s %s', $this->authorizationHeaderPrefix, $data['token']));
        $this->client = $client;
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200, $checkValidJson = false, $contentType = 'application/json')
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );

        // On se fout de ce qui est retourné si rien n'est retourné
        if ($statusCode != 204 && $checkValidJson) {
            $this->assertTrue(
                $response->headers->contains('Content-Type', $contentType),
                $response->headers
            );
        }

        if ($checkValidJson) {
            $decode = json_decode($response->getContent(), true);
            $this->assertTrue(
                ($decode !== null && $decode !== false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}
