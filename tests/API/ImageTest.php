<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Mime\MimeTypes;

class ImageTest extends ApiTestCase
{
    protected function getImage($width, $height, $format){
        $response = static::createClient()->request('GET', sprintf('/api/placeholder/%sx%s.%s', $width, $height, $format));

        $imageSize = getimagesizefromstring($response->getContent());
        $this->assertEquals($width, $imageSize[0]);
        $this->assertEquals($height, $imageSize[1]);
        $this->assertContains($imageSize["mime"], (new MimeTypes())->getMimeTypes($format));
    }

    protected function getSpaceImage($width, $height, $format, $category = null){
        if($category){
            $response = static::createClient()->request('GET', sprintf('/api/space/%s/%sx%s.%s', $category, $width, $height, $format));
        }else{
            $response = static::createClient()->request('GET', sprintf('/api/space/%sx%s.%s', $width, $height, $format));
        }

        $imageSize = getimagesizefromstring($response->getContent());
        $this->assertEquals($width, $imageSize[0]);
        $this->assertEquals($height, $imageSize[1]);
        $this->assertContains($imageSize["mime"], (new MimeTypes())->getMimeTypes($format));
    }

    public function testPlaceholderJpg(){
        $this->getImage(800, 600, "jpg");
        $this->getSpaceImage(800, 600, "jpg");
        $this->getSpaceImage(800, 600, "jpg", "movie");
    }

    public function testPlaceholderPng(){
        $this->getImage(800, 600, "png");
        $this->getSpaceImage(800, 600, "png");
        $this->getSpaceImage(800, 600, "png", "movie");
    }

    public function testPlaceholderGif(){
        $this->getImage(800, 600, "gif");
        $this->getSpaceImage(800, 600, "gif");
        $this->getSpaceImage(800, 600, "gif", "movie");
    }

    public function testPlaceholderWebp(){
        $this->getImage(800, 600, "webp");
        $this->getSpaceImage(800, 600, "webp");
        $this->getSpaceImage(800, 600, "webp", "movie");
    }
}
