<?php

namespace KI\CoreBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    public function testSearch()
    {
        $types = array(
            'User',
            'Club',
            'Ponthub',
            'Movie',
            'Serie',
            'Episode',
            'Album',
            'Music',
            'Game',
            'Software',
            'Other',
            'Actor',
            'Genre',
            'Post',
            'Event',
            'News',
            'Exercice',
            'Course',
            'Tag'
        );

        foreach ($types as $type) {
            $this->client->request('POST', '/search', array('search' => $type.'/al'));
            $response = $this->client->getResponse();
            $this->assertJsonResponse($response, 200);
        }

        $this->client->request('POST', '/search', array('search' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'Users/'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'al'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'Miam/'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/search', array('search' => 'Miam/ps'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }
}
