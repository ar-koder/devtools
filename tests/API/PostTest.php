<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class PostTest extends ApiTestCase
{
    protected function setUp(): void
    {
        $user = new User();
        $user->setEmail("test@example.com");
        $user->setName("test user");
        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }

    protected function tearDown(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        $em->remove($user);
        $em->flush();
    }

    protected function createEntity() : array
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        $body = [
            "title" => "Post title",
            "body" => "Post content",
            "user" => sprintf("/api/users/%s", $user->getId())
        ];

        $response = static::createClient()->request('POST', '/api/posts', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains($body);
        $this->assertMatchesResourceItemJsonSchema(Post::class);
        return $response->toArray();
    }

    public function testCreate()
    {
        $this->createEntity();
    }

    public function testGet()
    {
        $data = $this->createEntity();
        static::createClient()->request('GET', sprintf('/api/posts/%s', $data['id']));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Post::class);
    }

    public function testPut()
    {
        $data = $this->createEntity();

        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        $body = [
            "title" => "PUT - Post title",
            "body" => "Post content",
            "user" => sprintf("/api/users/%s", $user->getId())
        ];

        static::createClient()->request('PUT', sprintf('/api/posts/%s', $data["id"]), [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Post::class);
    }


    public function testPatch()
    {
        $data = $this->createEntity();

        $body = [
            "title" => "PATCH - Post title"
        ];

        static::createClient()->request('PUT', sprintf('/api/posts/%s', $data["id"]), [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Post::class);

    }

    public function testDelete(): void
    {
        $data = $this->createEntity();
        static::createClient()->request('DELETE', sprintf('/api/posts/%s', $data['id']));
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testGetAll(): void
    {
        static::createClient()->request('GET', '/api/posts');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Post::class);
    }

    public function testGetByUser(): void
    {
        $this->createEntity();
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        static::createClient()->request('GET', sprintf('/api/users/%s/posts', $user->getId()));
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Post::class);
    }
}
