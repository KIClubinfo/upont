<?php

namespace KI\PublicationBundle\Tests\Controller;

use KI\CoreBundle\Tests\WebTestCase;

class EventsControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests. Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {

        $this->client->request('POST', '/events', array('name' => 'Manger des chips', 'text' => 'C\'est bon', 'startDate' => 151515, 'endDate' => 31415, 'entryMethod' => 'libre', 'place' => 'DTC', 'authorClub' => 'bde'));
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
        $this->assertEquals('noreply@upont.enpc.fr', key($message->getFrom()));
    }

    public function testGet()
    {
        $this->client->request('GET', '/events');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/events/manger-des-chips');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/events/manger-des-chips/attendees');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/events/manger-des-chips/pookies');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/events/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request('PATCH', '/events/manger-des-chips', array('endDate' => 12345));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/events/manger-des-chips', array('text' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/events/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testAttend()
    {
        $this->client->request('POST', '/events/manger-des-chips/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/events/manger-des-chips/attend');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/events/manger-des-chips/decline');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/events/manger-des-chips/decline');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/events/manger-des-chips/decline');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/events/manger-des-chips');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/events/manger-des-chips');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    //Tests relatifs au shotgun

    public function testPostShotgunEvent()
    {
        $this->client->request('POST', '/events', array('name' => 'Semaine Ski', 'text' => 'Il fait froid', 'startDate' => 151515, 'endDate' => 31415, 'entryMethod' => 'shotgun', 'shotgunDate' => 101010, 'shotgunLimit' => 1, 'shotgunText' => 'Il est deux heures du matin, et tout va bien', 'place' => 'Far Far Away'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
    }

    public function testPostShotgun()
    {
        $this->client->request('POST', '/events/semaine-ski/shotgun', array('motivation' => 'moimoimoimoi'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }

    public function testPatchShotgun()
    {
        $this->client->request('PATCH', '/events/semaine-ski/shotgun', array('motivation' => 'lolololol'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/events/semaine-ski/shotgun', array());
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/events/dfsgjkdnv/shotgun', array('motivation' => 'miam'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testGetShotgun()
    {
        $this->client->request('GET', '/events/semaine-ski/shotgun');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $results = json_decode($response->getContent(), true);
        $this->assertNotEquals($results, null);
        $this->assertArrayHasKey('status', $results);
        $this->assertArrayHasKey('fail', $results);
        $this->assertArrayHasKey('limit', $results);
        $this->assertArrayHasKey('shotgunText', $results);
    }

    public function testDeleteShotgun()
    {
        $this->client->request('DELETE', '/events/semaine-ski/shotgun');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/events/semaine-ski/shotgun');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDeleteShotgunEvent()
    {
        $this->client->request('DELETE', '/events/semaine-ski');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);
    }
}
