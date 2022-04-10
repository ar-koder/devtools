<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class CommentTest extends ApiTestCase
{
    protected function setUp(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setName("test user");
        $em->persist($user);

        $post = new Post();
        $post->setTitle("title");
        $post->setBody("body");
        $post->setUser($user);

        $em->persist($post);
        $em->flush();
    }

    protected function tearDown(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        $em->remove($user);

        $post = $em->getRepository(Post::class)->findOneByTitle("title");
        $em->remove($post);

        $em->flush();
    }

    protected function createEntity() : array
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $post = $em->getRepository(Post::class)->findOneByTitle("title");

        $body = [
            "title" => "title",
            "body" => "body",
            "email" => "example@example.com",
            "post" => sprintf("/api/posts/%s", $post->getId())
        ];

        $response = static::createClient()->request('POST', '/api/comments', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertMatchesResourceItemJsonSchema(Comment::class);

        return $response->toArray();
    }

    public function testCreate()
    {
        $this->createEntity();
    }

    public function testGet()
    {
        $data = $this->createEntity();
        static::createClient()->request('GET', sprintf('/api/comments/%s', $data['id']));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Comment::class);
    }

    public function testPut()
    {
        $data = $this->createEntity();

        $em = self::getContainer()->get('doctrine')->getManager();
        $post = $em->getRepository(Post::class)->findOneByTitle("title");
        $body = [
            "title" => "title",
            "body" => "body",
            "email" => "example@example.com",
            "post" => sprintf("/api/posts/%s", $post->getId())
        ];

        static::createClient()->request('PUT', sprintf('/api/comments/%s', $data["id"]), [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Comment::class);
    }


    public function testPatch()
    {
        $data = $this->createEntity();

        $body = [
            "title" => "title"
        ];

        static::createClient()->request('PUT', sprintf('/api/comments/%s', $data["id"]), [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Comment::class);

    }

    public function testDelete(): void
    {
        $data = $this->createEntity();
        static::createClient()->request('DELETE', sprintf('/api/comments/%s', $data['id']));
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testGetAll(): void
    {
        static::createClient()->request('GET', '/api/comments');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Comment::class);
    }

    public function testGetByPost(): void
    {
        $this->createEntity();
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        static::createClient()->request('GET', sprintf('/api/posts/%s/comments', $user->getId()));
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Comment::class);
    }
}
