<?php

namespace KI\UpontBundle\Tests\Services;

use KI\UpontBundle\Tests\WebTestCase;
use KI\UpontBundle\EventListener\LogListener;

class KILogsTest extends WebTestCase
{
    protected $container;
    protected $service;

    public function __construct()
    {
        parent::__construct();
        $this->container = static::$kernel->getContainer();
        $this->service = $this->container->get('ki_upont.log');
    }

    public function testUserAgent()
    {
        // On teste quelques user agent de base
        $agents = array(
            array(
                'agent'   => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0',
                'system'  => 'Linux',
                'browser' => 'Firefox'
            ),
            array(
                'agent'   => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.56 Safari/536.5',
                'system'  => 'Windows 7',
                'browser' => 'Chrome'
            ),
            array(
                'agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
                'system'  => 'MacOS X',
                'browser' => 'Safari'
            ),
            array(
                'agent'   => 'Opera/9.80 (Windows NT 5.1; U; en) Presto/2.10.229 Version/11.60',
                'system'  => 'Windows XP',
                'browser' => 'Opera'
            ),
            array(
                'agent'   => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
                'system'  => 'Windows 7',
                'browser' => 'Internet Explorer'
            )
        );

        foreach ($agents as $agent) {
            $this->assertEquals($this->service->systemUserAgent($agent['agent']), $agent['system']);
            $this->assertEquals($this->service->browserUserAgent($agent['agent']), $agent['browser']);
        }
    }

    public function testLog()
    {
        // On effectue une requête quelconque
        $this->client->request(
            'PATCH',
            '/users/tdsdsssddssdds',
            array(
                'firstName' => 'KIMiam',
                'gender' => 'M',
                'phone' => '06.45.03.69.58'
            ),
            array(),
            array('User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0')
        );

        // On va chercher la ligne insérée
        $repo = $this->container->get('doctrine.orm.entity_manager')->getRepository('KIUpontBundle:Core\Log');
        $qb = $repo->createQueryBuilder('l');
        $qb->orderBy('l.date', 'DESC')->setMaxResults(1);
        /*$log = $qb->getQuery()->getSingleResult();

        // La ligne insérée doit correspondre à l'objet log suivant :
        // TODO
        $session = $this->container->get('security.context')->getToken();
        if (method_exists($session, 'getUser')) {
            $this->assertEquals(
                $log->getUser(),
                $session->getUser()
            );
        }
        $this->assertEquals(
            $log->getMethod(),
            'PATCH'
        );
        $this->assertEquals(
            $log->getUrl(),
            '/users/testificate'
        );
        $this->assertEquals(
            $log->getParams(),
            serialize(array('firstName' => 'KIMiam', 'gender' => 'M', 'phone' => '06.45.03.69.58'))
        );
        $this->assertEquals(
            $log->getCode(),
            404
        );
        $this->assertEquals(
            $log->getIp(),
            '127.0.0.1'
        );
        $this->assertEquals(
            $log->getBrowser(),
            'Autre'
        );
        $this->assertEquals(
            $log->getSystem(),
            'Autre'
        );
        $this->assertEquals(
            $log->getAgent(),
            'Symfony2 BrowserKit'
        );*/
    }
}
