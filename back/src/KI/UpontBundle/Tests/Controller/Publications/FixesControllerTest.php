<?php

namespace KI\UpontBundle\Tests\Controller\Publications;

use KI\UpontBundle\Tests\WebTestCase;

class FixesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    public function testPost()
    {
        $this->client->request(
            'POST',
            '/fixes',
            array(
                'name' => 'Panne d\'Internet',
                'problem' => 'J\'arrive pas à avoir Internet, duuh',
                'answer' => 'T\'as bien réglé ton proxy ?',
                'date' => 424283,
                'status' => 'En attente',
                'category' => 'Accès à Internet'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 201);
        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue($response->headers->has('Location'), $response->headers);
    }

    public function testGet()
    {
        $this->client->request('GET', '/fixes');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/fixes/panne-d-internet');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/fixes/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/fixes/panne-d-internet',
            array('solved' => 4242055, 'status' => 'Résolu !')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/fixes/panne-d-internet', array('name' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/fixes/sjoajsiosbsksaba7', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('PATCH', '/fixes/panne-d-internet', array('name' => 'miam', 'mail' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }



    // Tests relatifs aux utilisateurs liés à la tâche

    public function testLink()
    {
        $this->client->request('POST', '/fixes/panne-d-internet/respos/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/fixes/panne-d-internet/respos/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/fixes/panne-d-internet/respos/qmgjreijg');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('GET', '/fixes/panne-d-internet/respos');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testUnlink()
    {
        $this->client->request('DELETE', '/fixes/panne-d-internet/respos/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/fixes/panne-d-internet/respos/trancara');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/fixes/panne-d-internet');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/fixes/panne-d-internet');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
