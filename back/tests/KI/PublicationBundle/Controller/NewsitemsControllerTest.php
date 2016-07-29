<?php

namespace Tests\KI\PublicationBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class NewsitemsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request('POST', '/newsitems', array(
            'name' => 'La Porte',
            'text' => 'C\'est comme perdre',
            'sendMail' => true,
            'authorClub' => 'ki'
        ));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);

        // On vérifie qu'un mail a été envoyé
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(2, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // On vérifie le message
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertTrue(!empty($message->getSubject()));
        $this->assertEquals('evenements@upont.enpc.fr', key($message->getFrom()));
    }

    public function testGet()
    {
        $this->client->request('GET', '/newsitems');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/newsitems/la-porte');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/newsitems/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/newsitems/la-porte',
            array('text' => 'ddssqdqsd', 'sendMail' => false)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request(
            'PATCH',
            '/newsitems/la-porte',
            array('text' => '')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request(
            'PATCH',
            '/newsitems/sjoajsiohaysahais-asbsksaba7',
            array('username' => 'miam', 'email' => '123@mail.fr')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/newsitems/la-porte');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/newsitems/la-porte');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
