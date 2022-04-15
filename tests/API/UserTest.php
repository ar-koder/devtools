<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Album;
use App\Entity\Post;
use App\Entity\Todo;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends ApiTestCase
{
    protected function setUp(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('test user');
        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }

    protected function tearDown(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail('test@example.com');
        $em->remove($user);
        $em->flush();
    }

    public function testGet()
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail('test@example.com');

        static::createClient()->request('GET', sprintf('/api/users/%s', $user->getId()));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testGetAll(): void
    {
        static::createClient()->request('GET', '/api/users');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testAlbumsByUser(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail('test@example.com');

        static::createClient()->request('GET', sprintf('/api/users/%s/albums', $user->getId()));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Album::class);
    }

    public function testPostsByUser(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail('test@example.com');

        static::createClient()->request('GET', sprintf('/api/users/%s/posts', $user->getId()));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Post::class);
    }

    public function testTodosByUser(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail('test@example.com');

        static::createClient()->request('GET', sprintf('/api/users/%s/todos', $user->getId()));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Todo::class);
    }
}
