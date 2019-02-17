<?php

namespace Tests\App\Services;

use App\Service\ImageService;
use App\Tests\WebTestCase;

class ImageServiceTest extends WebTestCase
{
    protected $service;
    protected $path;

    public function setUp()
    {
        parent::setUp();

        $this->service = self::$container->get('test.App\Service\ImageService');
        $this->path = self::$container->getParameter('ki_core.images.directory');
    }

    public function testUploadBase64()
    {
        $imgResult = $this->service->uploadFromBase64($this->base64);
        $this->assertTrue($imgResult['image'] !== null);
    }

    public function testExtUploadBase64()
    {
        $result = $this->service->uploadFromBase64($this->base64);
        $this->assertEquals($result['extension'], 'png');
    }

    public function testUploadUrl()
    {
        $url = 'https://scontent-mrs1-1.xx.fbcdn.net/v/t1.0-9/19275070_1361855813851179_815950941904439796_n.jpg?_nc_cat=109&_nc_ht=scontent-mrs1-1.xx&oh=9a3791cb16f00d64799a504b09e57116&oe=5CB3716F';
        $result = $this->service->uploadFromUrl($url);
        $this->assertTrue($result['image'] !== null);
        $this->assertEquals($result['extension'], 'jpeg');

        $url = 'https://ia.media-imdb.com/images/M/MV5BMTg2OTIwNTQ2OF5BMl5BanBnXkFtZTcwNTA4NDAwMQ@@._V1_SX300.jpg';
        $result = $this->service->uploadFromUrl($url);
        $this->assertTrue($result['image'] !== null);
        $this->assertEquals($result['extension'], 'jpeg');

        $url = 'http://akamai-b.cdn.cddbp.net/cds/2.0/cover/FCBA/FCB8/A360/ACE4_medium_front.jpg';
        $result = $this->service->uploadFromUrl($url);
        $this->assertTrue($result['image'] !== null);
        $this->assertEquals($result['extension'], 'jpeg');
    }

    public function testFailUploadUrl()
    {
        $this->expectException(\Exception::class);
        $url = 'httzpqq//wsqdqww.youtube.com/yt/brand/media/image/YouTube-logo-full_color.png';
        $result = $this->service->uploadFromUrl($url);
        $this->assertEquals($result, null);
    }

    protected $base64 = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASAQMAAAByySynAAAABlBMVEUAAAD///+l2Z/dAAAAP0lEQVQImWNgPm9gwAAmbM4bH4AQzAdAYiDC/rzxByTi/+f/cIL5AwPnZGYGIGHMwGA5mdkASNgbMNgJ80AIAMCSHqNvm2VtAAAAAElFTkSuQmCC';
}
