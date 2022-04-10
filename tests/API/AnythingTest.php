<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class AnythingTest extends ApiTestCase
{
    protected function requestTest($method, $options){
        static::createClient()->request($method,'/api/anything', $options);

        $this->assertJsonContains([
            'method' => $method,
            'origins' => ["127.0.0.1"],
            'headers' => [
                "host" => "example.com",
                "user-agent" => "Symfony BrowserKit"
            ],
        ]);
    }

    public function testGet(){
        $this->requestTest('GET', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => ["key" => "value"]
        ]);
        $this->assertJsonContains([
            'headers' => [
                "content-type" => 'application/json',
            ],
            "content-type" => "json",
            "raw_body" => json_encode(["key" => "value"], JSON_THROW_ON_ERROR),
            "body" => ["key" => "value"]
        ]);
    }

    public function testPost()
    {
        $this->requestTest('POST', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => ["key" => "value"]
        ]);
        $this->assertJsonContains([
            'headers' => [
                "content-type" => 'application/json',
            ],
            "content-type" => "json",
            "raw_body" => json_encode(["key" => "value"], JSON_THROW_ON_ERROR),
            "body" => ["key" => "value"]
        ]);
    }

    public function testPut(){
        $this->requestTest('PUT', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => ["key" => "value"]
        ]);
        $this->assertJsonContains([
            'headers' => [
                "content-type" => 'application/json',
            ],
            "content-type" => "json",
            "raw_body" => json_encode(["key" => "value"], JSON_THROW_ON_ERROR),
            "body" => ["key" => "value"]
        ]);
    }

    public function testPatch(){
        $this->requestTest('PATCH', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => ["key" => "value"]
        ]);
        $this->assertJsonContains([
            'headers' => [
                "content-type" => 'application/json',
            ],
            "content-type" => "json",
            "raw_body" => json_encode(["key" => "value"], JSON_THROW_ON_ERROR),
            "body" => ["key" => "value"]
        ]);
    }

    public function testDelete(){
        $this->requestTest('DELETE', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => ["key" => "value"]
        ]);
        $this->assertJsonContains([
            'headers' => [
                "content-type" => 'application/json',
            ],
            "content-type" => "json",
            "raw_body" => json_encode(["key" => "value"], JSON_THROW_ON_ERROR),
            "body" => ["key" => "value"]
        ]);
    }
}
