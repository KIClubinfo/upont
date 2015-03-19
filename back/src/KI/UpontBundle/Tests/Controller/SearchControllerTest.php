<?php

namespace KI\UpontBundle\Tests\Controller;

use KI\UpontBundle\Tests\WebTestCase;

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
            $client = static::createClient();
            $client->request('POST', '/search', array('search' => $type.'/al'));
            $this->assertJsonResponse($client->getResponse(), 200);
        }

        $client = static::createClient();
        $client->request('POST', '/search', array('search' => ''));
        $this->assertJsonResponse($client->getResponse(), 200);

        $client = static::createClient();
        $client->request('POST', '/search', array('search' => 'Users/'));
        $this->assertJsonResponse($client->getResponse(), 400);

        $client = static::createClient();
        $client->request('POST', '/search', array('search' => 'al'));
        $this->assertJsonResponse($client->getResponse(), 400);

        $client = static::createClient();
        $client->request('POST', '/search', array('search' => 'Miam/'));
        $this->assertJsonResponse($client->getResponse(), 400);

        $client = static::createClient();
        $client->request('POST', '/search', array('search' => 'Miam/ps'));
        $this->assertJsonResponse($client->getResponse(), 400);
    }
}
