<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CookieTest extends ApiTestCase
{
    public function testCreate(){
        $body = [
            "key" => "freeform",
            "value" => "example"
        ];

        static::createClient()->request('POST', '/api/cookies', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHasCookie('freeform');
        $this->assertResponseCookieValueSame('freeform', "example");
    }

    public function testPatch(){
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie("freeform", "example"));

        $client->request('PATCH', sprintf('/api/cookies/%s', "freeform"), [
            'headers' => ['Content-Type' => 'application/json'],
            "body" => "edited-example"
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasCookie('freeform');
        $this->assertResponseCookieValueSame('freeform', "edited-example");
    }

    public function testDelete(){
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie("freeform", "example"));

        $response = $client->request('DELETE', sprintf('/api/cookies/%s', "freeform"), [
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasCookie('freeform');
        $this->assertSame(json_encode('The cookie with key freeform is deleted', JSON_THROW_ON_ERROR), $response->getContent());
    }

    public function testGet(){
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie("freeform", "example"));

        $client->request('GET', '/api/cookies', [
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertBrowserHasCookie('freeform');
        $this->assertBrowserCookieValueSame('freeform', "example");
        $this->assertJsonContains([
            "cookies" => [
                "freeform" => "example"
            ]
        ]);
    }
}
