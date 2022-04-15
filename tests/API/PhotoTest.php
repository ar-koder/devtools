<?php

namespace App\Tests\API;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Album;
use App\Entity\Photo;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class PhotoTest extends ApiTestCase
{
    protected function setUp(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setName("test user");
        $em->persist($user);

        $album = new Album();
        $album->setTitle("title");
        $album->setUser($user);

        $em->persist($album);
        $em->flush();
    }

    protected function tearDown(): void
    {
        $em = self::getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        $em->remove($user);

        $album = $em->getRepository(Album::class)->findOneByTitle("title");
        $em->remove($album);

        $em->flush();
    }

    protected function createEntity() : array
    {
        $em = self::getContainer()->get('doctrine')->getManager();
        $album = $em->getRepository(Album::class)->findOneByTitle("title");

        $body = [
            "title" => "title",
            "url" => "https://placeholders.arnaud-ritti.fr/api/placeholder/800x600.png",
            "thumbnailUrl" => "https://placeholders.arnaud-ritti.fr/api/placeholder/200x200.png?bgColor=%23FFF&textColor=%23000&text=Thumb",
            "album" => sprintf("/api/albums/%s", $album->getId())
        ];

        $response = static::createClient()->request('POST', '/api/photos', [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertMatchesResourceItemJsonSchema(Photo::class);

        return $response->toArray();
    }

    public function testCreate()
    {
        $this->createEntity();
    }

    public function testGet()
    {
        $data = $this->createEntity();
        static::createClient()->request('GET', sprintf('/api/photos/%s', $data['id']));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Photo::class);
    }

    public function testPut()
    {
        $data = $this->createEntity();

        $em = self::getContainer()->get('doctrine')->getManager();
        $album = $em->getRepository(Album::class)->findOneByTitle("title");
        $body = [
            "title" => "title",
            "url" => "https://placeholders.arnaud-ritti.fr/api/placeholder/800x600.png",
            "thumbnailUrl" => "https://placeholders.arnaud-ritti.fr/api/placeholder/200x200.png?bgColor=%23FFF&textColor=%23000&text=Thumb",
            "album" => sprintf("/api/albums/%s", $album->getId())
        ];

        static::createClient()->request('PUT', sprintf('/api/photos/%s', $data["id"]), [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Photo::class);
    }


    public function testPatch()
    {
        $data = $this->createEntity();

        $body = [
            "title" => "title"
        ];

        static::createClient()->request('PUT', sprintf('/api/photos/%s', $data["id"]), [
            'headers' => ['Content-Type' => 'application/json'],
            "json" => $body
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesResourceItemJsonSchema(Photo::class);

    }

    public function testDelete(): void
    {
        $data = $this->createEntity();
        static::createClient()->request('DELETE', sprintf('/api/photos/%s', $data['id']));
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testGetAll(): void
    {
        static::createClient()->request('GET', '/api/photos');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }

    public function testGetByAlbum(): void
    {
        $this->createEntity();
        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail("test@example.com");
        static::createClient()->request('GET', sprintf('/api/albums/%s/photos', $user->getId()));
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Photo::class);
    }
}
