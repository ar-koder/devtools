<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class StatusCodeTest extends ApiTestCase
{
    protected function requestTest($method, $code)
    {
        if (!array_key_exists($code, Response::$statusTexts)) {
            return;
        }

        static::createClient()->request($method, sprintf('/status/%s', $code));
        $this->assertResponseStatusCodeSame($code);
    }

    public function testGet()
    {
        foreach ([Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR] as $code) {
            $this->requestTest('GET', $code);
        }
    }

    public function testPost()
    {
        foreach ([Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR] as $code) {
            $this->requestTest('POST', $code);
        }
    }

    public function testPut()
    {
        foreach ([Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR] as $code) {
            $this->requestTest('PUT', $code);
        }
    }

    public function testPatch()
    {
        foreach ([Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR] as $code) {
            $this->requestTest('PATCH', $code);
        }
    }

    public function testDelete()
    {
        foreach ([Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT, Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR] as $code) {
            $this->requestTest('DELETE', $code);
        }
    }
}
