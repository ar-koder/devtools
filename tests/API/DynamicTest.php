<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Uid\UuidV6;

class DynamicTest extends ApiTestCase
{

    public function testDecode()
    {
        $testString = "Hello World";
        $response = static::createClient()->request('GET', sprintf('/api/decode/%s', base64_encode($testString)),[
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertEquals(json_encode($testString, JSON_THROW_ON_ERROR), $response->getContent());
    }

    public function testEncode()
    {
        $testString = "Hello World";
        $response = static::createClient()->request('GET', sprintf('/api/encode/%s', $testString),[
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertEquals(json_encode(base64_encode($testString), JSON_THROW_ON_ERROR), $response->getContent());
    }

    public function testUUIDs()
    {
        $response = static::createClient()->request('GET', '/api/uuid',[
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertMatchesJsonSchema([
            "uuid_v4" => "string",
            "uuid_v6" => "string"
        ]);

        $data = $response->toArray();
        $this->assertInstanceOf(UuidV4::class, Uuid::fromString($data["uuid_v4"]));
        $this->assertInstanceOf(UuidV6::class, Uuid::fromString($data["uuid_v6"]));
    }
}
