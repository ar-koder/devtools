<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class RequestTest extends ApiTestCase
{

    public function testUA()
    {
        static::createClient()->request('GET', '/user-agent',[
            'headers' => [
                'User-Agent' => "UA-TEST"
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains([
            "user-agent" => "UA-TEST"
        ]);
    }

    public function testIPs()
    {
        static::createClient()->request('GET', '/ip',[
            'headers' => [
                'User-Agent' => "UA-TEST"
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains([
            "origins" => [
                "127.0.0.1"
            ]
        ]);
    }

    public function testHeaders(): void
    {
        static::createClient()->request('GET', '/headers',[
            'headers' => [
                'User-Agent' => "UA-TEST"
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains([
            "headers" => [
                'host' => "example.com",
                'user-agent' => "UA-TEST"
            ]
        ]);
    }

}
