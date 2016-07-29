<?php

namespace Tests\KI\UserBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class AdmissiblesControllerTest extends WebTestCase
{
    // On crée une ressource sur laquelle seront effectués les tests.
    // Ne pas oublier de supprimer à la fin avec le test DELETE.
    protected function postAdmissible()
    {
        $this->client->request(
            'POST',
            '/admissibles',
            array(
                'firstName' => 'Testy',
                'lastName' => 'Test',
                'contact' => 'testificate@phpunit.zorg',
                'scei' => 12345,
                'room' => 'simple',
                'serie' => 4,
                'details' => 'Admissible test'
            )
        );
        return $this->client->getResponse();
    }

    public function testPost()
    {
        $response = $this->postAdmissible();
        $this->assertJsonResponse($response, 201);

        // On vérifie que le lieu du nouvel objet a été indiqué
        $this->assertTrue(
            $response->headers->has('Location'),
            $response->headers
        );

        // On n'accepte pas les duplicatas selon le numéro SCEI
        $response = $this->postAdmissible();
        $this->assertJsonResponse($response, 400);

        $this->client->request('POST', '/admissibles', array('username' => '', 'email' => '123'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);
    }

    public function testGet()
    {
        $this->client->request('GET', '/admissibles');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/admissibles/12345-'.date('Y'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/admissibles/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testPatch()
    {
        $this->client->request(
            'PATCH',
            '/admissibles/12345-'.date('Y'),
            array(
                'firstName' => 'KImiam',
                'lastName' => 'OP',
                'room' => 'simple',
                'serie' => 4,
                'details' => 'Admissible test'
            )
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('PATCH', '/admissibles/12345-'.date('Y'), array('firstName' => ''));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/admissibles/sjoajsiohaysahais-asbsksaba7', array('username' => 'miam', 'email' => '123@mail.fr'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testDeleteFail()
    {
        $this->client->request('DELETE', '/admissibles/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);

        $this->client->request('DELETE', '/admissibles/12345-'.date('Y'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/admissibles/12345-'.date('Y'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
