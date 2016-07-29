<?php

namespace Tests\KI\UserBundle\Controller;

use Tests\KI\CoreBundle\WebTestCase;

class OwnControllerTest extends WebTestCase
{
    public function testGetAchievements()
    {
        $this->client->request('GET', '/own/achievements');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $infos = json_decode($response->getContent(), true);
        $this->assertNotEquals($infos, null);
        $this->assertArrayHasKey('unlocked', $infos);
        $this->assertArrayHasKey('locked', $infos);
        $this->assertArrayHasKey('points', $infos);
        $this->assertArrayHasKey('current_level', $infos);
        $this->assertNotEquals($infos['current_level'], null);
        $this->assertArrayHasKey('name', $infos['current_level']);
        $this->assertArrayHasKey('description', $infos['current_level']);
        $this->assertArrayHasKey('points', $infos['current_level']);
        $this->assertArrayHasKey('image', $infos['current_level']);
    }

    public function testGetCourses()
    {
        $this->client->request('GET', '/own/courses');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/own/courseitems');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGetNotifications()
    {
        $this->client->request('GET', '/own/notifications');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGetFollowed()
    {
        $this->client->request('GET', '/own/followed');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGetEvents()
    {
        $this->client->request('GET', '/own/events');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGetFixs()
    {
        $this->client->request('GET', '/own/fixs');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGetNewsItems()
    {
        $this->client->request('GET', '/own/newsitems');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('GET', '/own/newsitems?limit=20&page=2');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testGetPreferences()
    {
        $this->client->request('GET', '/own/preferences');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testAddPreferences()
    {
        $this->client->request('PATCH', '/own/preferences', ['key' => 'lmlqkjflnimpquoi', 'value'=>'ok']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('PATCH', '/own/preferences', ['key' => 'notif_followed_event', 'value'=>'3']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('GET', '/own/preferences');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testRemovePreferences()
    {
        $this->client->request('DELETE', '/own/preferences', ['key' => 'lmlqkjflnimpquoi']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 400);

        $this->client->request('DELETE', '/own/preferences', ['key' => 'notif_followed_event']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('GET', '/own/preferences');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testToken()
    {
        $this->client->request('GET', '/own/token');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    public function testDeviceRegistration()
    {
        $this->client->request('POST', '/own/devices', ['device' => 'sjoajsiohaysahais-asbsksaba7', 'type' => 'iOS']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('POST', '/own/devices', ['device' => 'sjoajsiohaysahais-asbsksaba7', 'type' => 'iOS']);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('GET', '/own/devices');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->client->request('DELETE', '/own/devices/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 204);

        $this->client->request('DELETE', '/own/devices/sjoajsiohaysahais-asbsksaba7');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 404);
    }
}
